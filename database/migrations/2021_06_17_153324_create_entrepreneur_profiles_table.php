<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateEntrepreneurProfilesTable
 */
class CreateEntrepreneurProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrepreneur_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('address')->nullable();
            $table->unsignedTinyInteger('education_background_id')->nullable();
            $table->foreign('education_background_id')
                ->references('id')
                ->on('education_backgrounds');
            $table->string('school_name')->nullable();
            $table->unsignedTinyInteger('working_status_id')->nullable();
            $table->foreign('working_status_id')
                ->references('id')
                ->on('working_statuses');
            $table->string('present_company')->nullable();
            $table->unsignedTinyInteger('present_post_id')->nullable();
            $table->foreign('present_post_id')
                ->references('id')
                ->on('present_posts');
            $table->string('present_post_other')
                ->comment('If the user select other as option while present post, then they can fill other post value in this column')
                ->nullable();
            $table->unsignedTinyInteger('occupation_id')->nullable();
            $table->foreign('occupation_id')
                ->references('id')
                ->on('occupations');
            $table->enum('transfer', ['yes', 'no', 'only domestic', 'only overseas'])->nullable();
            $table->boolean('has_mgmt_exp')->nullable();
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
        Schema::dropIfExists('entrepreneur_profiles');
    }
}
