@extends('RTtemplate')

@section('title', 'Chat Detail')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/rt/chat.css') }}">
<style>
    .chat-container {
        height: calc(100vh - 250px);
        min-height: 500px;
        overflow-y: auto;
        padding: 20px;
        background-color: #e5ddd5;
        background-image: 
            linear-gradient(to bottom, rgba(0, 0, 0, 0.02) 25%, transparent 25%, transparent 50%, 
            rgba(0, 0, 0, 0.02) 50%, rgba(0, 0, 0, 0.02) 75%, transparent 75%, transparent);
        background-size: 20px 20px;
    }
    
    .message {
        display: flex;
        margin-bottom: 15px;
    }
    
    .message-incoming {
        justify-content: flex-start;
    }
    
    .message-outgoing {
        justify-content: flex-end;
    }
    
    .message-bubble {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 15px;
        position: relative;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    }
    
    .message-incoming .message-bubble {
        background-color: #ffffff;
        border-radius: 15px 15px 15px 0;
    }
    
    .message-outgoing .message-bubble {
        background-color: #dcf8c6;
        border-radius: 15px 15px 0 15px;
    }
    
    .message-text {
        margin-bottom: 5px;
        word-wrap: break-word;
    }
    
    .message-time {
        font-size: 0.75rem;
        color: #7d7d7d;
        text-align: right;
    }
    
    .message-status {
        margin-left: 5px;
        color: #4fc3f7;
    }
    
    .chat-input-container {
        background-color: #f0f0f0;
        padding: 10px;
        border-top: 1px solid #e0e0e0;
        position: sticky;
        bottom: 0;
    }
    
    .chat-input-wrapper {
        display: flex;
        align-items: center;
        background-color: #fff;
        border-radius: 20px;
        padding: 5px 15px;
    }
    
    .chat-input {
        flex: 1;
        border: none;
        padding: 8px 0;
        outline: none;
        resize: none;
        max-height: 100px;
        overflow-y: auto;
    }
    
    .chat-send-btn {
        background-color: transparent;
        border: none;
        color: #0099ff;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0 5px;
    }
    
    .chat-attachment-btn {
        background-color: transparent;
        border: none;
        color: #666;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0 5px;
    }
    
    .typing-indicator {
        display: flex;
        padding: 10px;
    }
    
    .typing-indicator span {
        height: 8px;
        width: 8px;
        background-color: #999;
        border-radius: 50%;
        margin: 0 2px;
        display: inline-block;
        animation: typing 1s infinite;
    }
    
    .typing-indicator span:nth-child(1) {
        animation-delay: 0s;
    }
    
    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }
    
    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }
    
    @keyframes typing {
        0% { opacity: 0.3; }
        50% { opacity: 1; }
        100% { opacity: 0.3; }
    }
    
    .message-image {
        max-width: 250px;
        max-height: 200px;
        border-radius: 10px;
        margin-bottom: 5px;
        cursor: pointer;
    }
    
    .date-divider {
        text-align: center;
        margin: 20px 0;
    }
    
    .date-divider span {
        background-color: rgba(225, 245, 254, 0.9);
        color: #505050;
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 16px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    }
    
    .reply-container {
        background-color: rgba(0, 0, 0, 0.04);
        border-radius: 10px;
        padding: 8px;
        margin-bottom: 8px;
        position: relative;
        border-left: 4px solid #0099ff;
    }
    
    .reply-text {
        font-size: 0.85rem;
        color: #666;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .reply-sender {
        font-weight: 600;
        font-size: 0.85rem;
        color: #0099ff;
    }
    
    /* Reply Preview in Input */
    .reply-preview {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #e3f2fd;
        border-left: 4px solid #0099ff;
        padding: 8px 12px;
        margin-bottom: 8px;
        border-radius: 4px;
    }
    
    .reply-preview-content {
        flex: 1;
        overflow: hidden;
    }
    
    .reply-preview-sender {
        font-weight: 600;
        font-size: 0.85rem;
        color: #0099ff;
        margin-bottom: 2px;
    }
    
    .reply-preview-text {
        font-size: 0.85rem;
        color: #666;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Document styling */
    .document-container {
        display: flex;
        align-items: center;
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
    }
    
    .document-icon {
        width: 40px;
        height: 40px;
        background-color: #0099ff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }
    
    .document-icon i {
        font-size: 20px;
        color: white;
    }
    
    .document-info {
        flex: 1;
        overflow: hidden;
    }
    
    .document-name {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .document-size {
        font-size: 12px;
        color: #666;
    }
    
    .document-actions {
        display: flex;
        gap: 5px;
        margin-left: 10px;
    }
    
    .document-actions .btn {
        padding: 3px 8px;
        font-size: 12px;
    }
    
    .document-actions .btn:hover {
        opacity: 0.8;
    }
    
    /* Notification for new messages */
    #newMessageNotification {
        position: absolute;
        bottom: 70px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #0099ff;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        display: none;
        z-index: 10;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('rt.chat.index') }}" class="me-3 text-white">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div class="d-flex align-items-center">
                            <div class="avatar-container me-2 position-relative">
                                <img src="{{ $otherUser['avatar'] }}" alt="Avatar" class="avatar rounded-circle" style="width: 40px; height: 40px;">
                                <span class="status-indicator {{ $otherUser['is_online'] ? 'bg-success' : 'bg-secondary' }} position-absolute" 
                                      data-bs-toggle="tooltip" 
                                      data-bs-placement="bottom" 
                                      title="{{ $otherUser['is_online'] ? 'Online' : 'Offline' }}"></span>
                            </div>
                            <div>
                                <h6 class="mb-0 text-white">{{ $otherUser['name'] }}</h6>
                                <small class="status text-white-50" 
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="bottom" 
                                       title="Status terakhir: {{ $otherUser['status'] }}">
                                    {{ $otherUser['is_online'] ? 'Online' : $otherUser['status'] }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="dropdown">
                            <button class="btn text-white" type="button" id="chatOptionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chatOptionsDropdown">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-search me-2"></i> Cari</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-volume-mute me-2"></i> Bisukan Notifikasi</a></li>
                                <li><a class="dropdown-item" href="#" onclick="clearChat('{{ $chatId }}')"><i class="fas fa-eraser me-2"></i> Hapus Semua Pesan</a></li>
                                <li><a class="dropdown-item" href="#" onclick="deleteChat('{{ $chatId }}')"><i class="fas fa-trash me-2"></i> Hapus Chat</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-flag me-2"></i> Laporkan</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="chat-container" id="chatContainer">
                    <!-- Notifikasi pesan baru -->
                    <div id="newMessageNotification" class="new-message-notification" style="display: none;">
                        <div class="alert alert-primary alert-dismissible fade show" role="alert">
                            <i class="fas fa-arrow-down me-2"></i> Pesan baru
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                    
                    <div id="chatMessages">
                        @if(count($messages) > 0)
                            <!-- Tanggal -->
                            <div class="date-divider">
                                <span>{{ date('l, d F Y') }}</span>
                            </div>
                            
                            @foreach($messages as $message)
                                <div class="message message-{{ $message['sender'] === 'me' ? 'outgoing' : 'incoming' }}">
                                    <div class="message-bubble" data-message-id="{{ $message['id'] }}">
                                        <div class="message-header">
                                            <div class="message-options">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="#" onclick="replyToMessage(this)"><i class="fas fa-reply me-2"></i>Balas</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="copyMessage(this)"><i class="fas fa-copy me-2"></i>Salin</a></li>
                                                        @if($message['sender'] === 'me')
                                                            <li><a class="dropdown-item" href="#" onclick="editMessage(this)"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteMessage(this)"><i class="fas fa-trash me-2"></i>Hapus</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if(isset($message['replyTo']))
                                            <div class="reply-container">
                                                <div class="reply-sender">{{ $message['replyTo']['sender'] === 'me' ? 'Anda' : $otherUser['name'] }}</div>
                                                <div class="reply-text">{{ $message['replyTo']['text'] }}</div>
                                                @if(isset($message['replyTo']['image']))
                                                    <img src="{{ asset($message['replyTo']['image']) }}" class="reply-image" alt="Reply Image" style="max-width: 50px; max-height: 50px; border-radius: 4px; margin-top: 4px;">
                                                @endif
                                            </div>
                                        @endif
                                        
                                        @if($message['text'])
                                            <div class="message-text">{{ $message['text'] }}</div>
                                        @endif
                                        
                                        @if(isset($message['image']))
                                            @php
                                                $img = $message['image'];
                                                $isAbsolute = Str::startsWith($img, ['http://', 'https://']);
                                            @endphp
                                            <img src="{{ $isAbsolute ? $img : asset($img) }}" alt="Image" class="message-image" onclick="openImageViewer('{{ $isAbsolute ? $img : asset($img) }}')">
                                        @endif
                                        
                                        @if(isset($message['document']))
                                            <div class="document-container">
                                                <div class="document-icon">
                                                    <i class="fas fa-file-{{ $message['document']['type'] === 'pdf' ? 'pdf' : ($message['document']['type'] === 'doc' || $message['document']['type'] === 'docx' ? 'word' : 'alt') }}"></i>
                                                </div>
                                                <div class="document-info">
                                                    <div class="document-name">{{ $message['document']['name'] }}</div>
                                                    <div class="document-size">{{ $message['document']['size'] }}</div>
                                                </div>
                                                <div class="document-actions">
                                                    @php
                                                        $docUrl = $message['document']['url'];
                                                        $isDocAbsolute = Str::startsWith($docUrl, ['http://', 'https://']);
                                                    @endphp
                                                    <a href="{{ $isDocAbsolute ? $docUrl : asset($docUrl) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Buka">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                    <a href="{{ $message['document']['download_url'] }}" class="btn btn-sm btn-outline-success" title="Unduh">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="message-time" data-timestamp="{{ $message['created_at'] }}">
                                            {{ $message['time'] }}
                                            @if($message['sender'] === 'me')
                                                <i class="fas fa-{{ $message['read'] ? 'check-double' : 'check' }} message-status"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center my-5">
                                <p class="text-muted">Belum ada pesan. Mulai percakapan sekarang!</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Notifikasi pesan baru -->
                    <div id="newMessageNotification">
                        <i class="fas fa-arrow-down me-2"></i> Pesan baru
                    </div>
                </div>
                
                <div class="chat-input-container">
                    <!-- Preview balasan -->
                    <div class="reply-preview" id="replyPreview" style="display: none;">
                        <div class="reply-preview-content">
                            <div class="reply-preview-sender"></div>
                            <div class="reply-preview-text"></div>
                            <img class="reply-image" style="display:none; max-width: 50px; max-height: 40px; border-radius: 4px; margin-top: 4px;" />
                        </div>
                        <button type="button" class="btn-close btn-sm" onclick="cancelReply()"></button>
                    </div>
                    
                    <!-- Preview gambar -->
                    <div id="imagePreviewContainer" class="mb-2" style="display: none;">
                        <div class="position-relative d-inline-block">
                            <img src="" id="imagePreview" alt="Preview" style="max-height: 100px; max-width: 200px; border-radius: 8px;">
                            <div id="imagePreviewLoading" class="upload-overlay" style="border-radius: 8px;">
                                <div class="spinner-border text-primary spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-sm position-absolute top-0 end-0 bg-white rounded-circle" style="margin: 5px; z-index: 2;" onclick="$('#imageInput').val(''); $('#imagePreviewContainer').hide();"></button>
                        </div>
                    </div>
                    
                    <!-- Preview dokumen -->
                    <div id="documentInfo" class="alert alert-info mb-2" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span id="documentIcon"><i class="fas fa-file"></i></span>
                                <span id="documentName">document.pdf</span>
                                <small id="documentSize" class="text-muted d-block">123 KB</small>
                            </div>
                            <button type="button" class="btn-close btn-sm" onclick="$('#documentInput').val(''); $('#documentInfo').hide();"></button>
                        </div>
                    </div>
                    
                    <div class="chat-input-wrapper">
                        <button class="chat-attachment-btn" id="attachmentBtn">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <textarea class="chat-input" placeholder="Ketik pesan..." rows="1"></textarea>
                        <button class="chat-send-btn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    
                    <!-- Hidden inputs untuk file upload -->
                    <input type="file" id="imageInput" style="display: none;" accept="image/*" onchange="previewUploadImage(this)">
                    <input type="file" id="documentInput" style="display: none;" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" onchange="showDocumentInfo(this)">
                    
                    <!-- Hidden input untuk reply -->
                    <input type="hidden" id="replyMessageId" value="">
                    
                    <!-- Hidden input untuk last message ID -->
                    <input type="hidden" id="lastMessageId" value="{{ count($messages) > 0 ? $messages[count($messages) - 1]['id'] : '' }}">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Gambar -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewModalLabel">Preview Gambar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="previewImage" class="img-fluid" alt="Preview">
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Pesan -->
<div class="modal fade" id="editMessageModal" tabindex="-1" aria-labelledby="editMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMessageModalLabel">Edit Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMessageForm">
                    <div class="mb-3">
                        <label for="editMessageText" class="form-label">Pesan</label>
                        <textarea class="form-control" id="editMessageText" rows="3"></textarea>
                    </div>
                    <input type="hidden" id="editMessageId" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveEditBtn" onclick="saveEditedMessage()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Dropdown Menu Attachment -->
<div class="dropdown-menu" id="attachmentMenu">
    <a class="dropdown-item" href="#" id="uploadImageOption">
        <i class="fas fa-image me-2"></i> Gambar
    </a>
    <a class="dropdown-item" href="#" id="uploadDocumentOption">
        <i class="fas fa-file me-2"></i> Dokumen
    </a>
</div>

<!-- Audio untuk notifikasi pesan -->
<audio id="notificationSound" preload="auto">
    <source src="{{ asset('sound/chat.mp3') }}" type="audio/mpeg">
</audio>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>
@endsection

@section('scripts')
<script src="{{ asset('js/rt/chat.js') }}"></script>
<script>
    // Inisialisasi variabel dan function
    const chatId = {{ $chatId }};
    let lastMessageId = {{ count($messages) > 0 ? $messages[count($messages) - 1]['id'] : 0 }};
    let messagePollingInterval = null;
    
    // Mulai pengecekan status online/offline pengguna
    const otherUserId = '{{ $otherUser['id'] }}';
    let statusCheckingInterval = null;
    
    // Variabel untuk mencegah pengiriman pesan ganda
    window.isSendingMessage = false;
    
    // Fungsi untuk menginisialisasi chat
    function initializeChat() {
        // Scroll ke pesan terbaru
        scrollToBottom();
        
        // Mulai polling pesan baru
        startMessagePolling(chatId, lastMessageId, 5000);
        
        // Mulai pengecekan status online
        statusCheckingInterval = startStatusChecking(otherUserId, 30000);
    }
    
    // Document ready
    $(document).ready(function() {
        initializeChat();
        
        // Inisialisasi tooltip
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Konversi waktu ke zona waktu lokal
        updateAllChatTimes();
        
        // Event listener untuk form pesan
        $('#chatForm').on('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });
        
        // Event listener untuk tombol kirim
        $('.chat-send-btn').on('click', function(e) {
            e.preventDefault();
            sendMessage();
        });
        
        // Event listener untuk mengirim pesan dengan tombol Enter (tanpa shift)
        $('.chat-input').on('keydown', function(e) {
            // Kirim pesan dengan Enter tanpa shift
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        // Auto-resize textarea
        $('.chat-input').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Notifikasi pesan baru
        $('#newMessageNotification').on('click', function() {
            scrollToBottom();
            hideNewMessageNotification();
        });
        
        // Inisialisasi dropdown attachment
        $('#attachmentBtn').on('click', function(e) {
            e.preventDefault();
            $('#attachmentMenu').css({
                display: 'block',
                position: 'absolute',
                bottom: '60px',
                left: '10px'
            }).addClass('show');
        });
        
        // Opsi upload gambar
        $('#uploadImageOption').on('click', function(e) {
            e.preventDefault();
            $('#attachmentMenu').removeClass('show').hide();
            $('#imageInput').click();
        });
        
        // Opsi upload dokumen
        $('#uploadDocumentOption').on('click', function(e) {
            e.preventDefault();
            $('#attachmentMenu').removeClass('show').hide();
            $('#documentInput').click();
        });
        
        // Tutup dropdown attachment saat klik di luar
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#attachmentBtn, #attachmentMenu').length) {
                $('#attachmentMenu').removeClass('show').hide();
            }
        });
        
        // Tandai pesan sebagai sudah dibaca saat halaman dimuat
        markAsRead(chatId);
        
        // Matikan atau reset suara notifikasi
        resetNotificationSound();
    });
    
    // Cleanup saat halaman ditutup
    $(window).on('beforeunload', function() {
        if (messagePollingInterval) {
            clearInterval(messagePollingInterval);
        }
        
        if (statusCheckingInterval) {
            clearInterval(statusCheckingInterval);
        }
    });
    
    // Fungsi untuk mengirim pesan
    function sendMessage() {
        const messageText = $('.chat-input').val().trim();
        const hasImage = $('#imageInput').val() !== '';
        const hasDocument = $('#documentInput').val() !== '';
        
        // Validasi: harus ada teks, gambar, atau dokumen
        if (!messageText && !hasImage && !hasDocument) {
            return;
        }
        
        // Cek apakah tombol kirim sedang disabled (sedang dalam proses pengiriman)
        if ($('.chat-send-btn').prop('disabled')) {
            return;
        }
        
        // Kirim pesan
        sendChatMessage(chatId, messageText);
    }
    
    // Fungsi untuk mematikan atau mereset suara notifikasi
    function resetNotificationSound() {
        const sound = document.getElementById('notificationSound');
        if (sound) {
            sound.pause();
            sound.currentTime = 0;
        }
    }
</script>
@endsection
