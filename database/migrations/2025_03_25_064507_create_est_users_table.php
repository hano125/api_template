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
        Schema::create('est_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mother_name')->nullable();
            $table->string('b_day')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('year_of_tqad', 150)->nullable();
            $table->string('jaht')->nullable();
            $table->string('title_job')->nullable();
            $table->enum('enabled', ['0', '1'])->default('1');
            $table->enum('employee_flag', ['0', '1', '2', '3', '4'])->nullable();
            $table->double('degree')->nullable();
            $table->string('certif')->nullable();
            $table->string('mkun')->nullable();
            $table->string('file_2')->nullable();
            $table->string('file_3')->nullable();
            $table->string('file_4')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('est_users');
    }
};
