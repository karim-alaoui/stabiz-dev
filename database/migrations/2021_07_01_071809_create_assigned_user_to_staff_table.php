<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignedUserToStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assigned_user_to_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('added_by_staff_id')
                ->nullable()
                ->constrained('staff')
                ->onDelete('cascade');
            $table->unique(['staff_id', 'user_id'], 'unique_user_for_a_staff');
            $table->timestampTz('created_at')
                ->default(\Illuminate\Support\Facades\DB::raw('now()'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assigned_user_to_staff');
    }
}
