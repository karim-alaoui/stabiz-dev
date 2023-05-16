<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFdrCompanyIndustriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fdr_company_industries', function (Blueprint $table) {
            $table->foreignId('founder_profile_id')
                ->constrained('founder_profiles')
                ->onDelete('cascade');
            $table->unsignedSmallInteger('industry_id');
            $table->foreign('industry_id')
                ->references('id')
                ->on('industries')
                ->onDelete('cascade');
            $table->primary(['founder_profile_id', 'industry_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fdr_company_industries');
    }
}
