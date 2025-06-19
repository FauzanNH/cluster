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
        Schema::create('jadwal_kerja_satpam', function (Blueprint $table) {
            $table->id();
            $table->string('users_id');
            $table->foreign('users_id')->references('users_id')->on('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('shift', ['pagi', 'siang', 'malam', 'libur']);
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('lokasi')->default('Pos Utama');
            $table->string('lokasi_detail')->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Composite unique to prevent duplicate schedules
            $table->unique(['users_id', 'tanggal']);
        });
        
        // Create a table for teams/groups of security guards
        Schema::create('tim_satpam', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tim');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
        
        // Create a pivot table for security guard teams
        Schema::create('anggota_tim_satpam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tim_satpam_id')->constrained('tim_satpam')->onDelete('cascade');
            $table->string('users_id');
            $table->foreign('users_id')->references('users_id')->on('users')->onDelete('cascade');
            $table->enum('posisi', ['Kepala Shift', 'Petugas']);
            $table->timestamps();
            
            // Prevent duplicate entries
            $table->unique(['tim_satpam_id', 'users_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_tim_satpam');
        Schema::dropIfExists('tim_satpam');
        Schema::dropIfExists('jadwal_kerja_satpam');
    }
};
