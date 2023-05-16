<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CascadePositionFdrPosition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fdr_pfd_positions', function (Blueprint $table) {
            $table->dropForeign('fdr_pfd_positions_position_id_foreign');
            $table->foreign('position_id')
                ->references('id')
                ->on('positions')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fdr_pfd_positions', function (Blueprint $table) {
            //
        });
    }
}
