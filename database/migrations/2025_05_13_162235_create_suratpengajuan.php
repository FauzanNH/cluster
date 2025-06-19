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
        Schema::create('suratpengajuan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('surat_id');
            $table->string('warga_id');
            $table->string('rumah_id');
            $table->string('jenis_surat');
            $table->enum('status_penegerjaan', ['menunggu verifikasi', 'sedang di validasi', 'disetujui', 'ditolak'])->default('menunggu verifikasi');
            $table->string('foto_ktp')->nullable();
            $table->string('kartu_keluarga')->nullable();
            $table->string('dokumen_lainnya1')->nullable();
            $table->string('dokumen_lainnya2')->nullable();
            $table->string('keperluan_keramaian')->nullable();
            $table->string('tempat_keramaian')->nullable();
            $table->string('tanggal_keramaian')->nullable();
            $table->string('jam_keramaian')->nullable();
            $table->timestamps();
            $table->engine = 'MyISAM';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suratpengajuan');
    }
};
