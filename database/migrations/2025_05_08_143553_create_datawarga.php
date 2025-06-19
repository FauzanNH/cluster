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
        Schema::create('datawarga', function (Blueprint $table) {
            $table->string('warga_id')->unique();
            $table->string('nama');
            $table->string('nik', 20)->unique();
            $table->string('no_kk', 20)->nullable();
            $table->string('domisili_ktp');
            $table->date('tanggal_lahir');
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->string('agama');
            $table->string('status_pernikahan');
            $table->string('pekerjaan');
            $table->string('pendidikan_terakhir');
            $table->string('foto_ktp')->nullable();
            $table->string('foto_kk')->nullable();
            $table->string('blok_rt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datawarga');
    }
};
