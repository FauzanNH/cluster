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
        Schema::create('datasatpam', function (Blueprint $table) {
            $table->string('users_id');
            $table->foreign('users_id')->references('users_id')->on('users')->onDelete('cascade');
            $table->string('nik');
            $table->string('tanggal_lahir');
            $table->string('no_kep');
            $table->string('seksi_unit_gerbang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datasatpam');
    }
};
