<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleIndustriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_industries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('industry_id');
            $table->foreign('industry_id')
                ->references('id')
                ->on('industries')
                ->cascadeOnDelete();
            $table->timestampTz('created_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_industries');
    }
}
