<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntrPfdOccupationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entr_pfd_occupations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entrepreneur_profile_id');
            $table->unsignedBigInteger('occupation_id');
            // Add any additional columns you need
            $table->timestamps();

            $table->foreign('entrepreneur_profile_id')->references('id')->on('entrepreneur_profiles')->onDelete('cascade');
            $table->foreign('occupation_id')->references('id')->on('occupations')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entr_pfd_occupations');
    }
}
