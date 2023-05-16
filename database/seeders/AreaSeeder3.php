<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Prefecture;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder3 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $removeAreas = ['東京', '中部'];
            Area::whereIn('name_ja', $removeAreas)->delete();

            /**@var Area $area */
            $area = Area::query()->where('name_ja', '関東')->first();
            Prefecture::query()->where('name_ja', '東京都')->forceDelete();
            $area->prefectures()->create(['name_ja' => '東京都']);

            /**@var Area $area2 */
            $area2 = Area::query()->where('name_ja', '九州')->first();
            if ($area2) {
                Prefecture::query()->where('name_ja', '沖縄県')->forceDelete();
                $area2->prefectures()->create(['name_ja' => '沖縄県']);
            }

            $sortOrder = [
                '関東',
                '関西',
                '北海道',
                '東北',
                '北陸甲信越',
                '東海',
                '中国・四国',
                '九州'
            ];

            array_walk($sortOrder, function ($val, $key) {
                $area = Area::query()->where('name_ja', $val)->first();
                if ($area) {
                    $area->sort_order = $key + 1;
                    $area->save();
                }
            });
        });
    }
}
