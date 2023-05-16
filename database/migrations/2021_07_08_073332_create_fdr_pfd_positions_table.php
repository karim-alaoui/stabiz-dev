<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFdrPfdPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fdr_pfd_positions', function (Blueprint $table) {
            $table->foreignId('founder_profile_id')
                ->constrained()
                ->onDelete('cascade');
            $table->unsignedSmallInteger('position_id');
            $table->foreign('position_id')
                ->references('id')
                ->on((new \App\Models\Occupation())->getTable())
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fdr_pfd_positions');
    }
}
