<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyIncomeRanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('income_ranges', function (Blueprint $table) {
            $table->unsignedBigInteger('lower_limit')->nullable()->change();
            $table->unsignedBigInteger('upper_limit')->nullable()->change();
            $table->boolean('is_lowest_limit')->nullable();
            $table->boolean('is_highest_limit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('income_ranges', function (Blueprint $table) {
            //
        });
    }
}
