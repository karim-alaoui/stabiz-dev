<?php

namespace Database\Seeders;

use App\Models\Prefecture;
use Illuminate\Database\Seeder;

class MorePrefectureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prefectures = ['その他', '海外'];
        array_map(function ($prefecture) {
            $exists = Prefecture::where('name_ja', $prefecture)->first();
            if (!$exists) {
                Prefecture::create([
                    'name_ja' => $prefecture
                ]);
            }
        }, $prefectures);
    }
}
