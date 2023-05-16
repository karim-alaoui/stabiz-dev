<?php

namespace Database\Seeders;

use App\Models\IncomeRange;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncomeRangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('TRUNCATE income_ranges restart identity CASCADE');
        IncomeRange::create([
            'lower_limit' => null,
            'upper_limit' => 500,
            'is_lowest_limit' => true,
            'unit' => 'ten thousand',
            'currency' => 'jpy'
        ]);

        for ($i = 501; $i <= 1500; $i += 100) {
            $data = [
                'lower_limit' => $i,
                'upper_limit' => $i + 99
            ];

            $found = IncomeRange::query()->where($data)->first();
            $more = [
                'unit' => 'ten thousand',
                'currency' => 'jpy'
            ];
            if (!$found) {
                $data = array_merge($data, $more);
                IncomeRange::query()->create($data);
            }
        }

        IncomeRange::create([
            'lower_limit' => 1501,
            'upper_limit' => null,
            'is_highest_limit' => true,
            'unit' => 'ten thousand',
            'currency' => 'jpy'
        ]);
    }
}
