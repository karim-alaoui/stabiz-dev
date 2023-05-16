<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenamePositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('occupations', 'occupations2'); // we will rename the positions table to occupations
        // occupations table already exists
        Schema::rename('positions', 'occupations');
        Schema::rename('position_categories', 'occupation_categories');
        Schema::rename('occupations2', 'positions');
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
