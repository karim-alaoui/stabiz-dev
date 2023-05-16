<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DesiredDateEntFdrProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('founder_profiles', function (Blueprint $table) {
            $table->date('work_start_date_4_entr')
                ->nullable()
                ->comment('The day entrepreneurs start working for this founder');
        });

        Schema::table('entrepreneur_profiles', function (Blueprint $table) {
            $table->date('work_start_date')->nullable()
                ->comment('The day this entrepreneur would like to start working');
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
            $table->dropColumn(['work_start_date_4_entr']);
        });

        Schema::table('entrepreneur_profiles', function (Blueprint $table) {
            $table->dropColumn(['work_start_date']);
        });
    }
}
