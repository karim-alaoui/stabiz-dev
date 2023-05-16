<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiskForCompanyImgs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('founder_profiles', function (Blueprint $table) {
            $table->string('company_logo_disk', 15)
                ->nullable();
            $table->string('company_banner_disk', 15)
                ->nullable();
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
