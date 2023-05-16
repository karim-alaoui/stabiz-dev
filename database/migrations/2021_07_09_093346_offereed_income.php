<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OffereedIncome extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('founder_profiles', function (Blueprint $table) {
            $table->unsignedSmallInteger('offered_income_range_id')->nullable();
            $table->foreign('offered_income_range_id')
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
        Schema::table('founder_profiles', function (Blueprint $table) {
            //
        });
    }
}
