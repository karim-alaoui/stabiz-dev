<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PresentPostEntrProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entrepreneur_profiles', function (Blueprint $table) {
            $table->dropForeign('entrepreneur_profiles_present_post_id_foreign');
            $table->foreign('present_post_id')
                ->references('id')
                ->on('present_posts')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entrepreneur_profiles', function (Blueprint $table) {
            //
        });
    }
}
