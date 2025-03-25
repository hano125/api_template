<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('est_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("main_id")->nullable();
            $table->foreign('main_id')->references('id')->on('est_mains')->onDelete('cascade');
            $table->string('vacancy_file')->nullable();
            $table->string('majles_file')->nullable();
            $table->string('malia_file')->nullable();
            $table->string('file_back')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('est_files');
    }
};
