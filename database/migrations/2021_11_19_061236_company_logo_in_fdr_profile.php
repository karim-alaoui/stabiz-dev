<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CompanyLogoInFdrProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('founder_profiles', function (Blueprint $table) {
            $table->string('company_logo_path', 1000)->nullable();
            $table->string('company_banner_img_path', 1000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('founder_profiles', function (Blueprint $table) {
            //
        });
    }
}
