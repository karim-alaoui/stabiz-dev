<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class OccupationSeeder
 * @package Database\Seeders
 */
class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('TRUNCATE positions restart identity cascade');
        $positions = '経営者,取締役,執行役員,管理職,顧問,役職無し,その他';
        $positions = explode(',', $positions);
        array_map(function ($position) {
            $position = trim($position);
            $exists = Position::query()->where('name', $position)->first();
            if (!$exists) {
                Position::create(['name' => $position]);
            }
        }, $positions);
    }
}
