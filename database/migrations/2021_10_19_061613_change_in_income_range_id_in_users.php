<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeInIncomeRangeIdInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('alter table users drop constraint if exists users_income_range_id_foreign');
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('income_range_id')->nullable()->change();
        });

        \App\Models\User::whereNotIn('income_range_id', \App\Models\IncomeRange::all()->pluck('id'))
            ->update(['income_range_id' => null]);
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('income_range_id')
                ->references('id')
                ->on('income_ranges')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
