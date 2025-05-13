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
        Schema::create('datarumah', function (Blueprint $table) {
            $table->string('rumah_id');
            $table->unsignedBigInteger('users_id');
            $table->string('warga_id1')->nullable();
            $table->string('warga_id2')->nullable();
            $table->string('warga_id3')->nullable();
            $table->string('warga_id4')->nullable();
            $table->string('warga_id5')->nullable();
            $table->string('blok_rt');
            $table->string('status_kepemilikan');
            $table->string('alamat_cluster');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datarumah');
    }
};
