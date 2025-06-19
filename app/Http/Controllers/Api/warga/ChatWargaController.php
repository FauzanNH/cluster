<?php

namespace App\Http\Controllers\Api\warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ChatWargaController extends Controller
{
    /**
     * Mendapatkan daftar chat
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChatList(Request $request)
    {
        try {
            $user_id = $request->user()->users_id;
            
            // Ambil semua chat yang melibatkan user yang sedang login
            $chats = Chat::where(function($query) use ($user_id) {
                    $query->where('user1_id', $user_id)
                          ->orWhere('user2_id', $user_id);
                })
                ->where('is_active', true)
                ->with(['user1', 'user2', 'lastMessage'])
                ->orderBy('last_activity', 'desc')
                ->get();
            
            // Format data untuk response
            $chatData = [];
            foreach ($chats as $chat) {
                $otherUser = $chat->getOtherUser($user_id);
                if (!$otherUser) continue; // Skip jika user lain tidak ditemukan
                
                $lastMessage = $chat->lastMessage;
                $unreadCount = $chat->unreadCount($user_id);
                
                $chatData[] = [
                    'id' => $chat->chat_id,
                    'name' => $otherUser->nama,
                    'avatar' => $otherUser->profile_picture ?? 'https://ionicframework.com/docs/img/demos/avatar.svg',
                    'lastMessage' => $lastMessage ? $lastMessage->message : 'Belum ada pesan',
                    'time' => $this->formatTime($lastMessage ? $lastMessage->created_at : null),
                    'unread' => $unreadCount,
                    'muted' => false, // Fitur mute bisa ditambahkan nanti
                ];
            }
            
            return response()->json([
                'success' => true,
                'chats' => $chatData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getChatList: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan detail chat
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChatDetail(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|string',
        ]);
        
        try {
            $user_id = $request->user()->users_id;
            $chat_id = $request->chat_id;
            
            // Cari chat berdasarkan chat_id
            $chat = Chat::where('chat_id', $chat_id)->first();
            
            if (!$chat || !$chat->hasUser($user_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat tidak ditemukan',
                ], 404);
            }
            
            // Dapatkan user lain dalam chat
            $otherUser = $chat->getOtherUser($user_id);
            
            if (!$otherUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna lain tidak ditemukan',
                ], 404);
            }
            
            // Ambil semua pesan dalam chat
            $messages = $chat->messages()
                ->with(['sender', 'replyMessage'])
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Format pesan untuk response
            $formattedMessages = [];
            foreach ($messages as $message) {
                try {
                    $messageData = [
                        'id' => $message->message_id,
                        'text' => $message->message,
                        'time' => $this->formatTime($message->created_at),
                        'sender' => $message->sender_id === $user_id ? 'me' : 'other',
                        'read' => $message->is_read,
                    ];
                    
                    // Tambahkan gambar jika ada
                    if ($message->hasImage()) {
                        $messageData['image'] = $message->getImageUrl();
                    }
                    
                    // Tambahkan dokumen jika ada
                    if ($message->hasDocument()) {
                        $messageData['document'] = [
                            'name' => $message->document_name,
                            'size' => $message->document_size,
                            'type' => $message->document_type,
                            'url' => $message->getDocumentUrl(),
                        ];
                    }
                    
                    // Tambahkan data reply jika ada
                    if ($message->isReply() && $message->replyMessage) {
                        $replyData = [
                            'id' => $message->replyMessage->message_id,
                            'text' => $message->replyMessage->message,
                            'sender' => $message->replyMessage->sender_id === $user_id ? 'me' : 'other',
                        ];
                        
                        if ($message->replyMessage->hasImage()) {
                            $replyData['image'] = $message->replyMessage->getImageUrl();
                        }
                        
                        $messageData['replyTo'] = $replyData;
                    }
                    
                    $formattedMessages[] = $messageData;
                } catch (\Exception $e) {
                    \Log::warning('Error formatting message: ' . $e->getMessage());
                    // Skip this message and continue with the next one
                    continue;
                }
            }
            
            // Tandai semua pesan sebagai sudah dibaca
            $chat->messages()
                ->where('sender_id', '!=', $user_id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            return response()->json([
                'success' => true,
                'chat' => [
                    'id' => $chat->chat_id,
                    'otherUser' => [
                        'id' => $otherUser->users_id,
                        'name' => $otherUser->nama,
                        'avatar' => $otherUser->profile_picture ?? 'https://ionicframework.com/docs/img/demos/avatar.svg',
                        'status' => 'Online', // Status bisa diambil dari tabel atau logika lain
                    ],
                    'messages' => $formattedMessages,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getChatDetail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kirim pesan chat baru (API)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|string',
                'message' => 'required_without_all:image,document|string|nullable',
                'image' => 'nullable|image|max:5120', // 5MB
                'document' => 'nullable|file|max:10240', // 10MB
                'reply_to' => 'nullable|exists:messages,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            
            $chatIdInput = $request->chat_id;
            $userId = Auth::user()->users_id;
            
            // Cari chat berdasarkan chat_id (bisa UUID atau ID numerik)
            $chat = null;
            
            // Coba cari berdasarkan ID numerik
            if (is_numeric($chatIdInput)) {
                $chat = Chat::find($chatIdInput);
            }
            
            // Jika tidak ditemukan, coba cari berdasarkan UUID
            if (!$chat) {
                $chat = Chat::where('chat_id', $chatIdInput)->first();
            }
            
            // Jika masih tidak ditemukan, kembalikan error
            if (!$chat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat tidak ditemukan'
                ], 404);
            }
            
            // Pastikan pengguna adalah bagian dari chat ini
            if ($chat->user1_id != $userId && $chat->user2_id != $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke chat ini'
                ], 403);
            }
            
            $message = new Message();
            $message->message_id = \Illuminate\Support\Str::uuid()->toString(); // Tambahkan message_id unik
            $message->chat_id = $chat->id; // Gunakan ID numerik yang valid
            $message->sender_id = $userId;
            $message->message = $request->message ?? '';
            $message->reply_to = $request->reply_to;
            
            // Upload gambar jika ada
            if ($request->hasFile('image')) {
                try {
                    $image = $request->file('image');
                    if (!$image->isValid()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'File gambar tidak valid'
                        ], 422);
                    }
                    $imageName = uniqid('chat_', true) . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('chat/images'), $imageName);
                    $message->image_path = 'chat/images/' . $imageName;
                } catch (\Exception $e) {
                    Log::error('Error saat upload gambar: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengunggah gambar: ' . $e->getMessage()
                    ], 500);
                }
            }
            
            // Upload dokumen jika ada
            if ($request->hasFile('document')) {
                try {
                    $document = $request->file('document');
                    if (!$document->isValid()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'File dokumen tidak valid'
                        ], 422);
                    }
                    if ($document->getSize() < 100) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Ukuran dokumen terlalu kecil (minimal 100 bytes)'
                        ], 422);
                    }
                    $documentName = uniqid('doc_', true) . '.' . $document->getClientOriginalExtension();
                    $document->move(public_path('chat/documents'), $documentName);
                    $message->document_path = 'chat/documents/' . $documentName;
                    $message->document_name = $document->getClientOriginalName();
                    $message->document_type = $document->getClientOriginalExtension();
                    $message->document_size = $this->formatFileSize($document->getSize());
                    Log::info('Dokumen berhasil diunggah:', [
                        'name' => $message->document_name,
                        'path' => $message->document_path,
                        'size' => $document->getSize(),
                        'type' => $message->document_type
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error saat upload dokumen: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengunggah dokumen: ' . $e->getMessage()
                    ], 500);
                }
            }
            
            // Simpan pesan ke database
            $message->save();
            
            // Update waktu terakhir chat
            $chat->last_activity = now();
            $chat->save();
            
            // Format pesan untuk response
            $messageData = $this->formatMessageForResponse($message, $userId);
            
            return response()->json([
                'success' => true,
                'data' => $messageData
            ]);
        } catch (\Exception $e) {
            Log::error('Error in sendMessage: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan pesan baru
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewMessages(Request $request)
    {
        try {
            $request->validate([
                'chat_id' => 'required|string',
                'last_message_id' => 'required|string',
            ]);
            
            $user_id = $request->user()->users_id;
            $chatIdInput = $request->chat_id;
            
            // Cari chat berdasarkan chat_id (bisa UUID atau ID numerik)
            $chat = null;
            
            // Coba cari berdasarkan ID numerik
            if (is_numeric($chatIdInput)) {
                $chat = Chat::find($chatIdInput);
            }
            
            // Jika tidak ditemukan, coba cari berdasarkan UUID
            if (!$chat) {
                $chat = Chat::where('chat_id', $chatIdInput)->first();
            }
            
            // Jika masih tidak ditemukan, kembalikan error
            if (!$chat || !$chat->hasUser($user_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat tidak ditemukan',
                ], 404);
            }
            
            // Cari message terakhir yang diterima client
            $lastMessageId = $request->last_message_id;
            $lastMessage = null;
            
            // Coba cari berdasarkan ID numerik
            if (is_numeric($lastMessageId)) {
                $lastMessage = Message::find($lastMessageId);
            }
            
            // Jika tidak ditemukan, coba cari berdasarkan UUID
            if (!$lastMessage) {
                $lastMessage = Message::where('message_id', $lastMessageId)->first();
            }
            
            if (!$lastMessage) {
                // Jika last_message_id tidak valid, kembalikan pesan terbaru
                // Ini untuk mencegah error dan memulai polling dari awal
                $lastMessage = $chat->messages()->orderBy('created_at', 'desc')->first();
                
                if (!$lastMessage) {
                    // Jika tidak ada pesan sama sekali, kembalikan array kosong
                    return response()->json([
                        'success' => true,
                        'messages' => []
                    ]);
                }
            }
            
            // Ambil pesan baru setelah last_message_id
            $newMessages = $chat->messages()
                ->where('created_at', '>', $lastMessage->created_at)
                ->with(['sender', 'replyMessage'])
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Format pesan untuk response
            $formattedMessages = [];
            foreach ($newMessages as $message) {
                $formattedMessages[] = $this->formatMessageForResponse($message, $user_id);
            }
            
            // Tandai semua pesan baru dari lawan bicara sebagai sudah dibaca
            $chat->messages()
                ->where('sender_id', '!=', $user_id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            return response()->json([
                'success' => true,
                'messages' => $formattedMessages
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getNewMessages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membuat chat baru
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createChat(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string|exists:users,users_id',
        ]);
        
        $user_id = $request->user()->users_id;
        $other_user_id = $request->user_id;
        
        // Cek apakah chat sudah ada
        $existingChat = Chat::where(function($query) use ($user_id, $other_user_id) {
                $query->where('user1_id', $user_id)
                      ->where('user2_id', $other_user_id);
            })
            ->orWhere(function($query) use ($user_id, $other_user_id) {
                $query->where('user1_id', $other_user_id)
                      ->where('user2_id', $user_id);
            })
            ->first();
        
        if ($existingChat) {
            // Jika chat sudah ada tapi tidak aktif, aktifkan kembali
            if (!$existingChat->is_active) {
                $existingChat->is_active = true;
                $existingChat->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Chat sudah ada',
                'chat_id' => $existingChat->chat_id
            ]);
        }
        
        // Buat chat baru
        $chat = new Chat();
        $chat->chat_id = Str::uuid()->toString();
        $chat->user1_id = $user_id;
        $chat->user2_id = $other_user_id;
        $chat->last_activity = now();
        $chat->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Chat berhasil dibuat',
            'chat_id' => $chat->chat_id
        ]);
    }

    /**
     * Mendapatkan daftar kontak
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContacts(Request $request)
    {
        $user_id = $request->user()->users_id;
        
        // Ambil semua pengguna kecuali diri sendiri
        $contacts = User::where('users_id', '!=', $user_id)
            ->select('users_id', 'nama', 'email', 'role')
            ->orderBy('nama')
            ->get();
        
        $formattedContacts = [];
        foreach ($contacts as $contact) {
            $formattedContacts[] = [
                'id' => $contact->users_id,
                'name' => $contact->nama,
                'avatar' => 'https://ionicframework.com/docs/img/demos/avatar.svg', // Avatar default
                'role' => $contact->role,
                'email' => $contact->email
            ];
        }
        
        return response()->json([
            'success' => true,
            'contacts' => $formattedContacts
        ]);
    }

    /**
     * Tandai pesan sebagai sudah dibaca
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|string',
        ]);
        
        $user_id = $request->user()->users_id;
        
        // Cari chat berdasarkan chat_id
        $chat = Chat::where('chat_id', $request->chat_id)->first();
        
        if (!$chat || !$chat->hasUser($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Chat tidak ditemukan',
            ], 404);
        }
        
        // Tandai semua pesan dari lawan bicara sebagai sudah dibaca
        $chat->messages()
            ->where('sender_id', '!=', $user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Pesan ditandai sebagai sudah dibaca'
        ]);
    }

    /**
     * Hapus pesan
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMessage(Request $request)
    {
        // Validasi request dengan rules yang berbeda berdasarkan tipe penghapusan
        if ($request->has('clear_all') && $request->clear_all) {
            $request->validate([
                'chat_id' => 'required|string',
                'clear_all' => 'boolean'
            ]);
            
            $user_id = $request->user()->users_id;
            
            // Cari chat berdasarkan chat_id
            $chat = Chat::where('chat_id', $request->chat_id)->first();
            
            if (!$chat || !$chat->hasUser($user_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat tidak ditemukan',
                ], 404);
            }
            
            // Hapus semua pesan dalam chat (hanya pesan yang dikirim oleh pengguna)
            // Untuk implementasi yang lebih lengkap, bisa ditambahkan opsi untuk menghapus semua pesan
            $messages = Message::where('chat_id', $chat->id)
                ->get();
                
            // Hapus file yang terkait dengan pesan
            foreach ($messages as $message) {
                // Hapus gambar jika ada
                if ($message->image_path && file_exists(public_path($message->image_path))) {
                    unlink(public_path($message->image_path));
                }
                
                // Hapus dokumen jika ada
                if ($message->document_path && file_exists(public_path($message->document_path))) {
                    unlink(public_path($message->document_path));
                }
                
                // Hapus pesan (soft delete)
                $message->delete();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Semua pesan dalam chat berhasil dihapus'
            ]);
        } else {
        $request->validate([
            'message_id' => 'required|string',
        ]);
        
        $user_id = $request->user()->users_id;
        
        // Cari pesan berdasarkan message_id
        $message = Message::where('message_id', $request->message_id)
            ->where('sender_id', $user_id) // Hanya bisa menghapus pesan sendiri
            ->first();
        
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak ditemukan atau Anda tidak memiliki izin untuk menghapusnya',
            ], 404);
        }
            
            // Hapus gambar jika ada
            if ($message->image_path && file_exists(public_path($message->image_path))) {
                unlink(public_path($message->image_path));
            }
            
            // Hapus dokumen jika ada
            if ($message->document_path && file_exists(public_path($message->document_path))) {
                unlink(public_path($message->document_path));
            }
        
        // Soft delete pesan
        $message->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dihapus'
        ]);
        }
    }

    /**
     * Membersihkan chat (menghapus semua pesan)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearChat(Request $request)
    {
        // Tambahkan parameter clear_all ke request
        $request->merge(['clear_all' => true]);
        
        // Panggil metode deleteMessage
        return $this->deleteMessage($request);
    }

    /**
     * Format waktu untuk tampilan
     *
     * @param  \DateTime|null  $dateTime
     * @return string
     */
    private function formatTime($dateTime)
    {
        if (!$dateTime) return '';
        
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $yesterday = $now->subDay()->format('Y-m-d');
        
        if ($dateTime->format('Y-m-d') === $today) {
            return $dateTime->format('H:i');
        } elseif ($dateTime->format('Y-m-d') === $yesterday) {
            return 'Kemarin';
        } else {
            return $dateTime->format('d/m');
        }
    }

    /**
     * Mengunduh dokumen dari pesan
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadDocument($id)
    {
        try {
            $message = Message::where('message_id', $id)->first();
            
            // Jika pesan tidak ditemukan, coba cari berdasarkan ID numerik
            if (!$message) {
                $message = Message::find($id);
            }
            
            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesan tidak ditemukan'
                ], 404);
            }
            
            $user_id = auth()->user()->users_id;
            $chat = Chat::find($message->chat_id);
            
            // Pastikan pengguna adalah bagian dari chat ini
            if (!$chat || !$chat->hasUser($user_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke dokumen ini'
                ], 403);
            }
            
            // Pastikan pesan memiliki dokumen
            if (!$message->document_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan'
                ], 404);
            }
            
            $path = public_path($message->document_path);
            
            // Cek apakah file ada
            if (!file_exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }
            
            // Siapkan nama file untuk diunduh
            $fileName = $message->document_name ?? basename($message->document_path);
            
            // Pastikan nama file memiliki ekstensi
            if ($message->document_type && !str_contains($fileName, '.')) {
                $fileName .= '.' . $message->document_type;
            }
            
            // Mendapatkan tipe konten
            $mimeType = mime_content_type($path);
            
            // Untuk tampilan di browser, gunakan inline untuk beberapa jenis file
            $disposition = 'attachment';
            
            // Untuk file gambar dan PDF, gunakan inline agar bisa ditampilkan langsung di browser
            if (in_array($mimeType, ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'])) {
                $disposition = 'inline';
            }
            
            // Cek apakah request dari mobile app (Ionic) dengan header khusus
            $userAgent = request()->header('User-Agent');
            $isMobile = preg_match('/(Mobile|Android|iPhone|iPad|iPod|Ionic|Capacitor)/i', $userAgent);
            
            // Jika dari mobile, selalu gunakan attachment untuk memastikan file diunduh dengan benar
            if ($isMobile) {
                $disposition = 'attachment';
            }
            
            // Tambahkan header untuk cross-origin
            $headers = [
                'Content-Type' => $mimeType,
                'Content-Disposition' => $disposition . '; filename="' . $fileName . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
            ];
            
            // Unduh file dengan header yang sesuai
            return response()->download($path, $fileName, $headers);
        } catch (\Exception $e) {
            \Log::error('Error in downloadDocument: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunduh dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus chat
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteChat(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|string',
        ]);
        
        $user_id = $request->user()->users_id;
        
        // Cari chat berdasarkan chat_id
        $chat = Chat::where('chat_id', $request->chat_id)->first();
        
        if (!$chat || !$chat->hasUser($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Chat tidak ditemukan',
            ], 404);
        }
        
        DB::beginTransaction();
        
        try {
            // Hapus semua pesan dalam chat
            $messages = Message::where('chat_id', $chat->id)->get();
            
            // Hapus file yang terkait dengan pesan
            foreach ($messages as $message) {
                // Hapus gambar jika ada
                if ($message->image_path && file_exists(public_path($message->image_path))) {
                    unlink(public_path($message->image_path));
                }
                
                // Hapus dokumen jika ada
                if ($message->document_path && file_exists(public_path($message->document_path))) {
                    unlink(public_path($message->document_path));
                }
                
                // Hapus pesan (soft delete)
                $message->delete();
            }
            
            // Hapus chat
            $chat->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Chat berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus chat: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format pesan untuk response API
     *
     * @param  \App\Models\Message  $message
     * @param  string  $userId
     * @return array
     */
    private function formatMessageForResponse(Message $message, string $userId): array
    {
        $response = [
            'id' => $message->id,
            'text' => $message->message,
            'time' => date('H:i', strtotime($message->created_at)),
            'sender' => $message->sender_id === $userId ? 'me' : 'other',
            'read' => (bool) $message->is_read,
            'created_at' => $message->created_at->toIso8601String()
        ];
        
        // Tambahkan gambar jika ada
        if ($message->image_path) {
            $response['image'] = asset($message->image_path);
        }
        
        // Tambahkan dokumen jika ada
        if ($message->document_path) {
            $response['document'] = [
                'id' => $message->id,
                'name' => $message->document_name ?? basename($message->document_path),
                'size' => $message->document_size ?? $this->formatFileSize(file_exists(public_path($message->document_path)) ? filesize(public_path($message->document_path)) : 0),
                'type' => $message->document_type ?? pathinfo($message->document_path, PATHINFO_EXTENSION),
                'url' => asset($message->document_path),
                'download_url' => route('api.warga.chat.download-document', ['id' => $message->id])
            ];
        }
        
        // Tambahkan data reply jika ada
        if ($message->reply_to) {
            $replyMessage = Message::find($message->reply_to);
            if ($replyMessage) {
                $replySender = $replyMessage->sender_id === $userId ? 'me' : 'other';
                $replyData = [
                    'id' => $replyMessage->id,
                    'text' => $replyMessage->message,
                    'sender' => $replySender,
                ];
                
                // Tambahkan gambar jika ada
                if ($replyMessage->image_path) {
                    $replyData['image'] = asset($replyMessage->image_path);
                }
                
                $response['replyTo'] = $replyData;
            }
        }
        
        return $response;
    }

    /**
     * Format ukuran file dalam bytes ke format yang lebih mudah dibaca
     *
     * @param  int  $bytes
     * @return string
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
