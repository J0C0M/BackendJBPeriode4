<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Word;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class WordManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Add admin middleware when implemented
        // $this->middleware('admin');
    }

    /**
     * Display a listing of words with filtering and pagination
     */
    public function index(Request $request)
    {
        $query = Word::query();

        // Apply filters
        if ($request->filled('search')) {
            $query->where('word', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('status')) {
            $is_active = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $is_active);
        }

        // Get words with game count
        $words = $query->withCount('games')
            ->orderBy($request->get('sort', 'word'), $request->get('direction', 'asc'))
            ->paginate(20)
            ->appends($request->query());

        // Get statistics
        $stats = [
            'total' => Word::count(),
            'active' => Word::where('is_active', true)->count(),
            'inactive' => Word::where('is_active', false)->count(),
            'easy' => Word::where('difficulty', 'easy')->where('is_active', true)->count(),
            'medium' => Word::where('difficulty', 'medium')->where('is_active', true)->count(),
            'hard' => Word::where('difficulty', 'hard')->where('is_active', true)->count(),
        ];

        return view('admin.words.index', compact('words', 'stats'));
    }

    /**
     * Show the form for creating a new word
     */
    public function create()
    {
        return view('admin.words.create');
    }

    /**
     * Store a newly created word in storage
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'word' => 'required|string|size:5|unique:words|regex:/^[A-Za-z]+$/',
            'difficulty' => 'required|in:easy,medium,hard',
            'is_active' => 'boolean',
        ], [
            'word.size' => 'The word must be exactly 5 letters long.',
            'word.unique' => 'This word already exists in the database.',
            'word.regex' => 'The word must contain only letters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $word = Word::create([
                'word' => strtoupper($request->word),
                'difficulty' => $request->difficulty,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('admin.words.index')
                ->with('success', 'Word "' . $word->word . '" created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create word: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified word
     */
    public function show(Word $word)
    {
        $word->load(['games' => function ($query) {
            $query->with(['player1', 'player2'])
                ->latest()
                ->take(10);
        }]);

        $gameStats = [
            'total_games' => $word->games()->count(),
            'completed_games' => $word->games()->where('status', 'completed')->count(),
            'win_rate' => 0,
            'average_attempts' => 0,
        ];

        // Calculate win rate and average attempts
        $completedGames = $word->games()
            ->where('status', 'completed')
            ->with('moves')
            ->get();

        if ($completedGames->count() > 0) {
            $totalWins = $completedGames->where('result', '!=', 'draw')->count();
            $gameStats['win_rate'] = round(($totalWins / $completedGames->count()) * 100, 2);

            $totalAttempts = $completedGames->sum(function ($game) {
                return $game->moves->count();
            });
            $gameStats['average_attempts'] = round($totalAttempts / $completedGames->count(), 2);
        }

        return view('admin.words.show', compact('word', 'gameStats'));
    }

    /**
     * Show the form for editing the specified word
     */
    public function edit(Word $word)
    {
        return view('admin.words.edit', compact('word'));
    }

    /**
     * Update the specified word in storage
     */
    public function update(Request $request, Word $word)
    {
        $validator = Validator::make($request->all(), [
            'word' => 'required|string|size:5|regex:/^[A-Za-z]+$/|unique:words,word,' . $word->id,
            'difficulty' => 'required|in:easy,medium,hard',
            'is_active' => 'boolean',
        ], [
            'word.size' => 'The word must be exactly 5 letters long.',
            'word.unique' => 'This word already exists in the database.',
            'word.regex' => 'The word must contain only letters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $word->update([
                'word' => strtoupper($request->word),
                'difficulty' => $request->difficulty,
                'is_active' => $request->boolean('is_active'),
            ]);

            return redirect()->route('admin.words.index')
                ->with('success', 'Word "' . $word->word . '" updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update word: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified word from storage
     */
    public function destroy(Word $word)
    {
        // Check if word is used in any games
        $gamesCount = $word->games()->count();

        if ($gamesCount > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete word "' . $word->word . '" because it is used in ' . $gamesCount . ' game(s). Consider deactivating it instead.');
        }

        try {
            $wordText = $word->word;
            $word->delete();

            return redirect()->route('admin.words.index')
                ->with('success', 'Word "' . $wordText . '" deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete word: ' . $e->getMessage());
        }
    }

    /**
     * Bulk import words from a file or text
     */
    public function bulkImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'import_type' => 'required|in:text,file',
            'words_text' => 'required_if:import_type,text|string',
            'words_file' => 'required_if:import_type,file|file|mimes:txt,csv',
            'difficulty' => 'required|in:easy,medium,hard',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $words = [];
        $difficulty = $request->difficulty;
        $isActive = $request->boolean('is_active', true);

        try {
            if ($request->import_type === 'text') {
                $words = explode("\n", trim($request->words_text));
            } else {
                $file = $request->file('words_file');
                $content = file_get_contents($file->getPathname());
                $words = explode("\n", trim($content));
            }

            // Clean and validate words
            $validWords = [];
            $errors = [];
            $duplicates = [];
            $line = 0;

            foreach ($words as $word) {
                $line++;
                $word = trim(strtoupper($word));

                if (empty($word)) continue;

                // Validate word format
                if (strlen($word) !== 5) {
                    $errors[] = "Line {$line}: '{$word}' is not 5 letters long";
                    continue;
                }

                if (!preg_match('/^[A-Z]+$/', $word)) {
                    $errors[] = "Line {$line}: '{$word}' contains invalid characters";
                    continue;
                }

                // Check for duplicates in database
                if (Word::where('word', $word)->exists()) {
                    $duplicates[] = $word;
                    continue;
                }

                // Check for duplicates in current batch
                if (in_array($word, array_column($validWords, 'word'))) {
                    $errors[] = "Line {$line}: '{$word}' is duplicated in the import";
                    continue;
                }

                $validWords[] = [
                    'word' => $word,
                    'difficulty' => $difficulty,
                    'is_active' => $isActive,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert valid words
            $inserted = 0;
            if (!empty($validWords)) {
                DB::table('words')->insert($validWords);
                $inserted = count($validWords);
            }

            $message = "Import completed: {$inserted} words added";
            if (!empty($duplicates)) {
                $message .= ", " . count($duplicates) . " duplicates skipped";
            }
            if (!empty($errors)) {
                $message .= ", " . count($errors) . " errors";
            }

            $type = (!empty($errors) || !empty($duplicates)) ? 'warning' : 'success';

            return redirect()->route('admin.words.index')
                ->with($type, $message)
                ->with('import_details', [
                    'inserted' => $inserted,
                    'duplicates' => $duplicates,
                    'errors' => $errors,
                ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Toggle word active status
     */
    public function toggleStatus(Word $word)
    {
        try {
            $word->update(['is_active' => !$word->is_active]);

            $status = $word->is_active ? 'activated' : 'deactivated';

            return redirect()->back()
                ->with('success', 'Word "' . $word->word . '" ' . $status . ' successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to toggle word status: ' . $e->getMessage());
        }
    }

    /**
     * Export words to CSV
     */
    public function export(Request $request)
    {
        $query = Word::query();

        // Apply same filters as index
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('status')) {
            $is_active = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $is_active);
        }

        $words = $query->withCount('games')->get();

        $filename = 'words_export_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($words) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Word', 'Difficulty', 'Status', 'Games Count', 'Created At']);

            foreach ($words as $word) {
                fputcsv($file, [
                    $word->word,
                    $word->difficulty,
                    $word->is_active ? 'Active' : 'Inactive',
                    $word->games_count,
                    $word->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
