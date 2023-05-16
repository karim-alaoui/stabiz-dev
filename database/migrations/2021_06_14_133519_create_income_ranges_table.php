<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateIncomeRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income_ranges', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedBigInteger('lower_limit')->nullable();
            $table->unsignedBigInteger('upper_limit')->nullable();
            $table->string('unit')->nullable();
            $table->string('currency')->default('japanese yen')->nullable();
            $table->softDeletesTz();
        });

        DB::statement('alter table income_ranges add constraint check_range check(lower_limit <= upper_limit)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('income_ranges');
    }
}
