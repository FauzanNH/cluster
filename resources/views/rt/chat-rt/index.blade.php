@extends('RTtemplate')

@section('title', 'Chat')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/rt/chat.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i> Chat
                    </h5>
                    <div class="d-flex">
                        <div class="input-group me-3" style="width: 300px;">
                            <input type="text" class="form-control" id="searchChat" placeholder="Cari percakapan..." aria-label="Cari percakapan">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-primary"></i>
                            </span>
                        </div>
                        <button class="btn btn-light" id="btnNewChat" data-bs-toggle="tooltip" title="Chat Baru">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="chat-list-container">
                        <ul class="list-group list-group-flush chat-list">
                            @if(count($chats) > 0)
                                @foreach($chats as $chat)
                                <li class="list-group-item chat-item" onclick="window.location.href='{{ route('rt.chat.viewchat', ['id' => $chat['id']]) }}'" data-user-id="{{ $chat['other_user_id'] ?? '' }}" data-chat-id="{{ $chat['id'] }}">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-container me-3 position-relative">
                                            <img src="{{ $chat['avatar'] }}" alt="Avatar" class="avatar rounded-circle">
                                            <span class="status-badge {{ $chat['is_online'] ? 'bg-success' : 'bg-secondary' }} position-absolute" title="{{ $chat['is_online'] ? 'Online' : 'Offline' }}"></span>
                                        </div>
                                        <div class="chat-info flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 {{ $chat['unread'] > 0 ? 'fw-bold' : '' }}">{{ $chat['name'] }}</h6>
                                                <small class="{{ $chat['unread'] > 0 ? 'text-primary fw-bold' : 'text-muted' }} chat-time" data-timestamp="{{ $chat['last_active_timestamp'] ?? '' }}">{{ $chat['time'] }}</small>
                                            </div>
                                            <p class="message-preview mb-0 text-truncate {{ $chat['unread'] > 0 ? '' : 'text-muted' }}">
                                                {{ $chat['lastMessage'] }}
                                            </p>
                                                                        <small class="text-muted status-text">
                                @if($chat['is_online'])
                                    <span class="text-success">Online</span>
                                @endif
                            </small>
                                        </div>
                                        @if($chat['unread'] > 0)
                                        <div class="chat-status ms-2">
                                            <span class="badge bg-primary rounded-pill">{{ $chat['unread'] }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </li>
                                @endforeach
                            @else
                                <li class="list-group-item text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-comments fa-3x mb-3 text-light"></i>
                                        <p>Belum ada percakapan</p>
                                        <button class="btn btn-sm btn-primary" id="btnStartChat">
                                            <i class="fas fa-plus me-1"></i> Mulai Percakapan
                                        </button>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audio untuk notifikasi pesan -->
<audio id="notificationSound" preload="auto">
    <source src="{{ asset('storage/sound/chat.mp3') }}" type="audio/mpeg">
</audio>

<!-- Modal Chat Baru -->
<div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="newChatModalLabel">Chat Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="searchContact" class="form-label">Cari Kontak</label>
                    <input type="text" class="form-control" id="searchContact" placeholder="Ketik nama atau ID pengguna...">
                </div>
                <div class="contact-list mt-3">
                    <div class="list-group" id="usersList">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat daftar pengguna...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/rt/chat.js') }}"></script>
<script>
    $(document).ready(function() {
        // Set flag untuk menandai halaman sedang loading
        sessionStorage.setItem('pageLoading', 'true');
        
        // Setelah semua resource dimuat, set flag menjadi false
        $(window).on('load', function() {
            setTimeout(function() {
                sessionStorage.setItem('pageLoading', 'false');
            }, 1000); // Tunggu 1 detik untuk memastikan semua proses loading selesai
        });
        
        // Inisialisasi tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Periksa dan minta izin untuk notifikasi dan autoplay audio
        checkNotificationPermission();
        
        // Inisialisasi status tracking
        setupUserStatusTracking();
        
        // Inisialisasi pengecekan status untuk setiap chat
        initializeStatusChecking();
        
        // Mulai polling untuk pembaruan chat
        startChatListPolling();
        
        // Konversi waktu ke zona waktu lokal
        updateAllChatTimes();
        
        // Buka modal chat baru
        $('#btnNewChat, #btnStartChat').click(function() {
            $('#newChatModal').modal('show');
            loadAvailableUsers();
        });
        
        // Filter chat berdasarkan pencarian
        $('#searchChat').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.chat-item').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        
        // Filter kontak berdasarkan pencarian
        $('#searchContact').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.contact-list .user-item').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        
        // Fungsi untuk memeriksa dan meminta izin notifikasi
        function checkNotificationPermission() {
            // Minta izin notifikasi browser jika belum diizinkan
            if (Notification && Notification.permission !== "granted" && Notification.permission !== "denied") {
                Notification.requestPermission();
            }
            
            // Coba memainkan suara sekali untuk mengizinkan autoplay audio
            try {
                const notificationSound = document.getElementById('notificationSound');
                if (notificationSound) {
                    // Atur volume ke 0 untuk pemutaran silent untuk memperoleh izin
                    notificationSound.volume = 0;
                    notificationSound.play().then(() => {
                        // Berhasil mendapatkan izin, kembalikan volume normal
                        notificationSound.pause();
                        notificationSound.currentTime = 0;
                        notificationSound.volume = 1;
                    }).catch(e => {
                        console.log('Autoplay audio tidak diizinkan, memerlukan interaksi pengguna.');
                    });
                }
            } catch (e) {
                console.error('Error saat memeriksa izin audio:', e);
            }
        }
        
        // Fungsi untuk inisialisasi pengecekan status online untuk setiap chat
        function initializeStatusChecking() {
            $('.chat-item').each(function() {
                const userId = $(this).data('user-id');
                if (userId) {
                    getUserStatus(userId).then(function(userData) {
                        if (userData && userData.success) {
                            updateChatItemStatus(userId, userData.is_online, userData.status_text);
                        }
                    });
                }
            });
            
            // Periksa status setiap 30 detik
            setInterval(function() {
                $('.chat-item').each(function() {
                    const userId = $(this).data('user-id');
                    if (userId) {
                        getUserStatus(userId).then(function(userData) {
                            if (userData && userData.success) {
                                updateChatItemStatus(userId, userData.is_online, userData.status_text);
                            }
                        });
                    }
                });
            }, 30000);
        }
        
        // Variabel untuk menyimpan interval polling
        var chatListPollingInterval;
        
        // Fungsi untuk memulai polling daftar chat (dengan optimasi performa)
        function startChatListPolling() {
            // Hentikan polling sebelumnya jika ada
            if (chatListPollingInterval) {
                clearInterval(chatListPollingInterval);
            }
            
            // Jalankan polling setiap 10 detik (lebih lama untuk mengurangi beban)
            chatListPollingInterval = setInterval(function() {
                fetchChatList();
            }, 10000);
            
            // Jalankan sekali saat halaman dimuat
            fetchChatList();
        }
        
        // Fungsi untuk mengambil daftar chat terbaru (dengan optimasi)
        function fetchChatList() {
            $.ajax({
                url: "{{ route('rt.chat.list') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        // Hanya update jika ada perubahan
                        updateChatList(response.chats);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Gagal mengambil daftar chat:", error);
                }
            });
        }
        
        // Fungsi untuk memperbarui daftar chat dengan performa yang lebih baik
        function updateChatList(chats) {
            if (chats.length === 0) {
                // Hanya update jika belum ada pesan kosong
                if (!$('.chat-list .text-center.py-5').length) {
                    $('.chat-list').html(`
                        <li class="list-group-item text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-comments fa-3x mb-3 text-light"></i>
                                <p>Belum ada percakapan</p>
                                <button class="btn btn-sm btn-primary" id="btnStartChat">
                                    <i class="fas fa-plus me-1"></i> Mulai Percakapan
                                </button>
                            </div>
                        </li>
                    `);
                    
                    // Tambahkan kembali event handler untuk tombol mulai chat
                    $('#btnStartChat').click(function() {
                        $('#newChatModal').modal('show');
                        loadAvailableUsers();
                    });
                }
                return;
            }
            
            // Hitung total pesan belum dibaca
            var totalUnread = 0;
            $.each(chats, function(index, chat) {
                totalUnread += chat.unread;
            });
            
            // Kirim event untuk memperbarui badge di navbar
            if (window.parent && window.parent !== window) {
                window.parent.postMessage({
                    type: 'updateUnreadCount',
                    count: totalUnread
                }, '*');
            }
            
            // Update chat items secara individual untuk menghindari re-render seluruh list
            $.each(chats, function(index, chat) {
                updateSingleChatItem(chat);
            });
            
            // Konversi waktu ke zona waktu lokal
            updateAllChatTimes();
        }
        
        // Fungsi untuk memperbarui satu item chat
        function updateSingleChatItem(chat) {
            // Cek apakah chat item sudah ada
            var existingItem = $(`.chat-item[data-chat-id="${chat.id}"]`);
            
            // Jika belum ada, tambahkan ke daftar
            if (existingItem.length === 0) {
                var chatUrl = "{{ route('rt.chat.viewchat', ['id' => 'CHAT_ID_PLACEHOLDER']) }}".replace('CHAT_ID_PLACEHOLDER', chat.id);
                
                var unreadBadge = chat.unread > 0 ? 
                    `<div class="chat-status ms-2">
                        <span class="badge bg-primary rounded-pill">${chat.unread}</span>
                    </div>` : '';
                
                var statusBadgeClass = chat.is_online ? 'bg-success' : 'bg-secondary';
                var statusText = chat.is_online ? 
                    `<small class="text-muted status-text"><span class="text-success">Online</span></small>` : 
                    `<small class="text-muted status-text"></small>`;
                
                var newItem = `
                <li class="list-group-item chat-item" onclick="window.location.href='${chatUrl}'" data-user-id="${chat.other_user_id || ''}" data-chat-id="${chat.id}">
                    <div class="d-flex align-items-center">
                        <div class="avatar-container me-3 position-relative">
                            <img src="${chat.avatar}" alt="Avatar" class="avatar rounded-circle">
                            <span class="status-badge ${statusBadgeClass} position-absolute" title="${chat.is_online ? 'Online' : 'Offline'}"></span>
                        </div>
                        <div class="chat-info flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 ${chat.unread > 0 ? 'fw-bold' : ''}">${chat.name}</h6>
                                <small class="${chat.unread > 0 ? 'text-primary fw-bold' : 'text-muted'} chat-time" data-timestamp="${chat.last_active_timestamp || ''}">${chat.time}</small>
                            </div>
                            <p class="message-preview mb-0 text-truncate ${chat.unread > 0 ? '' : 'text-muted'}">
                                ${chat.lastMessage}
                            </p>
                            ${statusText}
                        </div>
                        ${unreadBadge}
                    </div>
                </li>`;
                
                $('.chat-list').append(newItem);
            } else {
                // Jika sudah ada, update informasi yang berubah
                var unreadBadge = existingItem.find('.chat-status');
                if (chat.unread > 0) {
                    if (unreadBadge.length === 0) {
                        existingItem.find('.d-flex.align-items-center').append(`
                            <div class="chat-status ms-2">
                                <span class="badge bg-primary rounded-pill">${chat.unread}</span>
                            </div>
                        `);
                    } else {
                        unreadBadge.find('.badge').text(chat.unread);
                    }
                    existingItem.find('h6').addClass('fw-bold');
                    existingItem.find('.chat-time').addClass('text-primary fw-bold').removeClass('text-muted');
                    existingItem.find('.message-preview').removeClass('text-muted');
                } else {
                    unreadBadge.remove();
                    existingItem.find('h6').removeClass('fw-bold');
                    existingItem.find('.chat-time').removeClass('text-primary fw-bold').addClass('text-muted');
                    existingItem.find('.message-preview').addClass('text-muted');
                }
                
                // Update pesan terakhir
                existingItem.find('.message-preview').text(chat.lastMessage);
                
                // Update timestamp
                existingItem.find('.chat-time').attr('data-timestamp', chat.last_active_timestamp || '');
                
                // Update status online
                var statusBadge = existingItem.find('.status-badge');
                if (chat.is_online) {
                    statusBadge.removeClass('bg-secondary').addClass('bg-success').attr('title', 'Online');
                    existingItem.find('.status-text').html('<span class="text-success">Online</span>');
                } else {
                    statusBadge.removeClass('bg-success').addClass('bg-secondary').attr('title', 'Offline');
                    existingItem.find('.status-text').html('');
                }
            }
        }
        
        // Fungsi untuk update UI status pada daftar chat
        function updateChatItemStatus(userId, isOnline, statusText) {
            const chatItem = $(`.chat-item[data-user-id="${userId}"]`);
            if (chatItem.length) {
                const statusBadge = chatItem.find('.status-badge');
                if (statusBadge.length) {
                    if (isOnline) {
                        statusBadge.removeClass('bg-secondary').addClass('bg-success');
                        statusBadge.attr('title', 'Online');
                        chatItem.find('.status-text').html('<span class="text-success">Online</span>');
                    } else {
                        statusBadge.removeClass('bg-success').addClass('bg-secondary');
                        statusBadge.attr('title', statusText);
                        chatItem.find('.status-text').html('');
                    }
                }
            }
        }
        
        // Fungsi untuk memuat daftar pengguna yang tersedia
        function loadAvailableUsers() {
            $.ajax({
                url: "{{ route('rt.chat.available-users') }}",
                type: "GET",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        displayUsers(response.users);
                    } else {
                        $('#usersList').html('<div class="alert alert-danger">Gagal memuat daftar pengguna</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    $('#usersList').html('<div class="alert alert-danger">Error: ' + error + '<br>Detail: ' + xhr.responseText + '</div>');
                }
            });
        }
        
        // Fungsi untuk menampilkan daftar pengguna
        function displayUsers(users) {
            if (users.length === 0) {
                $('#usersList').html('<div class="alert alert-info">Tidak ada pengguna yang tersedia</div>');
                return;
            }
            
            var html = '';
            $.each(users, function(index, user) {
                var avatarUrl = user.profile_picture ? user.profile_picture : 'https://ionicframework.com/docs/img/demos/avatar.svg';
                var roleBadge = getRoleBadge(user.role);
                
                html += '<a href="#" class="list-group-item list-group-item-action user-item" data-user-id="' + user.users_id + '">';
                html += '<div class="d-flex align-items-center">';
                html += '<img src="' + avatarUrl + '" alt="Avatar" class="avatar rounded-circle me-3" style="width: 40px; height: 40px;">';
                html += '<div class="flex-grow-1">';
                html += '<div class="d-flex justify-content-between">';
                html += '<h6 class="mb-0">' + user.name + ' <small class="text-muted">(' + user.users_id + ')</small></h6>';
                html += roleBadge;
                html += '</div>';
                html += '<small class="text-muted">' + user.email + '</small>';
                html += '</div>';
                html += '</div>';
                html += '</a>';
            });
            
            $('#usersList').html(html);
            
            // Tambahkan event handler untuk memulai chat
            $('.user-item').click(function(e) {
                e.preventDefault();
                var userId = $(this).data('user-id');
                createNewChat(userId);
            });
        }
        
        // Fungsi untuk mendapatkan badge role
        function getRoleBadge(role) {
            var badgeClass = 'bg-secondary';
            
            if (role.toLowerCase() === 'warga') {
                badgeClass = 'bg-success';
            } else if (role.toLowerCase() === 'satpam') {
                badgeClass = 'bg-info';
            } else if (role.toLowerCase() === 'admin') {
                badgeClass = 'bg-danger';
            }
            
            return '<span class="badge ' + badgeClass + ' ms-2">' + role + '</span>';
        }
        
        // Fungsi untuk membuat chat baru
        function createNewChat(userId) {
            $.ajax({
                url: "{{ route('rt.chat.create') }}",
                type: "POST",
                data: {
                    user_id: userId,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        // Redirect ke halaman chat dengan ID yang benar
                        var chatUrl = "{{ route('rt.chat.viewchat', ['id' => ':id']) }}";
                        chatUrl = chatUrl.replace(':id', response.chat_id);
                        window.location.href = chatUrl;
                    } else {
                        alert('Gagal membuat chat: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Error: ' + error + '\nDetail: ' + xhr.responseText);
                }
            });
        }
    });
</script>
@endsection
