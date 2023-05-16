<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBooleanInTelescopeEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'telescope_entries';
        if (!Schema::hasTable($table)) return; // on local only it would exists, on other env, it will not exist
        Schema::table($table, function (Blueprint $table) {
            $table->dropColumn('should_display_on_index');
        });
        Schema::table($table, function (Blueprint $table) {
            $table->unsignedTinyInteger('should_display_on_index')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telescope_entries', function (Blueprint $table) {
            //
        });
    }
}
