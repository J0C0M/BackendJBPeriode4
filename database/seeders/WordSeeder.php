<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Word;

class WordSeeder extends Seeder
{
    public function run(): void
    {
        $words = [
            // Easy words
            ['word' => 'ABOUT', 'difficulty' => 'easy'],
            ['word' => 'ABOVE', 'difficulty' => 'easy'],
            ['word' => 'AFTER', 'difficulty' => 'easy'],
            ['word' => 'AGAIN', 'difficulty' => 'easy'],
            ['word' => 'BRING', 'difficulty' => 'easy'],
            ['word' => 'CLOSE', 'difficulty' => 'easy'],
            ['word' => 'FIRST', 'difficulty' => 'easy'],
            ['word' => 'GREAT', 'difficulty' => 'easy'],
            ['word' => 'HOUSE', 'difficulty' => 'easy'],
            ['word' => 'LIGHT', 'difficulty' => 'easy'],

            // Medium words
            ['word' => 'FRAME', 'difficulty' => 'medium'],
            ['word' => 'PLANT', 'difficulty' => 'medium'],
            ['word' => 'QUEST', 'difficulty' => 'medium'],
            ['word' => 'ROYAL', 'difficulty' => 'medium'],
            ['word' => 'SMILE', 'difficulty' => 'medium'],
            ['word' => 'TRAIN', 'difficulty' => 'medium'],
            ['word' => 'VOICE', 'difficulty' => 'medium'],
            ['word' => 'WORTH', 'difficulty' => 'medium'],
            ['word' => 'YOUTH', 'difficulty' => 'medium'],
            ['word' => 'BEACH', 'difficulty' => 'medium'],

            // Hard words
            ['word' => 'CYNIC', 'difficulty' => 'hard'],
            ['word' => 'EPOXY', 'difficulty' => 'hard'],
            ['word' => 'FJORD', 'difficulty' => 'hard'],
            ['word' => 'GLYPH', 'difficulty' => 'hard'],
            ['word' => 'LYMPH', 'difficulty' => 'hard'],
            ['word' => 'NYMPH', 'difficulty' => 'hard'],
            ['word' => 'PROXY', 'difficulty' => 'hard'],
            ['word' => 'QUIRK', 'difficulty' => 'hard'],
            ['word' => 'WALTZ', 'difficulty' => 'hard'],
            ['word' => 'ZESTY', 'difficulty' => 'hard'],
        ];

        foreach ($words as $wordData) {
            Word::create($wordData);
        }
    }
}
