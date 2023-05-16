<?php

namespace Database\Seeders;

use App\Models\WorkingStatus;
use Illuminate\Database\Seeder;

class WorkingStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $labelsEn = 'Working , Turing over, others';
        $labelsEn = explode(',', $labelsEn);
        foreach ($labelsEn as $item) {
            $background = WorkingStatus::where('label', trim($item))->first();
            if (!$background) {
                WorkingStatus::create([
                    'label' => trim($item)
                ]);
            }
        }
    }
}
