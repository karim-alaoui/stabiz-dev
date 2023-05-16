<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeInOfferedIncomeInFdrProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('alter table founder_profiles drop constraint if exists founder_profiles_offered_income_range_id_foreign');
        Schema::table('founder_profiles', function (Blueprint $table) {
            $table->foreign('offered_income_range_id')
                ->references('id')
                ->on('income_ranges')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('founder_profiles', function (Blueprint $table) {
            //
        });
    }
}
