<?php

namespace Database\Seeders;

use App\Models\EducationBackground;
use App\Models\LangLevel;
use Illuminate\Database\Seeder;

class LangLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $labelsEn = 'None,Basic conversation level, Daily conversation level,Business level,Native level';
        $labelsEn = explode(',', $labelsEn);
        $labelJP = 'なし,基本会話レベル,日常会話レベル,ビジネスレベル,ネイティブレベル';
        $labelJP = explode(',', $labelJP);

        foreach ($labelsEn as $key => $item) {
            $label = trim($item);
            $background = LangLevel::where('level', trim($label))->first();
            if (!$background) {
                LangLevel::create([
                    'level' => $label,
                ]);
            }
        }
    }
}
