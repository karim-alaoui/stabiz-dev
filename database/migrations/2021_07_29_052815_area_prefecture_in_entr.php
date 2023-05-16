<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AreaPrefectureInEntr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entrepreneur_profiles', function (Blueprint $table) {
            $table->unsignedSmallInteger('area_id')->nullable();
            $table->unsignedSmallInteger('prefecture_id')->nullable();
            $table->foreign('area_id')
                ->references('id')
                ->on('areas');
            $table->foreign('prefecture_id')
                ->references('id')
                ->on('prefectures');
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
