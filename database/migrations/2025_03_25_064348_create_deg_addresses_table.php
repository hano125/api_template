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
        Schema::create('deg_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('deg_address_name');
            // $table->foreignId('deg_id')->constrained('deg_types')->onDelete('cascade');
            $table->unsignedBigInteger('deg_id')->nullable();
            // $table->foreign('deg_id')->references('id')->on('deg_types')->onDelete('cascade');
            $table->enum('flag', ['0', '1'])->nullable(); // adjust enum values as needed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deg_addresses');
    }
};
