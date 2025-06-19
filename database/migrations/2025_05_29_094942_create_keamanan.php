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
        Schema::create('keamanan', function (Blueprint $table) {
            $table->string('users_id', 6);
            $table->string('pin')->nullable();
            $table->string('hint')->nullable();
            $table->enum('pin_active', ['aktif', 'nonaktif'])->default('nonaktif');
            $table->enum('login_pin_active', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
            $table->foreign('users_id')->references('users_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keamanan');
    }
};
