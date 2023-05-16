<?php

namespace Database\Factories;

use App\Models\EmailTemplate;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmailTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'subject' => Str::random(random_int(20, 50)),
            'body' => Str::random(),
            'comment' => Str::random(),
        ];
    }
}
