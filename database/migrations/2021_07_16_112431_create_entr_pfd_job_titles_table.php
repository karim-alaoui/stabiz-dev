<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntrPfdJobTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entr_pfd_job_titles', function (Blueprint $table) {
            $table->foreignId('entrepreneur_profile_id')
                ->constrained((new \App\Models\EntrepreneurProfile())->getTable())
                ->onDelete('cascade');
            $table->unsignedSmallInteger('job_title_id');
            $table->foreign('job_title_id')
                ->references('id')
                ->on('job_titles')
                ->onDelete('cascade');
            $table->primary(['entrepreneur_profile_id', 'job_title_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entr_pfd_job_titles');
    }
}
