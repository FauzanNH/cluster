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
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->string('pengaduan_id', 10)->primary();
            $table->unsignedBigInteger('users_id');
            $table->string('jenis_pengaduan', 32);
            $table->text('detail_pengaduan');
            $table->string('lokasi', 255)->nullable();
            $table->enum('status_pengaduan', ['Tersampaikan', 'Dibaca RT'])->default('Tersampaikan');
            $table->string('dokumen1', 255)->nullable();
            $table->string('dokumen2', 255)->nullable();
            $table->text('remark')->nullable();
            $table->string('blok_rt', 32)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
