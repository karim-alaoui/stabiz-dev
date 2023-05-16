<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFounderProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('founder_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('company_name')->nullable();
            $table->boolean('is_listed_company')->nullable();
            $table->unsignedSmallInteger('area_id')->nullable();
            $table->foreign('area_id')
                ->references('id')
                ->on('areas')
                ->onDelete('cascade');
            $table->unsignedSmallInteger('prefecture_id')->nullable();
            $table->foreign('prefecture_id')
                ->references('id')
                ->on('prefectures')
                ->onDelete('cascade');
            $table->unsignedInteger('no_of_employees')->nullable();
            $table->unsignedBigInteger('capital')->nullable();
            $table->unsignedBigInteger('last_year_sales')
                ->nullable();
            $table->date('established_on')->nullable();
            $table->string('business_partner_company')->nullable();
            $table->string('major_bank')->nullable();
            $table->text('company_features')->nullable();
            $table->text('job_description')->nullable();
            $table->text('application_conditions')->nullable();
            $table->text('employee_benefits')->nullable();
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
        Schema::dropIfExists('founder_profiles');
    }
}
