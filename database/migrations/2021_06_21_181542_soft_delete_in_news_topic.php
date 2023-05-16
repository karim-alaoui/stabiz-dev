<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SoftDeleteInNewsTopic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('news_topics', function (Blueprint $table) {
            $table->foreignId('added_by_staff_id')
                ->nullable()
                ->constrained('staff')
                ->onDelete('cascade');
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news_topics', function (Blueprint $table) {
            //
        });
    }
}
