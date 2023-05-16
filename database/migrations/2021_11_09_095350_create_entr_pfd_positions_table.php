<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntrPfdPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entr_pfd_positions', function (Blueprint $table) {
            $table->foreignId('entrepreneur_profile_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('position_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->primary(['entrepreneur_profile_id', 'position_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entr_pfd_positions');
    }
}
