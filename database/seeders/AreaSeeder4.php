<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Prefecture;
use Illuminate\Database\Seeder;

class AreaSeeder4 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prefecture = Prefecture::where('name_ja', '沖縄県')->first();
        if ($prefecture) {
            $area = '沖縄';
            $area = Area::where('name_ja', $area)->first();
            if ($area) {
                $prefecture->area_id = $area->id;
                $prefecture->save();
            }
        }
    }
}
