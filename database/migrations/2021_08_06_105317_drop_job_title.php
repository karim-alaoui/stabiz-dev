<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DropJobTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('drop table if exists job_titles cascade');
        DB::statement('drop table if exists fdr_pfd_job_titles cascade');
        DB::statement('drop table if exists entr_pfd_job_titles cascade');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
