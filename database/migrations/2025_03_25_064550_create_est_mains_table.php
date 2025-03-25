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
        Schema::create('est_mains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('minstry')->nullable();
            $table->string('tshkeel')->nullable();
            $table->string('vacancy_deg_type')->nullable();
            $table->string('vacancy_deg_address')->nullable();
            $table->string('mkun')->nullable();
            $table->string('required_deg_type')->nullable();
            $table->string('required_deg_address')->nullable();
            $table->string('certif')->nullable();
            $table->integer('book_num')->nullable();
            $table->string('book_date')->nullable();
            $table->string('newly_deg_type')->nullable();
            $table->string('newly_deg_address')->nullable();
            $table->enum('complate_flag', ['0', '1'])->nullable();
            $table->enum('deflg', ['0', '1'])->nullable();
            $table->string('num_back')->nullable();
            $table->string('chek')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('est_mains');
    }
};
