<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleAudiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_audiences', function (Blueprint $table) {
            $table->foreignId('article_id')
                ->constrained()
                ->onDelete('cascade');
            $table->enum('audience', ['founder', 'entrepreneur']);
            $table->primary(['article_id', 'audience']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_audiences');
    }
}
