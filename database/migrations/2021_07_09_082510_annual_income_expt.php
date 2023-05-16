<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AnnualIncomeExpt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entrepreneur_profiles', function (Blueprint $table) {
            $table->unsignedSmallInteger('expected_income_range_id')->nullable();
            $table->foreign('expected_income_range_id')
                ->references('id')
                ->on('income_ranges');
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
