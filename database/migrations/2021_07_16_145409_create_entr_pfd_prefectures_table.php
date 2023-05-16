<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntrPfdPrefecturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entr_pfd_prefectures', function (Blueprint $table) {
            $table->foreignId('entrepreneur_profile_id')
                ->constrained((new \App\Models\EntrepreneurProfile())->getTable())
                ->onDelete('cascade');
            $table->unsignedSmallInteger('prefecture_id');
            $table->foreign('prefecture_id')
                ->references('id')
                ->on('prefectures')
                ->onDelete('cascade');
            $table->primary(['entrepreneur_profile_id', 'prefecture_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entr_pfd_prefectures');
    }
}
