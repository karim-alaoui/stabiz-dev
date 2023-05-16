<?php

namespace Database\Factories;

use App\Models\Model;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Model::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => Str::random()
        ];
    }

    public function superadmin()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => Staff::SUPER_ADMIN_ROLE,
            ];
        });
    }

    public function matchmaker()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => Staff::MATCH_MAKER_ROLE,
            ];
        });
    }
}
