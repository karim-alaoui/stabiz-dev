<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadedDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploaded_docs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('verified_by_staff_id')->nullable();
            $table->foreign('verified_by_staff_id')
                ->references('id')
                ->on('staff')
                ->onDelete('set null');
            $table->enum('doc_name', [
                'all_historical_matter_cert',
                'fin_stmt_prev_fy',
                'tax_pymt_prev_period'
            ]);
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('filepath', 1000);
            $table->string('file_disk');
            $table->timestampTz('approved_at')->nullable();
            $table->timestampTz('rejected_at')->nullable();
            $table->softDeletesTz();
            $table->boolean('file_deleted')->nullable();
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
        Schema::dropIfExists('uploaded_docs');
    }
}
