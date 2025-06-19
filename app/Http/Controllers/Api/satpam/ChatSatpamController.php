<?php

namespace App\Http\Controllers\Api\satpam;

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

class ChatSatpamController extends Controller
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
                $lastMessage = $chat->lastMessage;
                $unreadCount = $chat->unreadCount($user_id);
                
                if ($otherUser) {
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
     * Mengirim pesan baru
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|string',
            'message' => 'nullable|string|max:1000',
            'image' => 'nullable|string', // Base64 encoded image
            'document' => 'nullable|string', // Base64 encoded document
            'document_name' => 'nullable|string',
            'document_type' => 'nullable|string',
            'document_size' => 'nullable|string',
            'reply_to' => 'nullable|string',
        ]);
        
        // Minimal harus ada satu dari message, image, atau document
        if (empty($request->message) && empty($request->image) && empty($request->document)) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak boleh kosong',
            ], 422);
        }
        
        $user_id = $request->user()->users_id;
        
        // Cari chat berdasarkan chat_id
        $chat = Chat::where('chat_id', $request->chat_id)->first();
        
        if (!$chat || !$chat->hasUser($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Chat tidak ditemukan',
            ], 404);
        }
        
        // Mulai transaksi database
        DB::beginTransaction();
        
        try {
            // Buat pesan baru
            $message = new Message();
            $message->message_id = Str::uuid()->toString();
            $message->chat_id = $chat->id;
            $message->sender_id = $user_id;
            $message->message = $request->message;
            $message->is_read = false;
            
            // Jika ada reply_to, cari pesan yang dibalas
            if ($request->reply_to) {
                $replyMessage = Message::where('message_id', $request->reply_to)->first();
                if ($replyMessage) {
                    $message->reply_to = $replyMessage->id;
                }
            }
            
            // Upload gambar jika ada
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = uniqid('chat_', true) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('chat/images'), $imageName);
                $message->image_path = 'chat/images/' . $imageName;
            } elseif ($request->image) {
                // Jika base64
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image));
                $imageName = 'chat_' . time() . '_' . Str::random(10) . '.png';
                $path = public_path('chat/images/' . $imageName);
                file_put_contents($path, $image);
                $message->image_path = 'chat/images/' . $imageName;
            }
            
            // Upload dokumen jika ada
            if ($request->hasFile('document')) {
                $document = $request->file('document');
                $documentName = uniqid('doc_', true) . '.' . $document->getClientOriginalExtension();
                $document->move(public_path('chat/documents'), $documentName);
                $message->document_path = 'chat/documents/' . $documentName;
                $message->document_name = $document->getClientOriginalName();
                $message->document_type = $document->getClientOriginalExtension();
                $message->document_size = $request->document_size ?? $document->getSize();
            } elseif ($request->document) {
                // Jika base64
                $document = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '', $request->document));
                $documentName = $request->document_name ?? ('doc_' . time() . '_' . Str::random(10));
                $path = public_path('chat/documents/' . $documentName);
                file_put_contents($path, $document);
                $message->document_path = 'chat/documents/' . $documentName;
                $message->document_name = $request->document_name;
                $message->document_type = $request->document_type;
                $message->document_size = $request->document_size;
            }
            
            $message->save();
            
            // Update last_activity di chat
            $chat->last_activity = now();
            $chat->save();
            
            // Commit transaksi
            DB::commit();
            
            // Format response
            $response = [
                'id' => $message->message_id,
                'text' => $message->message,
                'time' => $this->formatTime($message->created_at),
                'sender' => 'me',
                'read' => false,
            ];
            
            if ($message->hasImage()) {
                $response['image'] = $message->getImageUrl();
            }
            
            if ($message->hasDocument()) {
                $response['document'] = [
                    'name' => $message->document_name,
                    'size' => $message->document_size,
                    'type' => $message->document_type,
                    'url' => $message->getDocumentUrl(),
                ];
            }
            
            if ($message->isReply()) {
                $response['replyTo'] = [
                    'id' => $message->replyMessage->message_id,
                    'text' => $message->replyMessage->message,
                    'image' => $message->replyMessage->hasImage() ? $message->replyMessage->getImageUrl() : null,
                    'time' => $this->formatTime($message->replyMessage->created_at),
                    'sender' => $message->replyMessage->sender_id === $user_id ? 'me' : 'other',
                ];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim',
                'data' => $response
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mendapatkan pesan baru setelah message_id tertentu
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewMessages(Request $request)
    {
        $request->validate([
            'chat_id' => 'required',
            'last_message_id' => 'required',
        ]);
        
        $user_id = $request->user()->users_id;
        
        try {
            // Cari chat berdasarkan chat_id (bisa berupa UUID atau ID numerik)
            $chat = is_numeric($request->chat_id) 
                ? Chat::where('id', $request->chat_id)->first() 
                : Chat::where('chat_id', $request->chat_id)->first();
            
            if (!$chat || !$chat->hasUser($user_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat tidak ditemukan',
                ], 404);
            }
            
            // Temukan pesan terakhir yang dilihat
            $lastMessage = Message::where('message_id', $request->last_message_id)->first();
            
            if (!$lastMessage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesan terakhir tidak ditemukan',
                    'messages' => []
                ], 200); // Return 200 dengan array kosong, bukan error 404
            }
            
            // Ambil pesan baru setelah pesan terakhir yang dilihat
            $newMessages = $chat->messages()
                ->where('created_at', '>', $lastMessage->created_at)
                ->with(['sender', 'replyMessage'])
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Format pesan untuk response
            $formattedMessages = [];
        } catch (\Exception $e) {
            \Log::error('Error in getNewMessages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
        foreach ($newMessages as $message) {
            $formattedMessages[] = [
                'id' => $message->message_id,
                'text' => $message->message,
                'image' => $message->hasImage() ? $message->getImageUrl() : null,
                'document' => $message->hasDocument() ? [
                    'name' => $message->document_name,
                    'size' => $message->document_size,
                    'type' => $message->document_type,
                    'url' => $message->getDocumentUrl(),
                ] : null,
                'time' => $this->formatTime($message->created_at),
                'sender' => $message->sender_id === $user_id ? 'me' : 'other',
                'read' => $message->is_read,
                'created_at' => $message->created_at->toISOString(),
                'replyTo' => $message->isReply() ? [
                    'id' => $message->replyMessage->message_id,
                    'text' => $message->replyMessage->message,
                    'sender' => $message->replyMessage->sender_id === $user_id ? 'me' : 'other',
                ] : null,
            ];
        }
        
        return response()->json([
            'success' => true,
            'messages' => $formattedMessages
        ]);
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
        
        // Soft delete pesan
        $message->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dihapus'
        ]);
    }

    /**
     * Menghapus chat beserta semua pesan di dalamnya
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteChat(Request $request)
    {
        $request->validate([
            'chat_id' => 'required',
        ]);
        
        $user_id = $request->user()->users_id;
        
        // Cari chat berdasarkan chat_id
        $chat = is_numeric($request->chat_id) 
            ? Chat::where('id', $request->chat_id)->first() 
            : Chat::where('chat_id', $request->chat_id)->first();
        
        if (!$chat || !$chat->hasUser($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Chat tidak ditemukan',
            ], 404);
        }
        
        // Nonaktifkan chat (soft delete)
        $chat->is_active = false;
        $chat->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Chat berhasil dihapus',
        ]);
    }
    
    /**
     * Menghapus semua pesan dalam chat tetapi mempertahankan chat
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearChat(Request $request)
    {
        $request->validate([
            'chat_id' => 'required',
        ]);
        
        $user_id = $request->user()->users_id;
        
        // Cari chat berdasarkan chat_id
        $chat = is_numeric($request->chat_id) 
            ? Chat::where('id', $request->chat_id)->first() 
            : Chat::where('chat_id', $request->chat_id)->first();
        
        if (!$chat || !$chat->hasUser($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Chat tidak ditemukan',
            ], 404);
        }
        
        // Hapus semua pesan dalam chat (soft delete)
        Message::where('chat_id', $chat->id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Semua pesan berhasil dihapus',
        ]);
    }
    
    /**
     * Download dokumen dari chat
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadDocument($id)
    {
        try {
            $message = Message::find($id);
            
            if (!$message || !$message->hasDocument()) {
                abort(404, 'Dokumen tidak ditemukan');
            }
            
            // Periksa apakah user yang login adalah bagian dari chat ini
            $chat = Chat::find($message->chat_id);
            $user_id = Auth::user()->users_id;
            
            if (!$chat || !$chat->hasUser($user_id)) {
                abort(403, 'Anda tidak memiliki akses ke dokumen ini');
            }
            
            $path = public_path($message->document_path);
            
            if (!file_exists($path)) {
                abort(404, 'File tidak ditemukan');
            }
            
            $fileName = $message->document_name ?: 'document';
            
            // Tambahkan header untuk cross-origin
            $headers = [
                'Content-Type' => mime_content_type($path),
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
            ];
            
            return response()->download($path, $fileName, $headers);
        } catch (\Exception $e) {
            \Log::error('Error in downloadDocument: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat mengunduh dokumen');
        }
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
}
