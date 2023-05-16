<?php

namespace Database\Factories;

use App\Models\AssignedUserToStaff;
use App\Models\EntrepreneurProfile;
use App\Models\FounderProfile;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AssignedUserToStaffFactory
 * @package Database\Factories
 */
class AssignedUserToStaffFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AssignedUserToStaff::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = $this->faker->randomElement([User::ENTR, User::FOUNDER]);
        return [
            'user_id' => User::factory()
                ->has(
                    $type == User::ENTR ? EntrepreneurProfile::factory() : FounderProfile::factory(),
                    $type == User::ENTR ? 'entrProfile' : 'fdrProfile'
                )
                ->create(['type' => $type]),
            'staff_id' => Staff::factory(),
        ];
    }
}
