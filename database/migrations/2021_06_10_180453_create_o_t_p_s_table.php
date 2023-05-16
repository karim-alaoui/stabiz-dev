<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOTPSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('otp');
            $table->boolean('is_invalid')
                ->comment('when user requests a new otp all the old unused otp for the same email is marked as invalid')
                ->nullable();
            $table->timestampTz('expired_at')->default(\Illuminate\Support\Facades\DB::raw("now() + interval '5 min'"));
            $table->timestampTz('verified_at')->nullable();
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
        Schema::dropIfExists('otps');
    }
}
