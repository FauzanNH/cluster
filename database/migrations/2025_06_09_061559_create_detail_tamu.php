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
        Schema::create('detail_tamu', function (Blueprint $table) {
            $table->id();
            $table->string('tamu_id', 7);
            $table->string('nik', 20);
            $table->string('nama', 100);
            $table->string('tempat_lahir', 50);
            $table->date('tgl_lahir');
            $table->string('kewarganegaraan', 30);
            $table->string('alamat', 255);
            $table->string('rt', 5);
            $table->string('rw', 5);
            $table->string('kel_desa', 50);
            $table->string('kecamatan', 50);
            $table->string('kabupaten', 50);
            $table->string('agama', 20);
            $table->timestamps();

            $table->foreign('tamu_id')->references('tamu_id')->on('tamu')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_tamu');
    }
};
