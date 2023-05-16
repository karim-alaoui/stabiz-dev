<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LangInEntProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entrepreneur_profiles', function (Blueprint $table) {
            $table->unsignedMediumInteger('lang_id')->nullable();
            $table->foreign('lang_id')
                ->references('id')
                ->on('languages');
            $table->unsignedTinyInteger('lang_level_id')
                ->comment('foreign key to lang level table. This value refers to the language level of lang value of lang id column')
                ->nullable();
            $table->foreign('lang_level_id')
                ->references('id')
                ->on('lang_levels');
            $table->unsignedTinyInteger('en_lang_level_id')
                ->comment('foreign key to lang level table. This means english language level of the entrepreneur')
                ->nullable();
            $table->foreign('en_lang_level_id')
                ->references('id')
                ->on('lang_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entrepreneur_profiles', function (Blueprint $table) {
            //
        });
    }
}
