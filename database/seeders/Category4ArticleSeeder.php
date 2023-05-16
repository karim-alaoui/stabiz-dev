<?php

namespace Database\Seeders;

use App\Models\Category4Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class Category4ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i <= 10; $i++) {
            Category4Article::create(['name' => Str::random()]);
        }
    }
}
