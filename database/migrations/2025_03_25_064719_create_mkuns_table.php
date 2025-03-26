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
        Schema::create('mkuns', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->string('mkun_name');
=======
            $table->string('name');
>>>>>>> 830ca675d9a4c7834fee912b5c67136075deb7a3
            $table->string('mkun_flag', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkuns');
    }
};
