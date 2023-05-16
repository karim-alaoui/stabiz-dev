<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OccupationFKeyInEntProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\EntrepreneurProfile::query()->delete();

        Schema::table('entrepreneur_profiles', function (Blueprint $table) {
            $table->dropForeign('entrepreneur_profiles_occupation_id_foreign');
            $table->foreign('occupation_id')
                ->references('id')
                ->on('occupations');
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
