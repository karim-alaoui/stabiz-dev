<?php

namespace Database\Seeders;

use App\Models\MgmtExp;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MgmtExpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $exps = [
            'None',
            'Less than 10 people',
            'More than 11 people ~ less than 20 people',
            'More than 21 people ~ less than 50 people',
            '51 people or more'
        ];

        DB::transaction(function () use ($exps) {
            array_map(function ($val) {
                $exp = MgmtExp::where('exp', 'ilike', $val)->first();
                if (!$exp) MgmtExp::create(['exp' => $val]);
            }, $exps);
        });
    }
}
