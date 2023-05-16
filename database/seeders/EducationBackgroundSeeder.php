<?php

namespace Database\Seeders;

use App\Models\EducationBackground;
use Illuminate\Database\Seeder;

/**
 * Class EducationBackgroundSeeder
 * @package Database\Seeders
 */
class EducationBackgroundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $labelsEn = 'Junior high school graduates, high school graduates, professional graduates, university graduates, university dropouts, graduate school dropouts, graduate school graduates';
        $labelsEn = explode(',', $labelsEn);
        $labelJP = '中卒,高卒,専門卒,大卒,大学中退,大学院中退,大学院卒';
        $labelJP = explode(',', $labelJP);
        foreach ($labelsEn as $key => $item) {
            $label = trim($item);
            $background = EducationBackground::where('label', $label)->first();
            $deleteThis = in_array($label, ['graduate school dropouts', 'university dropouts']);
            if (!$background) {
                EducationBackground::create([
                    'label' => $label,
                    'deleted_at' => $deleteThis ? now() : null
                ]);
            } elseif ($deleteThis) {
                $background->delete();
            }
        }
    }
}
