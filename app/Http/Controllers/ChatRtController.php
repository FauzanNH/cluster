<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ChatRtController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (strtolower(Auth::user()->role) !== 'rt') {
                return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke halaman ini');
            }
            return $next($request);
        });
    }

    /**
     * Tampilkan halaman daftar chat
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $user_id = Auth::user()->users_id;
            
            // Ambil daftar chat dari database
            $chats = Chat::where('user1_id', $user_id)
                ->orWhere('user2_id', $user_id)
                ->orderBy('last_activity', 'desc')
                ->get();
            
            $chatList = [];
            
            foreach ($chats as $chat) {
                // Tentukan ID pengguna lain dalam chat
                $otherUserId = ($chat->user1_id == $user_id) ? $chat->user2_id : $chat->user1_id;
                
                // Ambil data pengguna lain
                $otherUser = User::where('users_id', $otherUserId)->first();
                
                if ($otherUser) {
                    // Ambil pesan terakhir
                    $lastMessage = Message::where('chat_id', $chat->id)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    // Hitung jumlah pesan yang belum dibaca
                    $unreadCount = Message::where('chat_id', $chat->id)
                        ->where('sender_id', '!=', $user_id)
                        ->where('is_read', 0)
                        ->count();
                    
                    // Format waktu pesan terakhir
                    $time = $lastMessage ? $this->formatTime($lastMessage->created_at) : '';
                    
                    // Siapkan data untuk view
                    $chatList[] = [
                        'id' => $chat->id,
                        'name' => $otherUser->nama,
                        'avatar' => 'https://ionicframework.com/docs/img/demos/avatar.svg',
                        'lastMessage' => $lastMessage ? $lastMessage->message : 'Mulai percakapan',
                        'time' => $time,
                        'unread' => $unreadCount,
                        'muted' => false, // Tambahkan fitur mute nanti
                        'other_user_id' => $otherUser->users_id, // Tambahkan ID pengguna lain
                        'is_online' => $otherUser->isOnline(), // Tambahkan status online
                        'last_active_timestamp' => $lastMessage ? $lastMessage->created_at->toIso8601String() : null
                    ];
                }
            }
            
            return view('rt.chat-rt.index', [
                'chats' => $chatList
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in index: ' . $e->getMessage());
            return redirect()->route('rt.dashboard')->with('error', 'Terjadi kesalahan saat memuat halaman chat: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan halaman detail chat
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function viewChat($id)
    {
        try {
            $user_id = Auth::user()->users_id;
            $chat = Chat::findOrFail($id);
            
            // Pastikan pengguna adalah bagian dari chat ini
            if ($chat->user1_id != $user_id && $chat->user2_id != $user_id) {
                return redirect()->route('rt.chat.index')->with('error', 'Anda tidak memiliki akses ke chat ini');
            }
            
            // Tentukan ID pengguna lain dalam chat
            $otherUserId = ($chat->user1_id == $user_id) ? $chat->user2_id : $chat->user1_id;
            
            // Ambil data pengguna lain
            $otherUser = User::where('users_id', $otherUserId)->first();
            
            if (!$otherUser) {
                return redirect()->route('rt.chat.index')->with('error', 'Pengguna tidak ditemukan');
            }
            
            // Ambil pesan-pesan dalam chat
            $messages = Message::where('chat_id', $id)
                ->orderBy('created_at', 'asc')
                ->get();
            
            $formattedMessages = [];
            
            foreach ($messages as $message) {
                $isSender = $message->sender_id == $user_id;
                
                // Cek apakah ada reply
                $replyData = null;
                if ($message->reply_to) {
                    $replyMessage = Message::find($message->reply_to);
                    if ($replyMessage) {
                        $replySender = $replyMessage->sender_id == $user_id ? 'me' : 'other';
                        $replyData = [
                            'id' => $replyMessage->id,
                            'text' => $replyMessage->message,
                            'sender' => $replySender,
                        ];
                        
                        // Tambahkan gambar jika ada
                        if ($replyMessage->image_path) {
                            $replyData['image'] = asset($replyMessage->image_path);
                        }
                    }
                }
                
                $messageData = [
                    'id' => $message->id,
                    'text' => $message->message,
                    'time' => date('H:i', strtotime($message->created_at)),
                    'sender' => $isSender ? 'me' : 'other',
                    'read' => $message->is_read,
                    'created_at' => $message->created_at->toIso8601String()
                ];
                
                // Tambahkan gambar jika ada
                if ($message->image_path) {
                    $messageData['image'] = asset($message->image_path);
                }
                
                // Tambahkan dokumen jika ada
                if ($message->document_path) {
                    $messageData['document'] = [
                    'id' => $message->id, // Tambahkan ID pesan untuk digunakan dalam unduhan
                        'name' => $message->document_name ?? basename($message->document_path),
                        'size' => $message->document_size ?? $this->formatFileSize(file_exists(public_path($message->document_path)) ? filesize(public_path($message->document_path)) : 0),
                        'type' => $message->document_type ?? pathinfo($message->document_path, PATHINFO_EXTENSION),
                    'url' => asset($message->document_path),
                    'download_url' => route('rt.chat.download-document', ['id' => $message->id])
                    ];
                }
                
                // Tambahkan data reply jika ada
                if ($replyData) {
                    $messageData['replyTo'] = $replyData;
                }
                
                $formattedMessages[] = $messageData;
            }
            
            // Tandai semua pesan sebagai sudah dibaca
            Message::where('chat_id', $id)
                ->where('sender_id', '!=', $user_id)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);
            
            return view('rt.chat-rt.viewchat-rt', [
                'chatId' => $id,
                'otherUser' => [
                    'id' => $otherUser->users_id,
                    'name' => $otherUser->nama,
                    'avatar' => 'https://ionicframework.com/docs/img/demos/avatar.svg',
                    'status' => $otherUser->getOnlineStatusText(),
                    'is_online' => $otherUser->isOnline(),
                    'last_active_timestamp' => $otherUser->last_active ? $otherUser->last_active->toIso8601String() : null
                ],
                'messages' => $formattedMessages
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in viewChat: ' . $e->getMessage());
            return redirect()->route('rt.chat.index')->with('error', 'Terjadi kesalahan saat memuat chat: ' . $e->getMessage());
        }
    }

    /**
     * Kirim pesan chat baru (API)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request, $id)
    {
        try {
            $chat = Chat::findOrFail($id);
            $user_id = Auth::user()->users_id;
            
            // Pastikan pengguna adalah bagian dari chat ini
            if ($chat->user1_id != $user_id && $chat->user2_id != $user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke chat ini'
                ]);
            }
            
            $request->validate([
                'message' => 'required_without_all:image,document|string|nullable',
                'image' => 'nullable|image|max:2048',
                'document' => 'nullable|file|max:10240',
                'reply_to' => 'nullable|exists:messages,id'
            ]);
            
            $message = new Message();
            $message->message_id = \Illuminate\Support\Str::uuid()->toString(); // Tambahkan message_id unik
            $message->chat_id = $id;
            $message->sender_id = $user_id;
            $message->message = $request->message ?? '';
            $message->reply_to = $request->reply_to;
            
            // Upload gambar jika ada
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = uniqid('chat_', true) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('chat/images'), $imageName);
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
                $message->document_size = $this->formatFileSize($document->getSize());
            }
            
            $message->save();
            
            // Update waktu terakhir chat
            $chat->last_activity = now();
            $chat->save();
            
            // Format pesan untuk response
            $messageData = [
                'id' => $message->id,
                'text' => $message->message,
                'time' => date('H:i', strtotime($message->created_at)),
                'sender' => 'me',
                'read' => false,
                'created_at' => $message->created_at->toIso8601String()
            ];
            
            // Tambahkan gambar jika ada
            if ($message->image_path) {
                $messageData['image'] = asset($message->image_path);
            }
            
            // Tambahkan dokumen jika ada
            if ($message->document_path) {
                $messageData['document'] = [
                    'name' => $message->document_name ?? basename($message->document_path),
                    'size' => $message->document_size ?? $this->formatFileSize(file_exists(public_path($message->document_path)) ? filesize(public_path($message->document_path)) : 0),
                    'type' => $message->document_type ?? pathinfo($message->document_path, PATHINFO_EXTENSION),
                    'url' => asset($message->document_path),
                    'download_url' => route('rt.chat.download-document', ['id' => $message->id])
                ];
            }
            
            // Tambahkan data reply jika ada
            if ($message->reply_to) {
                $replyMessage = Message::find($message->reply_to);
                if ($replyMessage) {
                    $replySender = $replyMessage->sender_id == $user_id ? 'me' : 'other';
                    $replyData = [
                        'id' => $replyMessage->id,
                        'text' => $replyMessage->message,
                        'sender' => $replySender,
                    ];
                    
                    // Tambahkan gambar jika ada
                    if ($replyMessage->image_path) {
                        $replyData['image'] = asset($replyMessage->image_path);
                    }
                    
                    $messageData['replyTo'] = $replyData;
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $messageData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in sendMessage: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil pesan chat baru (API)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewMessages(Request $request, $id)
    {
        try {
            $chat = Chat::findOrFail($id);
            $user_id = Auth::user()->users_id;
            
            // Pastikan pengguna adalah bagian dari chat ini
            if ($chat->user1_id != $user_id && $chat->user2_id != $user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke chat ini'
                ]);
            }
            
            $lastMessageId = $request->input('last_message_id', 0);
            
            // Ambil pesan baru
            $messages = Message::where('chat_id', $id)
                ->where('id', '>', $lastMessageId)
                ->orderBy('created_at', 'asc')
                ->get();
            
            $formattedMessages = [];
            
            foreach ($messages as $message) {
                $isSender = $message->sender_id == $user_id;
                
                // Cek apakah ada reply
                $replyData = null;
                if ($message->reply_to) {
                    $replyMessage = Message::find($message->reply_to);
                    if ($replyMessage) {
                        $replySender = $replyMessage->sender_id == $user_id ? 'me' : 'other';
                        $replyData = [
                            'id' => $replyMessage->id,
                            'text' => $replyMessage->message,
                            'sender' => $replySender,
                        ];
                        
                        // Tambahkan gambar jika ada
                        if ($replyMessage->image) {
                            $replyData['image'] = asset($replyMessage->image);
                        }
                    }
                }
                
                $messageData = [
                    'id' => $message->id,
                    'text' => $message->message,
                    'time' => date('H:i', strtotime($message->created_at)),
                    'sender' => $isSender ? 'me' : 'other',
                    'read' => $message->is_read,
                    'created_at' => $message->created_at->toIso8601String()
                ];
                
                // Tambahkan gambar jika ada
                if ($message->image) {
                    $messageData['image'] = asset($message->image);
                }
                
                // Tambahkan dokumen jika ada
                if ($message->document_path) {
                    $messageData['document'] = [
                        'id' => $message->id,
                        'name' => $message->document_name ?? basename($message->document_path),
                        'size' => $message->document_size ?? $this->formatFileSize(file_exists(public_path($message->document_path)) ? filesize(public_path($message->document_path)) : 0),
                        'type' => $message->document_type ?? pathinfo($message->document_path, PATHINFO_EXTENSION),
                        'url' => asset($message->document_path),
                        'download_url' => route('rt.chat.download-document', ['id' => $message->id])
                    ];
                }
                
                // Tambahkan data reply jika ada
                if ($replyData) {
                    $messageData['replyTo'] = $replyData;
                }
                
                $formattedMessages[] = $messageData;
            }
            
            // Tandai pesan yang baru saja diambil sebagai sudah dibaca
            Message::where('chat_id', $id)
                ->where('sender_id', '!=', $user_id)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);
            
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
     * Tandai pesan sebagai sudah dibaca (API)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $chat = Chat::findOrFail($id);
            $user_id = Auth::user()->users_id;
            
            // Pastikan pengguna adalah bagian dari chat ini
            if ($chat->user1_id != $user_id && $chat->user2_id != $user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke chat ini'
                ]);
            }
            
            // Tandai semua pesan sebagai sudah dibaca
            Message::where('chat_id', $id)
                ->where('sender_id', '!=', $user_id)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);
            
            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in markAsRead: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus chat (API)
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteChat($id)
    {
        try {
            $chat = Chat::findOrFail($id);
            $user_id = Auth::user()->users_id;
            
            // Pastikan pengguna adalah bagian dari chat ini
            if ($chat->user1_id != $user_id && $chat->user2_id != $user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke chat ini'
                ]);
            }
            
            // Hapus semua pesan dalam chat secara permanen
            $messages = Message::where('chat_id', $id)->get();
            $totalFilesDeleted = 0;
            
            foreach ($messages as $message) {
                // Hapus file gambar terkait jika ada
                if ($message->image_path && file_exists(public_path($message->image_path))) {
                    unlink(public_path($message->image_path));
                    $totalFilesDeleted++;
                }
                
                // Hapus file dokumen terkait jika ada
                if ($message->document_path && file_exists(public_path($message->document_path))) {
                    unlink(public_path($message->document_path));
                    $totalFilesDeleted++;
                }
                
                // Hapus pesan secara permanen
                $message->forceDelete();
            }
            
            // Hapus chat
            $chat->delete();
            
            $fileMessage = $totalFilesDeleted > 0 ? " dan {$totalFilesDeleted} file media" : "";
            
            return response()->json([
                'success' => true,
                'message' => "Chat dan semua pesannya{$fileMessage} telah dihapus"
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in deleteChat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan daftar chat (API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChatList()
    {
        try {
            $user_id = Auth::user()->users_id;
            
            // Ambil daftar chat dari database
            $chats = Chat::where('user1_id', $user_id)
                ->orWhere('user2_id', $user_id)
                ->orderBy('last_activity', 'desc') // Gunakan last_activity bukan updated_at
                ->get();
            
            $chatList = [];
            
            foreach ($chats as $chat) {
                // Tentukan ID pengguna lain dalam chat
                $otherUserId = ($chat->user1_id == $user_id) ? $chat->user2_id : $chat->user1_id;
                
                // Ambil data pengguna lain
                $otherUser = User::where('users_id', $otherUserId)->first();
                
                if ($otherUser) {
                    // Ambil pesan terakhir
                    $lastMessage = Message::where('chat_id', $chat->id)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    // Hitung jumlah pesan yang belum dibaca
                    $unreadCount = Message::where('chat_id', $chat->id)
                        ->where('sender_id', '!=', $user_id)
                        ->where('is_read', 0)
                        ->count();
                    
                    // Format waktu pesan terakhir
                    $time = $lastMessage ? $this->formatTime($lastMessage->created_at) : '';
                    
                    // Siapkan data untuk response
                    $chatList[] = [
                        'id' => $chat->id,
                        'name' => $otherUser->nama,
                        'avatar' => 'https://ionicframework.com/docs/img/demos/avatar.svg',
                        'lastMessage' => $lastMessage ? $lastMessage->message : 'Mulai percakapan',
                        'time' => $time,
                        'unread' => $unreadCount,
                        'muted' => false, // Tambahkan fitur mute nanti
                        'other_user_id' => $otherUser->users_id, // Tambahkan ID pengguna lain
                        'is_online' => $otherUser->isOnline(), // Tambahkan status online
                        'last_active_timestamp' => $lastMessage ? $lastMessage->created_at->toIso8601String() : null
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'chats' => $chatList
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
     * Membuat chat baru
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createChat(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,users_id'
        ]);
        
        $user_id = Auth::user()->users_id;
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
            return response()->json([
                'success' => true,
                'chat_id' => $existingChat->id,
                'message' => 'Chat sudah ada'
            ]);
        }
        
        // Buat chat baru
        $chat = new Chat();
        $chat->chat_id = \Illuminate\Support\Str::uuid()->toString();
        $chat->user1_id = $user_id;
        $chat->user2_id = $other_user_id;
        $chat->last_activity = now();
        $chat->save();
        
        return response()->json([
            'success' => true,
            'chat_id' => $chat->id,
            'message' => 'Chat berhasil dibuat'
        ]);
    }

    /**
     * Mendapatkan daftar pengguna yang tersedia untuk chat
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableUsers()
    {
        try {
            $user_id = Auth::user()->users_id;
            
            // Ambil semua pengguna kecuali diri sendiri
            $users = User::where('users_id', '!=', $user_id)
                        ->select('users_id', 'nama as name', 'email', 'role')
                        ->orderBy('nama')
                        ->get();
            
            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getAvailableUsers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * Format waktu untuk tampilan
     *
     * @param  \Carbon\Carbon  $dateTime
     * @return string
     */
    private function formatTime($dateTime)
    {
        $now = now();
        $yesterday = now()->subDay();
        
        if ($dateTime->isSameDay($now)) {
            return $dateTime->format('H:i');
        } elseif ($dateTime->isSameDay($yesterday)) {
            return 'Kemarin';
        } else {
            return $dateTime->format('d/m');
        }
    }

    /**
     * Hapus pesan (API)
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMessage($id)
    {
        try {
            $message = Message::findOrFail($id);
            $user_id = Auth::user()->users_id;
            
            // Pastikan pengguna adalah pengirim pesan
            if ($message->sender_id != $user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya dapat menghapus pesan yang Anda kirim'
                ], 403);
            }
            
            // Hapus pesan secara permanen dari database
            $message->forceDelete();
            
            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in deleteMessage: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
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
            $message = Message::findOrFail($id);
            $user_id = Auth::user()->users_id;
            $chat = Chat::findOrFail($message->chat_id);
            
            // Pastikan pengguna adalah bagian dari chat ini
            if ($chat->user1_id != $user_id && $chat->user2_id != $user_id) {
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
            $isMobile = preg_match('/(Mobile|Android|iPhone|iPad|iPod)/i', $userAgent);
            
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
     * Hapus semua pesan dalam chat
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearChat($id)
    {
        try {
            $chat = Chat::findOrFail($id);
            $user_id = Auth::user()->users_id;
            
            // Pastikan pengguna adalah bagian dari chat ini
            if ($chat->user1_id != $user_id && $chat->user2_id != $user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke chat ini'
                ]);
            }
            
            // Hapus semua pesan dalam chat secara permanen
            $messages = Message::where('chat_id', $id)->get();
            $totalFilesDeleted = 0;
            
            foreach ($messages as $message) {
                // Hapus file gambar terkait jika ada
                if ($message->image_path && file_exists(public_path($message->image_path))) {
                    unlink(public_path($message->image_path));
                    $totalFilesDeleted++;
                }
                
                // Hapus file dokumen terkait jika ada
                if ($message->document_path && file_exists(public_path($message->document_path))) {
                    unlink(public_path($message->document_path));
                    $totalFilesDeleted++;
                }
                
                // Hapus pesan secara permanen
                $message->forceDelete();
            }
            
            // Update waktu aktivitas terakhir
            $chat->last_activity = now();
            $chat->save();
            
            $fileMessage = $totalFilesDeleted > 0 ? " dan {$totalFilesDeleted} file media" : "";
            
            return response()->json([
                'success' => true,
                'message' => "Semua pesan{$fileMessage} telah dihapus"
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in clearChat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
