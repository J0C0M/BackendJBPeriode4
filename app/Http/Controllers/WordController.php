<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class WordController extends Controller
{
    public function __construct()
    {
        // Add admin middleware if you have it
        // $this->middleware('admin');
    }

    /**
     * Display a listing of words
     */
    public function index(Request $request)
    {
        $query = Word::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('word', 'like', '%' . $request->search . '%');
        }

        // Filter by difficulty
        if ($request->has('difficulty') && $request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        // Filter by active status
        if ($request->has('active') && $request->active !== '') {
            $query->where('is_active', (bool) $request->active);
        }

        $words = $query->orderBy('word')
            ->paginate(20)
            ->withQueryString();

        $difficulties = ['easy', 'medium', 'hard'];
        $totalWords = Word::count();
        $activeWords = Word::where('is_active', true)->count();

        return view('admin.words.index', compact('words', 'difficulties', 'totalWords', 'activeWords'));
    }

    /**
     * Show the form for creating a new word
     */
    public function create()
    {
        $difficulties = ['easy', 'medium', 'hard'];
        return view('admin.words.create', compact('difficulties'));
    }

    /**
     * Store a newly created word
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'word' => 'required|string|size:5|alpha|unique:words,word',
            'difficulty' => 'required|in:easy,medium,hard',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Word::create([
            'word' => strtoupper($request->word),
            'difficulty' => $request->difficulty,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('words.index')
            ->with('success', 'Word added successfully!');
    }

    /**
     * Display the specified word
     */
    public function show(Word $word)
    {
        $gamesPlayed = $word->games()->count();
        $gamesWon = $word->games()->whereNotNull('winner_id')->count();
        $averageAttempts = $word->games()
            ->whereNotNull('winner_id')
            ->with('moves')
            ->get()
            ->avg(function ($game) {
                return $game->moves->where('guessed_word', $game->word->word)->first()?->attempt_number ?? 0;
            });

        return view('admin.words.show', compact('word', 'gamesPlayed', 'gamesWon', 'averageAttempts'));
    }

    /**
     * Show the form for editing the specified word
     */
    public function edit(Word $word)
    {
        $difficulties = ['easy', 'medium', 'hard'];
        return view('admin.words.edit', compact('word', 'difficulties'));
    }

    /**
     * Update the specified word
     */
    public function update(Request $request, Word $word)
    {
        $validator = Validator::make($request->all(), [
            'word' => 'required|string|size:5|alpha|unique:words,word,' . $word->id,
            'difficulty' => 'required|in:easy,medium,hard',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $word->update([
            'word' => strtoupper($request->word),
            'difficulty' => $request->difficulty,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('words.index')
            ->with('success', 'Word updated successfully!');
    }

    /**
     * Remove the specified word
     */
    public function destroy(Word $word)
    {
        // Check if word is used in any games
        if ($word->games()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete word that has been used in games.');
        }

        $word->delete();

        return redirect()->route('words.index')
            ->with('success', 'Word deleted successfully!');
    }

    /**
     * Toggle word active status
     */
    public function toggleActive(Word $word)
    {
        $word->update([
            'is_active' => !$word->is_active
        ]);

        $status = $word->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "Word {$status} successfully!",
            'is_active' => $word->is_active
        ]);
    }

    /**
     * Bulk import words from CSV
     */
    public function bulkImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('csv_file');
        $difficulty = $request->difficulty;

        $words = [];
        $duplicates = [];
        $invalid = [];

        if (($handle = fopen($file->getPathname(), 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $word = strtoupper(trim($data[0]));

                // Validate word
                if (strlen($word) !== 5 || !ctype_alpha($word)) {
                    $invalid[] = $word;
                    continue;
                }

                // Check for duplicates in database
                if (Word::where('word', $word)->exists()) {
                    $duplicates[] = $word;
                    continue;
                }

                $words[] = [
                    'word' => $word,
                    'difficulty' => $difficulty,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            fclose($handle);
        }

        // Insert valid words
        if (!empty($words)) {
            Word::insert($words);
        }

        $message = count($words) . ' words imported successfully!';
        if (!empty($duplicates)) {
            $message .= ' ' . count($duplicates) . ' duplicates skipped.';
        }
        if (!empty($invalid)) {
            $message .= ' ' . count($invalid) . ' invalid words skipped.';
        }

        return redirect()->route('words.index')
            ->with('success', $message);
    }

    /**
     * Export words to CSV
     */
    public function export(Request $request)
    {
        $query = Word::query();

        // Apply filters
        if ($request->has('difficulty') && $request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->has('active') && $request->active !== '') {
            $query->where('is_active', (bool) $request->active);
        }

        $words = $query->orderBy('word')->get();

        $filename = 'words_' . date('Y-m-d_H-i-s') . '.csv';
        $path = storage_path('app/temp/' . $filename);

        // Create temp directory if it doesn't exist
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        // Add header
        fputcsv($file, ['Word', 'Difficulty', 'Active', 'Created At']);

        // Add data
        foreach ($words as $word) {
            fputcsv($file, [
                $word->word,
                $word->difficulty,
                $word->is_active ? 'Yes' : 'No',
                $word->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Get word statistics
     */
    public function statistics()
    {
        $stats = [
            'total_words' => Word::count(),
            'active_words' => Word::where('is_active', true)->count(),
            'by_difficulty' => Word::selectRaw('difficulty, COUNT(*) as count')
                ->groupBy('difficulty')
                ->pluck('count', 'difficulty')
                ->toArray(),
            'games_played' => Word::withCount('games')->get()->sum('games_count'),
            'most_used' => Word::withCount('games')
                ->orderBy('games_count', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Get random word for testing
     */
    public function random(Request $request)
    {
        $difficulty = $request->get('difficulty');
        $word = Word::getRandomWord($difficulty);

        if (!$word) {
            return response()->json([
                'success' => false,
                'message' => 'No words available for the specified difficulty.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'word' => $word->word,
            'difficulty' => $word->difficulty,
        ]);
    }

    /**
     * Validate if a word exists in the database
     */
    public function validateWord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'word' => 'required|string|size:5|alpha',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Invalid word format.'
            ]);
        }

        $word = strtoupper($request->word);
        $exists = Word::where('word', $word)->where('is_active', true)->exists();

        return response()->json([
            'success' => true,
            'valid' => $exists,
            'word' => $word,
        ]);
    }
}
