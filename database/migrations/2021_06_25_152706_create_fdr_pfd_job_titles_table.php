<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFdrPfdJobTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fdr_pfd_job_titles', function (Blueprint $table) {
            $table->foreignId('founder_profile_id')
                ->constrained('founder_profiles')
                ->onDelete('cascade');
            $table->unsignedSmallInteger('job_title_id');
            $table->foreign('job_title_id')
                ->references('id')
                ->on('job_titles')
                ->onDelete('cascade');
            $table->primary(['founder_profile_id', 'job_title_id']);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fdr_pfd_job_titles');
    }
}
