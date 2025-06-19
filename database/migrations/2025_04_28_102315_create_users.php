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
        Schema::create('users', function (Blueprint $table) {
            $table->string('users_id', 6)->primary();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('no_hp');
            $table->string('password');
            $table->enum('gender', ['laki-laki', 'perempuan']);
            $table->enum('role', ['RT', 'Satpam', 'Warga', 'Developer'])->default('RT');
            $table->string('alamat');
            $table->string('rt_blok')->default(null);
            $table->string('fcm_token', 255)->nullable();
            $table->timestamps();
            $table->boolean('is_online')->default(false);
            $table->timestamp('last_active')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
