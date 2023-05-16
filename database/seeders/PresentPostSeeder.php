<?php

namespace Database\Seeders;

use App\Models\PresentPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresentPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            DB::statement('truncate present_posts restart identity cascade');
            $data = [
                'business owner',
                'director',
                'executive officer',
                'management',
                'adviser',
                'no job title',
                'otherwise'
            ];
            array_map(function ($val) {
                $post = PresentPost::where('name', $val)->first();
                if (!$post) PresentPost::create(['name' => $val]);
            }, $data);
        });
    }
}
