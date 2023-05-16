<?php

namespace Database\Seeders;

use App\Models\FdrPfdIndustry;
use App\Models\FounderProfile;
use App\Models\Industry;
use App\Models\Position;
use App\Models\Prefecture;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class FounderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment() == 'production') return null;

        DB::transaction(function () {
            $users = User::factory()
                ->count(20)
                ->create(['type' => User::FOUNDER]);

            $users->map(function ($user) {
                /**@var FounderProfile $founder */
                $founder = FounderProfile::factory()->create(['user_id' => $user->id]);
                $industries = Industry::query()->inRandomOrder()->take(3)->get();

                $industries->map(function ($industry) use ($founder) {
                    FdrPfdIndustry::factory()->create([
                        'industry_id' => $industry->id,
                        'founder_profile_id' => $founder->id
                    ]);
                });

                $prefectures = Prefecture::query()->inRandomOrder()->take(3)->get();
                $prefectures->map(function ($prefecture) use ($founder) {
                    $founder->pfdPrefectures()->create(['prefecture_id' => $prefecture->id]);
                });

                $positions = Position::query()->inRandomOrder()->take(3)->get();
                $positions->map(function ($position) use ($founder) {
                    $founder->pfdPositions()->create(['position_id' => $position->id]);
                });
            });
        });
    }
}
