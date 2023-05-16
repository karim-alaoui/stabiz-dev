<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateEntrIndustriesTable
 */
class CreateEntrIndustriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entr_exp_industries', function (Blueprint $table) {
            $table->foreignId('entrepreneur_profile_id')
                ->constrained((new \App\Models\EntrepreneurProfile())->getTable())
                ->onDelete('cascade');
            $table->unsignedSmallInteger('industry_id');
            $table->foreign('industry_id')
                ->references('id')
                ->on((new \App\Models\Industry())->getTable())
                ->onDelete('cascade');
            $table->primary(['entrepreneur_profile_id', 'industry_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entr_exp_industries');
    }
}
