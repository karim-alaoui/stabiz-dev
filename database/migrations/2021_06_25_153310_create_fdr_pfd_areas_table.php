<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFdrPfdAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fdr_pfd_prefectures', function (Blueprint $table) {
            $table->foreignId('founder_profile_id')
                ->constrained('founder_profiles')
                ->onDelete('cascade');
            $table->unsignedSmallInteger('prefecture_id');
            $table->foreign('prefecture_id')
                ->references('id')
                ->on('prefectures')
                ->onDelete('cascade');
            $table->primary(['founder_profile_id', 'prefecture_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fdr_pfd_prefectures');
    }
}
