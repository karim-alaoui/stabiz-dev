<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeInExpectedIncomeInEntrProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('alter table entrepreneur_profiles drop constraint if exists entrepreneur_profiles_expected_income_range_id_foreign');
        Schema::table('entrepreneur_profiles', function (Blueprint $table) {
            $table->foreign('expected_income_range_id')
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
        Schema::table('entrepreneur_profiles', function (Blueprint $table) {
            //
        });
    }
}
