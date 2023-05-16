<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFounderUserTable extends Migration
{
    public function up()
    {
        Schema::create('founder_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('founder_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role');
            $table->timestamps();

            $table->foreign('founder_id')->references('id')->on('founder_profiles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('founder_user');
    }
}

