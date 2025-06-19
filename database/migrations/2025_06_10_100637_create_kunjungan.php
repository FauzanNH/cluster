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
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->string('kunjungan_id', 12)->unique();
            $table->string('tamu_id', 7);
            $table->string('rumah_id');
            $table->string('tujuan_kunjungan');
            $table->enum('status_kunjungan', ['Menunggu Menuju Cluster', 'Sedang Berlangsung', 'Meninggalkan Cluster'])->default('Menunggu Menuju Cluster');
            $table->timestamp('waktu_masuk')->nullable();
            $table->timestamp('waktu_keluar')->nullable();
            $table->timestamps();

            $table->foreign('tamu_id')->references('tamu_id')->on('tamu')->onDelete('cascade');
            $table->foreign('rumah_id')->references('rumah_id')->on('datarumah')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};
