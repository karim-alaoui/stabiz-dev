<?php

namespace Database\Factories;

use App\Models\Staff;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VideoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Video::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => Str::random(),
            'description' => Str::random(),
            'link' => 'https://youtube.com',
            'staff_id' => Staff::first()->id ?? null
        ];
    }
}
