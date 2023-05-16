<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeRevokedColumnInOauthAccessTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->unsignedTinyInteger('revoked')->change();
        });*/
        DB::statement('alter table oauth_access_tokens alter column revoked type smallint using (case when revoked = true then 1 else 0 end)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->boolean('revoked')->change();
        });
    }
}
