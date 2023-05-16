<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackageInPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['name']);
            $table->unsignedSmallInteger('package_id')->nullable();
            $table->foreign('package_id')
                ->references('id')
                ->on((new \App\Models\Package())->getTable())
                ->onDelete('cascade');
            $table->string('stripe_plan_id')->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->dropColumn(['package_id', 'stripe_plan_id']);
        });
    }
}
