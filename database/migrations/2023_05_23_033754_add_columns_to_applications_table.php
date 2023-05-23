<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->timestamp('founder_NDA')->nullable();
            $table->timestamp('entrepreneur_NDA')->nullable();
            $table->text('negotiations')->nullable();
            $table->unsignedBigInteger('admin')->nullable();
            
            $table->foreign('admin')->references('id')->on('staff')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['founder_NDA', 'entrepreneur_NDA', 'negotiations', 'admin']);
        });
    }
}
