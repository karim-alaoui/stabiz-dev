<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 11; $i++) {
            Language::create([
                'name' => Str::random(5),
            ]);
        }

        if (Language::where('name', 'other')->first()) return;
        Language::create([
            'name' => 'other',
        ]);
    }
}
