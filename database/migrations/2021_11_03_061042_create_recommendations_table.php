<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('by_staff_id')
                ->comment('it was done by this staff')
                ->nullable();
            $table->foreign('by_staff_id')
                ->references('id')
                ->on('staff')
                ->onDelete('set null');
            $table->unsignedBigInteger('recommended_to_user_id')
                ->comment('to this user, the other user is recommended to')
                ->nullable();
            $table->foreign('recommended_to_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->unsignedBigInteger('recommended_user_id')
                ->comment('this user is recommended to the recommended_to_user_id')
                ->nullable();
            $table->foreign('recommended_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recommendations');
    }
}
