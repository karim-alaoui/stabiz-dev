<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        // make sure that it's never run on any production env by accident
        if (App::environment('production')) {
            throw new Exception('Can not run seeder in production');
        }
        /**
         * Boot up database with data
         *
         * when we go into a new database, we will need to run these seeders
         * for the application to function.
         * add the seeders that will be needed to fill data in the database here.
         * The seeders are usually common information like areas, prefectures, incomes etc etc
         * We will uncomment it to run and then comment.
         */
        $this->call([
            EducationBackgroundSeeder::class,
            WorkingStatusSeeder::class,
            LangLevelSeeder::class,
            PositionSeeder::class,
            PresentPostSeeder::class, // keep it behind position
            IncomeRangeSeeder::class,
            LangSeeder::class,
            RolesPermissionSeeder::class,
            IndustrySeeder::class,
            AreaSeeder::class,
            OccupationSeeder::class,
            EmailTemplateSeeder::class,
            Category4ArticleSeeder::class,
            MgmtExpSeeder::class,
            PackageSeeder::class,
            AreaSeeder2::class
        ]);
    }
}
