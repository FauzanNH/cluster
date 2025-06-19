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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique(); // ID unik untuk pesan
            $table->unsignedBigInteger('chat_id'); // ID chat
            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->string('sender_id', 6); // ID pengirim pesan
            $table->foreign('sender_id')->references('users_id')->on('users')->onDelete('cascade');
            $table->text('message')->nullable(); // Isi pesan teks
            $table->string('image_path')->nullable(); // Path ke gambar jika ada
            $table->string('document_path')->nullable(); // Path ke dokumen jika ada
            $table->string('document_name')->nullable(); // Nama dokumen
            $table->string('document_type')->nullable(); // Tipe dokumen
            $table->string('document_size')->nullable(); // Ukuran dokumen
            $table->unsignedBigInteger('reply_to')->nullable(); // ID pesan yang dibalas
            $table->foreign('reply_to')->references('id')->on('messages')->onDelete('set null');
            $table->boolean('is_read')->default(false); // Status dibaca
            $table->timestamps();
            $table->softDeletes(); // Untuk fitur hapus pesan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
