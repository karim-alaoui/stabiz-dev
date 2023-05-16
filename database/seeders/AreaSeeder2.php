<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Prefecture;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

/**
 * Class AreaSeeder2
 * @package Database\Seeders
 */
class AreaSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newAreas = [
            ['name' => '北海道', 'prefectures' => ['北海道']],
            ['name' => '沖縄', 'prefectures' => ['沖縄']],
            ['name' => '北陸甲信越', 'prefectures' => ['新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県']],
            ['name' => '東海', 'prefectures' => ['岐阜県', '静岡県', '愛知県', '三重県']],
            ['name' => '中国・四国', 'prefectures' => ['鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県', '香川県', '愛媛県', '高知県']],
            ['name' => '九州', 'prefectures' => ['福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県']]
        ];

        foreach ($newAreas as $newArea) {
            $name = Arr::get($newArea, 'name');
            if (!$name) continue;
            $area = Area::query()->withTrashed()->where('name_ja', $name)->first();
            if ($area) {
                $area->deleted_at = null;
                $area->save();
            } else {
                $area = Area::create(['name_ja' => $name]);
            }

            $prefectures = Arr::get($newArea, 'prefectures', []);
            array_map(function ($name) use ($area) {
                $prefecture = Prefecture::query()->withTrashed()->where('name_ja', $name)->first();
                if ($prefecture) {
                    $prefecture->deleted_at = null;
                    $prefecture->area_id = $area->id;
                    $prefecture->save();
                } else {
                    Prefecture::create(['name_ja' => $name, 'area_id' => $area->id]);
                }
            }, $prefectures);
        }
    }
}
