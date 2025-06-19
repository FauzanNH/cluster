/**
 * Chat functionality for RT panel
 */

// Fungsi untuk memformat waktu
function formatTime(date) {
    if (typeof date === 'string') {
        date = new Date(date);
    }
    
    // Gunakan toLocaleTimeString untuk mendapatkan waktu lokal
    return date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });
}

// Fungsi untuk memformat tanggal
function formatDate(date) {
    if (typeof date === 'string') {
        date = new Date(date);
    }
    
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    
    // Bandingkan tanggal dalam zona waktu lokal
    if (date.toLocaleDateString() === today.toLocaleDateString()) {
        return formatTime(date);
    } else if (date.toLocaleDateString() === yesterday.toLocaleDateString()) {
        return 'Kemarin';
    } else {
        // Format tanggal dengan toLocaleDateString untuk mendapatkan format lokal
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit'
        });
    }
}

// Fungsi untuk menandai pesan sebagai sudah dibaca
function markAsRead(chatId) {
    $.ajax({
        url: `/rt/chat/${chatId}/mark-read`,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('Pesan ditandai sudah dibaca');
        },
        error: function(xhr) {
            console.error('Gagal menandai pesan sebagai sudah dibaca', xhr);
        }
    });
}

// Fungsi untuk menghapus chat
function deleteChat(chatId) {
    // Gunakan SweetAlert jika tersedia
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Chat?',
            text: 'Seluruh percakapan ini akan dihapus secara permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim request DELETE ke server
                $.ajax({
                    url: `/rt/chat/${chatId}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('Chat telah dihapus');
                            // Redirect ke halaman daftar chat
                            setTimeout(() => {
                                window.location.href = '/rt/chat';
                            }, 1000);
                        } else {
                            showToast('Gagal menghapus chat: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Gagal menghapus chat';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showToast(errorMessage);
                    }
                });
            }
        });
    } else if (confirm('Apakah Anda yakin ingin menghapus chat ini? Semua pesan akan dihapus secara permanen.')) {
        // Kirim request DELETE ke server
        $.ajax({
            url: `/rt/chat/${chatId}`,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast('Chat telah dihapus');
                    // Redirect ke halaman daftar chat
                    setTimeout(() => {
                        window.location.href = '/rt/chat';
                    }, 1000);
                } else {
                    showToast('Gagal menghapus chat: ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Gagal menghapus chat';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showToast(errorMessage);
            }
        });
    }
}

// Variabel untuk mencegah pengiriman ganda
let isSendingMessage = false;
let lastSendTime = 0;

// Fungsi untuk mengirim pesan
function sendChatMessage(chatId, message) {
    // Cek apakah sedang dalam proses pengiriman, jika ya maka batalkan
    if (isSendingMessage) {
        return;
    }
    
    // Debounce pengiriman pesan (mencegah klik ganda)
    const now = Date.now();
    if (now - lastSendTime < 1000) { // 1 detik debounce
        console.log('Debounce: mencegah pengiriman ganda');
        return;
    }
    lastSendTime = now;
    
    // Set status sedang mengirim
    isSendingMessage = true;
    
    // Tampilkan indikator loading pada tombol kirim
    const $sendButton = $('.chat-send-btn');
    const originalContent = $sendButton.html();
    $sendButton.html('<i class="fas fa-spinner fa-spin"></i>');
    $sendButton.prop('disabled', true);
    
    const replyTo = $('#replyMessageId').val();
    const formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('message', message);
    
    // Tambahkan reply_to jika ada
    if (replyTo) {
        formData.append('reply_to', replyTo);
    }
    
    // Generate ID sementara untuk pesan
    const tempId = 'temp-' + Date.now();
    let tempMessageAdded = false;
    
    // Tambahkan gambar jika ada
    const imageInput = document.getElementById('imageInput');
    if (imageInput && imageInput.files.length > 0) {
        formData.append('image', imageInput.files[0]);
        
        // Tampilkan preview gambar dengan loading
        const reader = new FileReader();
        reader.onload = function(e) {
            // Tambahkan pesan dengan status loading
            appendLoadingMessage('image', tempId, {
                preview: e.target.result,
                caption: message
            });
            tempMessageAdded = true;
        };
        reader.readAsDataURL(imageInput.files[0]);
    }
    
    // Tambahkan dokumen jika ada
    const documentInput = document.getElementById('documentInput');
    if (documentInput && documentInput.files.length > 0) {
        const file = documentInput.files[0];
        formData.append('document', file);
        
        // Tampilkan dokumen dengan loading
        appendLoadingMessage('document', tempId, {
            name: file.name,
            size: formatFileSize(file.size),
            caption: message
        });
        tempMessageAdded = true;
    }
    
    // Jika tidak ada gambar/dokumen, tampilkan pesan teks dengan loading jika ada
    if (!tempMessageAdded && message) {
        // Tambahkan pesan teks biasa dengan ID sementara
        const messageData = {
            id: tempId,
            text: message,
            time: formatTime(new Date()),
            sender: 'me',
            read: false,
            isTemp: true // Flag untuk menandai pesan sementara
        };
        appendMessage(messageData);
        
        // Tambahkan kelas loading
        $(`[data-message-id="${tempId}"] .message-status`).addClass('uploading').html('<i class="fas fa-clock"></i>');
    }
    
    // Tunggu sebentar agar animasi loading terlihat
    setTimeout(() => {
        $.ajax({
            url: `/rt/chat/${chatId}/send`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Reset input
                    $('.chat-input').val('');
                    $('.chat-input').css('height', 'auto');
                    $('#imageInput').val('');
                    $('#documentInput').val('');
                    $('#imagePreviewContainer').hide();
                    $('#documentInfo').hide();
                    $('#replyPreview').hide();
                    $('#replyMessageId').val('');
                    
                    // Hapus pesan loading/sementara
                    $(`.message-bubble[data-message-id="${tempId}"], #temp-message-${tempId}`).closest('.message').remove();
                    
                    // Tambahkan pesan ke tampilan
                    appendMessage(response.data);
                    
                    // Scroll ke bawah
                    scrollToBottom();
                }
            },
            error: function(xhr, status, error) {
                console.error('Gagal mengirim pesan', xhr);
                let errorMsg = 'Gagal mengirim pesan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                // Hapus pesan loading dan tampilkan error
                $(`.message-bubble[data-message-id="${tempId}"], #temp-message-${tempId}`).closest('.message').remove();
                showToast(errorMsg);
            },
            complete: function() {
                // Reset status pengiriman dan tombol kirim
                isSendingMessage = false;
                $sendButton.html(originalContent);
                $sendButton.prop('disabled', false);
            }
        });
    }, 500);
}

// Fungsi untuk mengambil pesan baru
function fetchNewMessages(chatId, lastMessageId) {
    $.ajax({
        url: `/rt/chat/${chatId}/new-messages`,
        type: 'GET',
        data: {
            last_message_id: lastMessageId
        },
        success: function(response) {
            if (response.success && response.messages.length > 0) {
                // Tambahkan pesan baru ke tampilan
                let hasIncomingMessages = false;
                
                response.messages.forEach(function(message) {
                    appendMessage(message);
                    
                    // Periksa apakah ada pesan masuk (bukan dari kita)
                    if (message.sender !== 'me') {
                        hasIncomingMessages = true;
                    }
                });
                
                // Update last message ID
                lastMessageId = response.messages[response.messages.length - 1].id;
                $('#lastMessageId').val(lastMessageId);
                
                // Scroll ke bawah jika user sudah di bawah
                if (isScrolledToBottom()) {
                    scrollToBottom();
                } else {
                    // Tampilkan notifikasi ada pesan baru
                    showNewMessageNotification();
                }
                
                // Jika ada pesan masuk, perbarui notifikasi
                if (hasIncomingMessages) {
                    // Mainkan suara notifikasi jika halaman tidak aktif atau bukan halaman chat detail
                    playNotificationSound();
                    
                    // Kirim event untuk memperbarui badge di navbar dan sidebar
                    try {
                        // Coba kirim pesan ke parent window
                        if (window.parent && window.parent !== window) {
                            window.parent.postMessage({
                                type: 'updateUnreadCount',
                                forceUpdate: true
                            }, '*');
                        }
                        
                        // Juga update langsung ke sessionStorage untuk sinkronisasi antar tab
                        const currentUnread = parseInt(sessionStorage.getItem('totalUnreadMessages') || '0');
                        sessionStorage.setItem('totalUnreadMessages', currentUnread + 1);
                    } catch (e) {
                        console.error('Gagal mengirim notifikasi ke parent window:', e);
                    }
                    
                    // Tampilkan notifikasi browser jika halaman tidak aktif
                    if (document.hidden) {
                        // Gunakan Web Notification API jika diizinkan
                        if (Notification && Notification.permission === "granted") {
                            try {
                                const otherUserName = $('.chat-header h6').text().trim() || 'Seseorang';
                                const lastMessage = response.messages[response.messages.length - 1];
                                const messageText = lastMessage.text || (lastMessage.image ? 'Mengirim gambar' : 'Mengirim dokumen');
                                
                                const notification = new Notification("Pesan Baru", {
                                    body: `${otherUserName}: ${messageText.substring(0, 50)}${messageText.length > 50 ? '...' : ''}`,
                                    icon: "/favicon.ico",
                                    tag: 'chat-notification', // Menggabungkan notifikasi serupa
                                    renotify: true // Notifikasi baru akan menggantikan yang lama
                                });
                                
                                notification.onclick = function() {
                                    window.focus();
                                    scrollToBottom(); // Scroll ke pesan terbaru
                                    this.close();
                                };
                                
                                // Tutup notifikasi setelah 5 detik
                                setTimeout(() => notification.close(), 5000);
                            } catch (e) {
                                console.error('Gagal menampilkan notifikasi browser:', e);
                            }
                        }
                        // Minta izin notifikasi jika belum diizinkan
                        else if (Notification && Notification.permission !== "denied") {
                            Notification.requestPermission();
                        }
                        
                        // Tambahkan notifikasi di title halaman sebagai fallback
                        const currentTitle = document.title;
                        if (!currentTitle.includes('Pesan Baru')) {
                            document.title = `(New) Pesan Baru - ${currentTitle}`;
                            
                            // Reset title saat halaman mendapat fokus
                            const resetTitle = function() {
                                document.title = currentTitle;
                                window.removeEventListener('focus', resetTitle);
                            };
                            window.addEventListener('focus', resetTitle);
                        }
                    }
                    
                    // Juga tampilkan notifikasi dalam halaman
                    showNewMessageNotification();
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Gagal mengambil pesan baru', xhr);
            let errorMsg = 'Gagal mengambil pesan baru';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            // Jangan tampilkan toast untuk setiap error polling
            // Cukup log saja ke konsol
        }
    });
}

// Fungsi untuk mengambil daftar chat
function fetchChatList() {
    $.ajax({
        url: '/rt/chat/list',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const chatList = response.chats;
                let chatListHTML = '';
                
                // Hitung total pesan yang belum dibaca sebelum update
                let previousUnread = 0;
                $('.chat-item').each(function() {
                    const unreadBadge = $(this).find('.badge');
                    if (unreadBadge.length) {
                        previousUnread += parseInt(unreadBadge.text() || '0');
                    }
                });
                
                // Hitung total pesan yang belum dibaca dari respons baru
                let currentUnread = 0;
                if (chatList.length > 0) {
                    chatList.forEach(function(chat) {
                        currentUnread += (chat.unread || 0);
                    });
                }
                
                // Mainkan suara notifikasi jika jumlah pesan belum dibaca bertambah
                // dan halaman sudah selesai loading (bukan first load)
                const isFirstLoad = sessionStorage.getItem('chatListInitialized') !== 'true';
                if (currentUnread > previousUnread && previousUnread > 0 && !isFirstLoad) {
                    playNotificationSound();
                }
                
                // Setelah pertama kali load, set flag bahwa chat list sudah diinisialisasi
                sessionStorage.setItem('chatListInitialized', 'true');
                
                if (chatList.length > 0) {
                    chatList.forEach(function(chat) {
                        // Format waktu menggunakan zona waktu lokal
                        const localTime = chat.last_active_timestamp ? 
                            formatLocalTime(chat.last_active_timestamp, 'chat-time') : 
                            chat.time;
                        
                        chatListHTML += `
                        <li class="list-group-item chat-item" onclick="window.location.href='/rt/chat/viewchat/${chat.id}'" data-user-id="${chat.other_user_id || ''}">
                            <div class="d-flex align-items-center">
                                <div class="avatar-container me-3 position-relative">
                                    <img src="${chat.avatar}" alt="Avatar" class="avatar rounded-circle">
                                    <span class="status-badge ${chat.is_online ? 'bg-success' : 'bg-secondary'} position-absolute" title="${chat.is_online ? 'Online' : 'Offline'}"></span>
                                </div>
                                <div class="chat-info flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 ${chat.unread > 0 ? 'fw-bold' : ''}">${chat.name}</h6>
                                        <small class="${chat.unread > 0 ? 'text-primary fw-bold' : 'text-muted'} chat-time" data-timestamp="${chat.last_active_timestamp || ''}">${localTime}</small>
                                    </div>
                                    <p class="message-preview mb-0 text-truncate ${chat.unread > 0 ? '' : 'text-muted'}">
                                        ${chat.lastMessage}
                                    </p>
                                    <small class="text-muted status-text">
                                        ${chat.is_online ? '<span class="text-success">Online</span>' : ''}
                                    </small>
                                </div>
                                ${chat.unread > 0 ? 
                                    `<div class="chat-status ms-2">
                                        <span class="badge bg-primary rounded-pill">${chat.unread}</span>
                                    </div>` : 
                                    ''}
                            </div>
                        </li>`;
                    });
                } else {
                    chatListHTML = `
                    <li class="list-group-item text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-comments fa-3x mb-3 text-light"></i>
                            <p>Belum ada percakapan</p>
                            <button class="btn btn-sm btn-primary" id="btnStartChat">
                                <i class="fas fa-plus me-1"></i> Mulai Percakapan
                            </button>
                        </div>
                    </li>`;
                }
                
                $('.chat-list').html(chatListHTML);
                
                // Reinisialisasi event handler untuk tombol mulai chat
                $('#btnStartChat').click(function() {
                    $('#newChatModal').modal('show');
                    loadAvailableUsers();
                });
                
                // Update status online/offline
                initializeStatusChecking();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching chat list:', error);
        }
    });
}

// Fungsi untuk mengambil daftar pengguna yang tersedia untuk chat
function fetchAvailableUsers() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '/rt/chat/available-users',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    resolve(response.users);
                } else {
                    reject('Gagal mengambil daftar pengguna');
                }
            },
            error: function(xhr) {
                console.error('Gagal mengambil daftar pengguna', xhr);
                reject('Gagal mengambil daftar pengguna: ' + xhr.statusText);
            }
        });
    });
}

// Fungsi untuk membuat chat baru
function createNewChat(userId) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '/rt/chat/create',
            type: 'POST',
            data: {
                user_id: userId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    resolve(response);
                } else {
                    reject(response.message || 'Gagal membuat chat baru');
                }
            },
            error: function(xhr) {
                console.error('Gagal membuat chat baru', xhr);
                reject('Gagal membuat chat baru: ' + xhr.statusText);
            }
        });
    });
}

// Polling untuk pesan baru (dalam implementasi nyata, lebih baik menggunakan WebSocket)
let pollingInterval;

function startMessagePolling(chatId, lastMessageId, interval = 3000) {
    // Simpan ID pesan terakhir di hidden input
    if (!$('#lastMessageId').length) {
        $('body').append(`<input type="hidden" id="lastMessageId" value="${lastMessageId}">`);
    } else {
        $('#lastMessageId').val(lastMessageId);
    }
    
    // Hentikan polling sebelumnya jika ada
    stopMessagePolling();
    
    // Polling sesuai interval yang ditentukan (default 3 detik)
    pollingInterval = setInterval(function() {
        const currentLastMessageId = $('#lastMessageId').val();
        fetchNewMessages(chatId, currentLastMessageId);
    }, interval);
    
    // Simpan interval ID di hidden input
    if (!$('#pollingIntervalId').length) {
        $('body').append(`<input type="hidden" id="pollingIntervalId" value="${pollingInterval}">`);
    } else {
        $('#pollingIntervalId').val(pollingInterval);
    }
    
    // Jalankan sekali saat mulai polling
    fetchNewMessages(chatId, lastMessageId);
}

function stopMessagePolling() {
    // Hentikan interval polling global
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
    
    // Hentikan juga interval yang disimpan di hidden input (untuk kompatibilitas)
    const intervalId = $('#pollingIntervalId').val();
    if (intervalId) {
        clearInterval(parseInt(intervalId));
        $('#pollingIntervalId').val('');
    }
}

// Fungsi untuk memuat pesan chat
function loadChatMessages(chatId) {
    // Tandai semua pesan sebagai sudah dibaca
    markAsRead(chatId);
    
    // Mulai polling untuk pesan baru
    const lastMessageId = $('.message-bubble').last().data('message-id') || '0';
    startMessagePolling(chatId, lastMessageId);
    
    // Tambahkan event handler untuk input pesan
    $('.chat-input').on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            const message = $(this).val().trim();
            if (message) {
                sendChatMessage(chatId, message);
            }
        }
    });
    
    // Tambahkan event handler untuk tombol kirim
    $('.chat-send-btn').on('click', function() {
        const message = $('.chat-input').val().trim();
        if (message) {
            sendChatMessage(chatId, message);
        }
    });
    
    // Tambahkan event handler untuk scroll
    $('#chatContainer').on('scroll', function() {
        if (isScrolledToBottom()) {
            // Sembunyikan notifikasi pesan baru
            hideNewMessageNotification();
        }
    });
    
    // Tambahkan event handler untuk klik notifikasi pesan baru
    $('#newMessageNotification').on('click', function() {
        scrollToBottom();
        hideNewMessageNotification();
    });
    
    // Tambahkan event handler untuk tombol attachment
    $('#attachmentBtn').on('click', function() {
        $('#attachmentMenu').toggleClass('show');
    });
    
    // Sembunyikan menu attachment saat klik di luar
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#attachmentBtn').length && !$(e.target).closest('#attachmentMenu').length) {
            $('#attachmentMenu').removeClass('show');
        }
    });
}

// Fungsi untuk mengirim pesan dengan gambar
function sendImageMessage(chatId, imageData, caption) {
    console.log(`Sending image to chat ${chatId} with caption: ${caption}`);
    
    // Simulasi request ke server
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve({
                success: true,
                message: {
                    id: Date.now(),
                    text: caption || '',
                    image: imageData,
                    time: formatTime(new Date()),
                    sender: 'me',
                    read: false
                }
            });
        }, 500);
    });
}

// Fungsi untuk mengirim pesan dengan dokumen
function sendDocumentMessage(chatId, documentData, caption) {
    console.log(`Sending document to chat ${chatId} with caption: ${caption}`);
    
    // Simulasi request ke server
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve({
                success: true,
                message: {
                    id: Date.now(),
                    text: caption || '',
                    document: documentData,
                    time: formatTime(new Date()),
                    sender: 'me',
                    read: false
                }
            });
        }, 500);
    });
}

// Fungsi untuk membalas pesan
function replyToMessage(element) {
    const messageBubble = $(element).closest('.message-bubble');
    const messageId = messageBubble.data('message-id');
    const messageText = messageBubble.find('.message-text').text().trim();
    const messageSender = messageBubble.closest('.message').hasClass('message-outgoing') ? 'Anda' : $('.chat-header h5').text().trim();
    const messageImage = messageBubble.find('.message-image').attr('src');
    
    // Tampilkan preview balasan
    const replyPreview = $('#replyPreview');
    replyPreview.find('.reply-preview-text').text(messageText);
    replyPreview.find('.reply-preview-sender').text(messageSender);
    
    if (messageImage) {
        replyPreview.find('.reply-image').attr('src', messageImage).show();
    } else {
        replyPreview.find('.reply-image').hide();
    }
    
    replyPreview.show();
    $('#replyMessageId').val(messageId);
    
    // Focus ke input pesan
    $('.chat-input').focus();
}

// Fungsi untuk membatalkan balasan
function cancelReply() {
    $('#replyPreview').hide();
    $('#replyMessageId').val('');
}

// Fungsi untuk menyalin pesan
function copyMessage(element) {
    const messageBubble = $(element).closest('.message-bubble');
    const messageText = messageBubble.find('.message-text').text().trim();
    
    // Salin ke clipboard
    if (navigator.clipboard) {
        navigator.clipboard.writeText(messageText)
            .then(() => {
                showToast('Pesan disalin ke clipboard');
            })
            .catch(err => {
                console.error('Gagal menyalin teks: ', err);
                fallbackCopyText(messageText);
            });
    } else {
        fallbackCopyText(messageText);
    }
}

// Fallback untuk menyalin teks jika Clipboard API tidak tersedia
function fallbackCopyText(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    
    // Hindari scrolling ke bawah
    textArea.style.top = '0';
    textArea.style.left = '0';
    textArea.style.position = 'fixed';
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showToast('Pesan disalin ke clipboard');
        } else {
            showToast('Gagal menyalin pesan');
        }
    } catch (err) {
        console.error('Fallback: Gagal menyalin teks', err);
        showToast('Gagal menyalin pesan');
    }
    
    document.body.removeChild(textArea);
}

// Fungsi untuk menampilkan toast
function showToast(message) {
    const toast = $(`<div class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>`);
    
    $('.toast-container').append(toast);
    const bsToast = new bootstrap.Toast(toast[0], { delay: 2000 });
    bsToast.show();
    
    // Hapus toast dari DOM setelah disembunyikan
    toast.on('hidden.bs.toast', function() {
        toast.remove();
    });
}

// Fungsi untuk menghapus pesan
function deleteMessage(element) {
    const messageBubble = $(element).closest('.message-bubble');
    const messageId = messageBubble.data('message-id');
    const chatId = $('#chatId').val(); // Ambil chat ID dari input hidden
    
    // Tampilkan konfirmasi
    const messageText = messageBubble.find('.message-text').text().trim();
    const confirmText = messageText.length > 30 ? 
        messageText.substring(0, 30) + '...' : 
        messageText;
    
    // Gunakan SweetAlert atau Bootstrap modal jika tersedia
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Pesan?',
            text: 'Pesan ini akan dihapus secara permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim request ke server untuk menghapus pesan
                $.ajax({
                    url: `/rt/chat/message/${messageId}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Hapus pesan dari tampilan
                            messageBubble.closest('.message').fadeOut(300, function() {
                                $(this).remove();
                            });
                            
                            // Tampilkan toast
                            showToast('Pesan telah dihapus');
                        } else {
                            showToast('Gagal menghapus pesan: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Gagal menghapus pesan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showToast(errorMessage);
                    }
                });
            }
        });
    } else if (confirm('Apakah Anda yakin ingin menghapus pesan ini? Tindakan ini tidak dapat dibatalkan.')) {
        // Kirim request ke server untuk menghapus pesan
        $.ajax({
            url: `/rt/chat/message/${messageId}`,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Hapus pesan dari tampilan
                    messageBubble.closest('.message').fadeOut(300, function() {
                        $(this).remove();
                    });
                    
                    // Tampilkan toast
                    showToast('Pesan telah dihapus');
                } else {
                    showToast('Gagal menghapus pesan: ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Gagal menghapus pesan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showToast(errorMessage);
            }
        });
    }
}

// Fungsi untuk mengedit pesan
function editMessage(element) {
    const messageBubble = $(element).closest('.message-bubble');
    const messageText = messageBubble.find('.message-text').text().trim();
    const messageId = messageBubble.data('message-id');
    
    // Tampilkan modal edit
    $('#editMessageModal').find('textarea').val(messageText);
    $('#editMessageModal').find('#editMessageId').val(messageId);
    $('#editMessageModal').modal('show');
}

// Fungsi untuk menyimpan pesan yang diedit
function saveEditedMessage() {
    const messageId = $('#editMessageId').val();
    const newText = $('#editMessageModal').find('textarea').val().trim();
    
    if (!newText) {
        alert('Pesan tidak boleh kosong');
        return;
    }
    
    // Update pesan di tampilan
    const messageBubble = $(`.message-bubble[data-message-id="${messageId}"]`);
    messageBubble.find('.message-text').text(newText);
    
    // Tambahkan tanda edited
    if (!messageBubble.find('.edited-mark').length) {
        messageBubble.find('.message-time').append(' <span class="edited-mark">(edited)</span>');
    }
    
    // Tutup modal
    $('#editMessageModal').modal('hide');
    
    // TODO: Kirim request ke server untuk update pesan
}

// Fungsi untuk preview gambar yang akan diupload
function previewUploadImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        // Tampilkan loading indicator
        $('#imagePreviewContainer').show();
        $('#imagePreview').hide();
        $('#imagePreviewLoading').show();
        
        reader.onload = function(e) {
            const imagePreview = $('#imagePreview');
            imagePreview.attr('src', e.target.result);
            
            // Tampilkan gambar dan sembunyikan loading
            imagePreview.show();
            $('#imagePreviewLoading').hide();
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Fungsi untuk menampilkan info dokumen
function showDocumentInfo(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        const fileSize = formatFileSize(file.size);
        const fileType = getDocumentIcon(fileName);
        
        $('#documentName').text(fileName);
        $('#documentSize').text(fileSize);
        $('#documentIcon').html(`<i class="${fileType}"></i>`);
        $('#documentInfo').show();
    }
}

// Fungsi untuk mendapatkan ikon dokumen berdasarkan tipe file
function getDocumentIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    
    switch (ext) {
        case 'pdf':
            return 'fas fa-file-pdf';
        case 'doc':
        case 'docx':
            return 'fas fa-file-word';
        case 'xls':
        case 'xlsx':
            return 'fas fa-file-excel';
        case 'ppt':
        case 'pptx':
            return 'fas fa-file-powerpoint';
        case 'txt':
            return 'fas fa-file-alt';
        case 'zip':
        case 'rar':
            return 'fas fa-file-archive';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            return 'fas fa-file-image';
        default:
            return 'fas fa-file';
    }
}

// Fungsi untuk format ukuran file
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Fungsi untuk cek apakah container chat sudah di-scroll ke bawah
function isScrolledToBottom() {
    const container = document.getElementById('chatContainer');
    return container.scrollHeight - container.clientHeight <= container.scrollTop + 50;
}

// Fungsi untuk scroll container chat ke bawah
function scrollToBottom() {
    const container = document.getElementById('chatContainer');
    container.scrollTop = container.scrollHeight;
}

// Fungsi untuk menampilkan notifikasi pesan baru
function showNewMessageNotification() {
    const $notification = $('#newMessageNotification');
    
    // Jika notifikasi sudah ditampilkan, tidak perlu menampilkan lagi
    if ($notification.is(':visible')) {
        return;
    }
    
    // Tampilkan notifikasi dengan animasi
    $notification.fadeIn(300);
    
    // Tambahkan event listener untuk scroll otomatis saat notifikasi diklik
    $notification.off('click').on('click', function() {
        scrollToBottom();
        hideNewMessageNotification();
    });
}

// Fungsi untuk menyembunyikan notifikasi pesan baru
function hideNewMessageNotification() {
    const $notification = $('#newMessageNotification');
    $notification.fadeOut(300);
}

// Fungsi untuk tambahkan pesan ke tampilan
function appendMessage(message) {
    // Cek apakah pesan sudah ada di DOM
    if ($(`[data-message-id="${message.id}"]`).length > 0 && !message.isTemp) {
        return;
    }
    
    // Format waktu menggunakan zona waktu lokal
    const localTime = message.created_at ? formatLocalTime(message.created_at, 'time') : 
                     (message.isTemp ? '<i class="fas fa-clock"></i>' : formatTime(new Date()));
    
    // Tentukan kelas untuk pesan (incoming/outgoing)
    const messageClass = message.sender === 'me' ? 'message-outgoing' : 'message-incoming';
    const messageAlign = message.sender === 'me' ? 'justify-content-end' : 'justify-content-start';
    
    let replyHtml = '';
    if (message.replyTo) {
        const replySender = message.replyTo.sender === 'me' ? 'Anda' : $('.chat-header h6').text();
        const replyText = message.replyTo.text || '';
        
        // Periksa apakah URL gambar reply perlu ditambahkan domain
        let replyImageUrl = message.replyTo.image || '';
        if (replyImageUrl && !replyImageUrl.startsWith('http')) {
            const baseUrl = window.location.origin;
            replyImageUrl = `${baseUrl}/${replyImageUrl.replace(/^\//, '')}`;
        }
        
        const replyImage = replyImageUrl ? `<img src="${replyImageUrl}" class="reply-image" alt="Reply Image" style="max-width: 50px; max-height: 50px; border-radius: 4px; margin-top: 4px;">` : '';
        
        replyHtml = `
        <div class="reply-container reply-${message.sender === 'me' ? 'outgoing' : 'incoming'}">
            <div class="reply-sender">${replySender}</div>
            <div class="reply-text">${replyText}</div>
            ${replyImage}
        </div>`;
    }
    
    let messageContent = '';
    
    // Tambahkan teks pesan jika ada
    if (message.text) {
        messageContent += `<div class="message-text">${message.text}</div>`;
    }
    
    // Tambahkan gambar jika ada
    if (message.image) {
        // Periksa apakah URL gambar perlu ditambahkan domain
        let imageUrl = message.image;
        if (imageUrl && !imageUrl.startsWith('http')) {
            const baseUrl = window.location.origin;
            // Hilangkan slash di awal jika ada
            imageUrl = `${baseUrl}/${imageUrl.replace(/^\//, '')}`;
        }
        
        messageContent += `<img src="${imageUrl}" alt="Image" class="message-image" onclick="openImageViewer('${imageUrl}')" style="cursor: pointer;">`;
    }
    
    // Tambahkan dokumen jika ada
    if (message.document) {
        // Periksa apakah URL dokumen perlu ditambahkan domain
        let docUrl = message.document.url;
        if (docUrl && !docUrl.startsWith('http')) {
            const baseUrl = window.location.origin;
            docUrl = `${baseUrl}/${docUrl.replace(/^\//, '')}`;
        }
        
        const docIcon = getDocumentIcon(message.document.name);
        messageContent += `
        <div class="document-container">
            <div class="document-icon">
                <i class="fas fa-${docIcon}"></i>
            </div>
            <div class="document-info">
                <div class="document-name">${message.document.name}</div>
                <div class="document-size">${message.document.size}</div>
            </div>
            <div class="document-actions">
                <a href="${docUrl}" class="btn btn-sm btn-outline-primary" target="_blank" title="Buka">
                    <i class="fas fa-external-link-alt"></i>
                </a>
                <a href="/rt/chat/document/${message.id}" class="btn btn-sm btn-outline-success" title="Unduh">
                    <i class="fas fa-download"></i>
                </a>
            </div>
        </div>`;
    }
    
    // Status pesan (dibaca/belum)
    const messageStatus = message.sender === 'me' ? 
        `<i class="fas fa-${message.read ? 'check-double' : 'check'} message-status ${message.isTemp ? 'uploading' : ''}"></i>` : 
        '';
    
    // Jika pesan sementara, tampilkan indikator
    const tempClass = message.isTemp ? 'temp-message' : '';
    
    // Buat HTML untuk pesan
    const messageHtml = `
    <div class="message ${messageClass} ${tempClass}" data-message-id="${message.id}">
        <div class="message-bubble" data-message-id="${message.id}">
            <div class="message-header">
                <div class="message-options">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="replyToMessage(this)"><i class="fas fa-reply me-2"></i>Balas</a></li>
                            <li><a class="dropdown-item" href="#" onclick="copyMessage(this)"><i class="fas fa-copy me-2"></i>Salin</a></li>
                            ${message.sender === 'me' && !message.isTemp ? `<li><a class="dropdown-item" href="#" onclick="editMessage(this)"><i class="fas fa-edit me-2"></i>Edit</a></li>` : ''}
                            ${message.sender === 'me' && !message.isTemp ? `<li><a class="dropdown-item text-danger" href="#" onclick="deleteMessage(this)"><i class="fas fa-trash me-2"></i>Hapus</a></li>` : ''}
                        </ul>
                    </div>
                </div>
            </div>
            
            ${replyHtml}
            ${messageContent}
            
            <div class="message-time" data-timestamp="${message.created_at || ''}">
                ${localTime}
                ${messageStatus}
            </div>
        </div>
    </div>`;
    
    // Tambahkan pesan ke container
    $('#chatMessages').append(messageHtml);
    
    // Tambahkan event listener untuk klik gambar (alternatif)
    if (message.image) {
        $(`[data-message-id="${message.id}"] .message-image`).off('click').on('click', function() {
            openImageViewer(message.image);
        });
    }
    
    // Scroll ke bawah jika sudah di bawah
    if (isScrolledToBottom()) {
        scrollToBottom();
    } else {
        showNewMessageNotification();
    }
}

// Fungsi untuk membuka image viewer
function openImageViewer(imageUrl) {
    // Periksa apakah imageUrl kosong atau tidak valid
    if (!imageUrl) {
        console.error('URL gambar tidak valid:', imageUrl);
        return;
    }
    
    try {
        // Periksa apakah imageUrl sudah lengkap atau perlu ditambahkan domain
        if (imageUrl && !imageUrl.startsWith('http')) {
            // Ini adalah URL relatif, tambahkan domain
            const baseUrl = window.location.origin;
            
            // Jika imageUrl dimulai dengan 'storage/', hapus slash di awal jika ada
            if (imageUrl.startsWith('/')) {
                imageUrl = imageUrl.substring(1);
            }
            
            // Gabungkan baseUrl dengan imageUrl
            imageUrl = `${baseUrl}/${imageUrl}`;
        }
        
        console.log('Opening image viewer with URL:', imageUrl);
        
        // Isi src pada modal
        $('#previewImage').attr('src', imageUrl);
        
        // Buka modal
        const imageModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
        imageModal.show();
    } catch (error) {
        console.error('Error opening image viewer:', error);
    }
}

// Tambahkan fungsi openImageViewer ke window untuk akses global
window.openImageViewer = openImageViewer;

/**
 * Setup tracking status online/offline pengguna
 */
function setupUserStatusTracking() {
    // Kirim status online saat halaman dimuat
    updateUserStatus('online');
    
    // Kirim status online setiap 30 detik untuk memperbarui timestamp last_active
    const onlineInterval = setInterval(() => {
        if (document.visibilityState === 'visible') {
            updateUserStatus('online');
        }
    }, 30000);
    
    // Deteksi saat pengguna meninggalkan tab/browser
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            // Pengguna meninggalkan tab, set status away
            updateUserStatus('away');
        } else if (document.visibilityState === 'visible') {
            // Pengguna kembali ke tab, set status online
            updateUserStatus('online');
        }
    });
    
    // Deteksi saat pengguna menutup halaman/browser
    window.addEventListener('beforeunload', function() {
        // Set status offline saat pengguna menutup halaman
        updateUserStatus('offline');
        
        // Hentikan interval
        clearInterval(onlineInterval);
    });
}

/**
 * Update status pengguna ke server
 * 
 * @param {string} status - Status pengguna ('online', 'away', atau 'offline')
 */
function updateUserStatus(status) {
    $.ajax({
        url: `/rt/user/status/${status}`,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log(`Status updated to ${status}`);
        },
        error: function(xhr, status, error) {
            console.error('Failed to update status:', error);
        }
    });
}

/**
 * Mendapatkan status pengguna dari server
 * 
 * @param {string} userId - ID pengguna yang ingin dicek statusnya
 * @returns {Promise} - Promise yang mengembalikan data status pengguna
 */
function getUserStatus(userId) {
    return $.ajax({
        url: `/rt/user/status/${userId}`,
        type: 'GET',
        dataType: 'json'
    });
}

/**
 * Update UI berdasarkan status online pengguna
 * 
 * @param {Object} userData - Data pengguna dari server
 */
function updateUserStatusUI(userData) {
    if (!userData || !userData.success) return;
    
    const isOnline = userData.is_online;
    const statusText = userData.status_text;
    const lastActive = userData.last_active_time || '';
    const lastActiveTimestamp = userData.last_active_timestamp || '';
    
    // Format waktu sesuai zona waktu lokal
    const localTimeFormatted = lastActiveTimestamp ? 
        formatLocalTime(lastActiveTimestamp, 'full') : 
        'Belum pernah aktif';
    
    // Hitung waktu relatif
    const relativeTime = lastActiveTimestamp ? 
        getRelativeTime(lastActiveTimestamp) : 
        'Belum pernah aktif';
    
    // Update status di header chat
    const statusIndicator = $('.status-indicator');
    const statusTextElement = $('.status.text-white-50');
    
    if (statusIndicator.length && statusTextElement.length) {
        // Update indikator status (dot)
        if (isOnline) {
            statusIndicator.removeClass('bg-secondary').addClass('bg-success');
            // Tambahkan tooltip untuk status online
            statusIndicator.attr('title', 'Online');
            statusIndicator.tooltip('dispose').tooltip();
        } else {
            statusIndicator.removeClass('bg-success').addClass('bg-secondary');
            // Tambahkan tooltip untuk status offline dengan waktu terakhir online
            statusIndicator.attr('title', `Offline - Terakhir aktif: ${localTimeFormatted}`);
            statusIndicator.tooltip('dispose').tooltip();
        }
        
        // Update teks status dengan waktu relatif
        statusTextElement.text(isOnline ? 'Online' : relativeTime);
        // Tambahkan tooltip untuk status text dengan waktu lengkap
        statusTextElement.attr('title', `Terakhir aktif: ${localTimeFormatted}`);
        statusTextElement.tooltip('dispose').tooltip();
    }
}

/**
 * Mulai pengecekan status pengguna secara berkala
 * 
 * @param {string} userId - ID pengguna yang ingin dicek statusnya
 * @param {number} interval - Interval pengecekan dalam milidetik
 * @return {number} - ID interval untuk digunakan saat membersihkan interval
 */
function startStatusChecking(userId, interval = 15000) {
    // Cek status saat pertama kali halaman dimuat
    getUserStatus(userId).then(function(userData) {
        updateUserStatusUI(userData);
    });
    
    // Cek status secara berkala
    const intervalId = setInterval(function() {
        getUserStatus(userId).then(function(userData) {
            updateUserStatusUI(userData);
        });
    }, interval);
    
    return intervalId;
}

// Format waktu untuk tampilan di daftar chat (index.blade.php)
function formatChatTime(serverTime) {
    if (!serverTime) return '';
    
    // Buat objek Date dari waktu server
    const date = new Date(serverTime);
    const now = new Date();
    const yesterday = new Date(now);
    yesterday.setDate(yesterday.getDate() - 1);
    
    // Format sesuai dengan kebutuhan
    if (date.toDateString() === now.toDateString()) {
        // Hari ini: tampilkan jam
        return date.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: false
        });
    } else if (date.toDateString() === yesterday.toDateString()) {
        // Kemarin
        return 'Kemarin';
    } else if (date.getFullYear() === now.getFullYear()) {
        // Tahun ini: tampilkan tanggal & bulan
        return date.toLocaleDateString('id-ID', { 
            day: '2-digit', 
            month: '2-digit'
        });
    } else {
        // Tahun lalu: tampilkan tanggal, bulan & tahun
        return date.toLocaleDateString('id-ID', { 
            day: '2-digit', 
            month: '2-digit', 
            year: '2-digit'
        });
    }
}

// Fungsi untuk memformat waktu lokal dengan berbagai format
function formatLocalTime(serverTime, format = 'datetime') {
    if (!serverTime) return '';
    
    // Buat objek Date dari waktu server
    const date = new Date(serverTime);
    
    // Format sesuai dengan kebutuhan
    switch (format) {
        case 'full':
            return date.toLocaleString('id-ID', { 
                weekday: 'long',
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
        case 'date':
            return date.toLocaleDateString('id-ID', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        case 'time':
            return date.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false
            });
        case 'datetime':
            return date.toLocaleString('id-ID', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
        case 'chat-time':
            return formatChatTime(serverTime);
        default:
            return date.toLocaleString('id-ID');
    }
}

/**
 * Menghitung dan memformat waktu relatif (seperti "5 menit yang lalu")
 * 
 * @param {string} serverTime - Waktu dari server dalam format ISO atau timestamp
 * @return {string} - Teks waktu relatif
 */
function getRelativeTime(serverTime) {
    if (!serverTime) return '';
    
    const date = new Date(serverTime);
    const now = new Date();
    
    // Hitung perbedaan dalam milidetik
    const diffMs = now - date;
    const diffSec = Math.floor(diffMs / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffHour = Math.floor(diffMin / 60);
    const diffDay = Math.floor(diffHour / 24);
    
    // Format teks relatif
    if (diffSec < 60) {
        return 'Baru saja';
    } else if (diffMin < 60) {
        return `${diffMin} menit yang lalu`;
    } else if (diffHour < 24) {
        const remainingMinutes = diffMin % 60;
        if (remainingMinutes > 0) {
            return `${diffHour} jam ${remainingMinutes} menit yang lalu`;
        }
        return `${diffHour} jam yang lalu`;
    } else if (diffDay < 7) {
        return `${diffDay} hari yang lalu`;
    } else {
        // Jika lebih dari 7 hari, tampilkan tanggal lengkap
        return formatLocalTime(serverTime, 'datetime');
    }
}

/**
 * Menyesuaikan ukuran gambar pesan agar tampil dengan benar
 * 
 * @param {HTMLImageElement} img - Elemen gambar yang perlu disesuaikan
 */
function adjustImageSize(img) {
    // Ukuran maksimum gambar
    const MAX_WIDTH = 250;
    const MAX_HEIGHT = 200;
    
    // Pastikan gambar sudah dimuat sepenuhnya
    if (img.complete) {
        applyImageSizing(img);
    } else {
        img.onload = function() {
            applyImageSizing(img);
        };
    }
    
    // Fungsi untuk menerapkan ukuran yang tepat
    function applyImageSizing(img) {
        // Untuk gambar dalam pesan
        const container = img.closest('.message-image-container');
        if (container) {
            const loadingIndicator = container.querySelector('.image-loading-indicator');
            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }
        }
        
        // Atur ukuran gambar
        if (img.naturalWidth > MAX_WIDTH || img.naturalHeight > MAX_HEIGHT) {
            const widthRatio = MAX_WIDTH / img.naturalWidth;
            const heightRatio = MAX_HEIGHT / img.naturalHeight;
            const ratio = Math.min(widthRatio, heightRatio);
            
            img.style.width = Math.floor(img.naturalWidth * ratio) + 'px';
            img.style.height = Math.floor(img.naturalHeight * ratio) + 'px';
        } else {
            img.style.maxWidth = MAX_WIDTH + 'px';
            img.style.maxHeight = MAX_HEIGHT + 'px';
        }
        
        // Tambahkan kelas untuk menunjukkan bahwa gambar sudah dimuat
        img.classList.add('loaded');
    }
}

/**
 * Menampilkan pesan dengan status loading saat upload
 * 
 * @param {string} type - Tipe pesan ('image' atau 'document')
 * @param {string} tempId - ID sementara untuk pesan
 * @param {object} data - Data tambahan (nama file, ukuran, dll)
 */
function appendLoadingMessage(type, tempId, data = {}) {
    const messageClass = 'message-outgoing';
    let messageContent = '';
    
    // Tambahkan konten sesuai tipe
    if (type === 'image') {
        if (data.preview) {
            messageContent += `
                <div class="message-image-container loading">
                    <img src="${data.preview}" alt="Uploading Image" class="message-image uploading">
                    <div class="upload-overlay">
                        <div class="spinner-border text-light spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            `;
        } else {
            messageContent += `
                <div class="message-image-container loading">
                    <div class="image-placeholder">
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="upload-overlay">
                        <div class="spinner-border text-light spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            `;
        }
        
        if (data.caption) {
            messageContent += `<div class="message-text">${data.caption}</div>`;
        }
    } 
    else if (type === 'document') {
        messageContent += `
            <div class="document-container loading">
                <div class="document-icon">
                    <i class="${getDocumentIcon(data.name || 'file.txt')}"></i>
                </div>
                <div class="document-info">
                    <div class="document-name">${data.name || 'Dokumen'}</div>
                    <div class="document-size">${data.size || 'Mengunggah...'}</div>
                </div>
                <div class="upload-overlay">
                    <div class="spinner-border text-primary spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        `;
        
        if (data.caption) {
            messageContent += `<div class="message-text">${data.caption}</div>`;
        }
    }
    
    // Buat elemen pesan
    const messageHtml = `
        <div class="message ${messageClass} temp-message animate__animated animate__fadeIn" id="temp-message-${tempId}">
            <div class="message-bubble" data-message-id="${tempId}">
                <div class="message-header">
                </div>
                ${messageContent}
                <div class="message-time">
                    <i class="fas fa-clock"></i>
                    <i class="fas fa-check message-status uploading"></i>
                </div>
            </div>
        </div>
    `;
    
    // Tambahkan ke container
    $('#chatMessages').append(messageHtml);
    
    // Scroll ke bawah
    scrollToBottom();
    
    return tempId;
}

// Fungsi untuk menghapus semua pesan dalam chat
function clearChat(chatId) {
    // Gunakan SweetAlert jika tersedia
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Semua Pesan?',
            text: 'Semua pesan dalam percakapan ini akan dihapus secara permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                const loadingToast = showToast('Menghapus pesan...', 0);
                
                // Kirim request ke server
                $.ajax({
                    url: `/rt/chat/${chatId}/clear`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Sembunyikan loading toast
                        if (loadingToast) {
                            loadingToast.hide();
                        }
                        
                        if (response.success) {
                            // Hapus semua pesan dari tampilan
                            $('#chatMessages').empty();
                            
                            // Tambahkan tanggal hari ini
                            $('#chatMessages').append(`
                                <div class="date-divider">
                                    <span>${formatDate(new Date())}</span>
                                </div>
                                <div class="text-center my-5">
                                    <p class="text-muted">Semua pesan telah dihapus</p>
                                </div>
                            `);
                            
                            // Reset last message ID
                            $('#lastMessageId').val('0');
                            
                            showToast(response.message);
                        } else {
                            showToast('Gagal menghapus pesan: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        // Sembunyikan loading toast
                        if (loadingToast) {
                            loadingToast.hide();
                        }
                        
                        let errorMessage = 'Gagal menghapus pesan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showToast(errorMessage);
                    }
                });
            }
        });
    } else if (confirm('Apakah Anda yakin ingin menghapus semua pesan dalam chat ini? Semua pesan dan file akan dihapus secara permanen.')) {
        // Tampilkan loading
        const loadingToast = showToast('Menghapus pesan...', 0);
        
        // Kirim request ke server
        $.ajax({
            url: `/rt/chat/${chatId}/clear`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Sembunyikan loading toast
                if (loadingToast) {
                    loadingToast.hide();
                }
                
                if (response.success) {
                    // Hapus semua pesan dari tampilan
                    $('#chatMessages').empty();
                    
                    // Tambahkan tanggal hari ini
                    $('#chatMessages').append(`
                        <div class="date-divider">
                            <span>${formatDate(new Date())}</span>
                        </div>
                        <div class="text-center my-5">
                            <p class="text-muted">Semua pesan telah dihapus</p>
                        </div>
                    `);
                    
                    // Reset last message ID
                    $('#lastMessageId').val('0');
                    
                    showToast(response.message);
                } else {
                    showToast('Gagal menghapus pesan: ' + response.message);
                }
            },
            error: function(xhr) {
                // Sembunyikan loading toast
                if (loadingToast) {
                    loadingToast.hide();
                }
                
                let errorMessage = 'Gagal menghapus pesan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showToast(errorMessage);
            }
        });
    }
}

// Fungsi untuk memainkan suara notifikasi
function playNotificationSound() {
    try {
        // Cek apakah pengguna berada di halaman viewchat-rt
        const isViewChatPage = window.location.pathname.includes('/rt/chat/viewchat/');
        
        // Cek apakah halaman baru dimuat (flag time)
        const now = Date.now();
        const lastNotificationTime = parseInt(sessionStorage.getItem('lastNotificationTime') || '0');
        const timeDiff = now - lastNotificationTime;
        
        // Cek apakah halaman sedang dalam proses loading
        const isPageLoading = sessionStorage.getItem('pageLoading') === 'true';
        
        // Hanya mainkan suara jika:
        // 1. Tidak berada di halaman viewchat-rt atau tab tidak aktif
        // 2. Sudah lebih dari 5 detik sejak notifikasi terakhir (untuk mencegah spam)
        // 3. Tidak sedang dalam proses loading halaman
        // 4. Halaman sudah selesai loading sepenuhnya
        if ((!isViewChatPage || document.hidden) && 
            timeDiff > 5000 && 
            document.readyState === 'complete' && 
            !isPageLoading) {
            
            console.log('Playing notification sound');
            
            // Gunakan audio yang sudah ada atau buat baru
            let notificationSound = document.getElementById('notificationSound');
            
            if (!notificationSound) {
                notificationSound = document.createElement('audio');
                notificationSound.id = 'notificationSound';
                notificationSound.src = '/storage/sound/chat.mp3';
                notificationSound.preload = 'auto';
                document.body.appendChild(notificationSound);
            }
            
            // Reset dan mainkan suara
            notificationSound.currentTime = 0;
            
            // Promise untuk memainkan audio (untuk menangani browser modern)
            const playPromise = notificationSound.play();
            
            // Tangani error jika ada
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.error('Gagal memainkan suara notifikasi:', error);
                    
                    // Jika error karena interaksi pengguna, coba mainkan lagi saat ada interaksi
                    if (error.name === 'NotAllowedError') {
                        // Tambahkan event listener untuk penanganan interaksi pengguna
                        const handleUserInteraction = function() {
                            notificationSound.play().catch(e => console.error('Masih gagal memainkan suara:', e));
                            document.removeEventListener('click', handleUserInteraction);
                            document.removeEventListener('keydown', handleUserInteraction);
                        };
                        
                        document.addEventListener('click', handleUserInteraction);
                        document.addEventListener('keydown', handleUserInteraction);
                    }
                });
            }
            
            // Simpan waktu notifikasi terakhir
            sessionStorage.setItem('lastNotificationTime', now.toString());
        } else {
            console.log('Skip notification sound playback', {
                isViewChatPage,
                isHidden: document.hidden,
                timeDiff,
                readyState: document.readyState,
                isPageLoading
            });
        }
    } catch (e) {
        console.error('Error saat memainkan suara notifikasi:', e);
    }
}

// Export fungsi untuk digunakan di halaman
window.chatFunctions = {
    formatTime,
    formatDate,
    markAsRead,
    deleteChat,
    sendChatMessage,
    fetchNewMessages,
    fetchChatList,
    fetchAvailableUsers,
    createNewChat,
    startMessagePolling,
    stopMessagePolling,
    loadChatMessages,
    sendImageMessage,
    sendDocumentMessage,
    replyToMessage,
    cancelReply,
    copyMessage,
    deleteMessage,
    editMessage,
    saveEditedMessage,
    previewUploadImage,
    showDocumentInfo,
    getDocumentIcon,
    getUserStatus,
    updateUserStatusUI,
    startStatusChecking,
    setupUserStatusTracking,
    appendLoadingMessage,
    clearChat,
    playNotificationSound
};

// Initialize tracking when document is ready
$(document).ready(function() {
    setupUserStatusTracking();
    
    // Dengarkan pesan dari parent window (untuk notifikasi navbar)
    window.addEventListener('message', function(event) {
        // Cek apakah pesan dari parent window
        if (event.data && event.data.type === 'newNotification') {
            // Play notification sound
            playNotificationSound();
        }
    });
    
    // Existing initialization code...
    
    // Perbarui semua format waktu
    updateAllChatTimes();
});

// Fungsi untuk memainkan suara notifikasi - versi global
window.playNotificationSound = function() {
    playNotificationSound();
};

// Fungsi untuk memperbarui format waktu pada semua pesan dalam daftar chat
function updateAllChatTimes() {
    // Perbarui waktu di daftar chat
    $('.chat-item').each(function() {
        const timeElement = $(this).find('.chat-time');
        const timestamp = timeElement.data('timestamp');
        if (timestamp) {
            timeElement.text(formatLocalTime(timestamp, 'chat-time'));
        }
    });
    
    // Perbarui waktu di pesan chat
    $('.message-time').each(function() {
        const timestamp = $(this).data('timestamp');
        if (timestamp) {
            // Simpan ikon status jika ada
            const statusIcon = $(this).find('.message-status').clone();
            $(this).html(formatLocalTime(timestamp, 'time'));
            
            // Tambahkan kembali ikon status jika ada
            if (statusIcon.length) {
                $(this).append(statusIcon);
            }
        }
    });
}
