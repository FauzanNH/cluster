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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id')->unique(); // ID unik untuk chat
            $table->string('user1_id', 6); // ID user pertama
            $table->string('user2_id', 6); // ID user kedua
            $table->foreign('user1_id')->references('users_id')->on('users')->onDelete('cascade');
            $table->foreign('user2_id')->references('users_id')->on('users')->onDelete('cascade');
            $table->timestamp('last_activity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
