<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntrPfdAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entr_pfd_areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entrepreneur_profile_id');
            $table->unsignedBigInteger('area_id');
            // Add any additional columns you need
            $table->timestamps();

            $table->foreign('entrepreneur_profile_id')->references('id')->on('entrepreneur_profiles')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entr_pfd_areas');
    }
}
