<?php

namespace Database\Seeders;

use App\Models\Prefecture;
use Illuminate\Database\Seeder;

class PrefectureSeeder2 extends Seeder
{
    /**
     * Delete this prefecture
     *
     * @return void
     */
    public function run()
    {
        $names = ['沖縄'];
        foreach ($names as $name) {
            Prefecture::where('name_ja', $name)->delete();
        }
    }
}
