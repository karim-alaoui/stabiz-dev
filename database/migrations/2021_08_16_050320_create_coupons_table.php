<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateCouponsTable
 */
class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_id')->unique()->comment('Stripe coupon id')->nullable();
            $table->string('name', 20)->unique()->nullable();
            $table->unsignedSmallInteger('amount_off')->nullable();
            $table->unsignedSmallInteger('percent_off')->nullable();
            $table->enum('currency', ['jpy', 'usd'])
                ->default('jpy');
            $table->enum('duration', ['forever', 'once', 'repeating'])->default('once');
            $table->unsignedSmallInteger('duration_in_months')->nullable();
            $table->unsignedInteger('redeem_by')->nullable();
            $table->enum('assign_after', ['registration', 'profile_fill_up'])
                ->comment('Assign after an event. It could be registration, after filling up profile etc')
                ->nullable();
            $table->boolean('is_a_campaign')
                ->comment('is it an any campaign? If yes, then this coupon will be valid for app')
                ->default(false);
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
        Schema::dropIfExists('master_coupons');
    }
}
