<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesInPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('recurring')->default(false);
            $table->enum('interval', ['month', 'year', 'day'])->nullable();
            $table->renameColumn('price_currency', 'currency');
            $table->renameColumn('price_per_month', 'price');
            $table->dropColumn(['price_per_year']);
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
            $table->dropColumn(['interval', 'recurring']);
            $table->renameColumn('currency', 'price_currency');
            $table->renameColumn('price', 'price_per_month');
            $table->unsignedBigInteger('price_per_year');
        });
    }
}
