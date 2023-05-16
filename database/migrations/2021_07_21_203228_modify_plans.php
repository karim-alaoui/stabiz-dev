<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['recurring']);
        });

        \Illuminate\Support\Facades\DB::statement(<<<QUERY
alter table plans
    alter column currency type varchar(255),
    alter column currency drop not null,
    add constraint check_currency_either_yen_or_usd
        CHECK ("currency" in ('jpy', 'usd'))
QUERY
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('currency')->nullable()->change();
        });
    }
}
