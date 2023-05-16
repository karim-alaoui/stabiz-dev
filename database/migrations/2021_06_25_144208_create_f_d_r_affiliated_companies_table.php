<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFDRAffiliatedCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fdr_affiliated_companies', function (Blueprint $table) {
            $table->foreignId('founder_profile_id')
                ->constrained('founder_profiles')
                ->onDelete('cascade');
            $table->string('company_name');
            $table->primary(['founder_profile_id', 'company_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fdr_affiliated_companies');
    }
}
