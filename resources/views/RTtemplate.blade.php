<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard RT') - Bukit Asri Cluster</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/template.css') }}">
    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/rt/dashboard.css') }}">
    
    <style>
        /* Badge notifikasi */
        #chatBadge, #sidebarChatBadge {
            display: none;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            font-size: 11px;
            font-weight: bold;
            line-height: 18px;
            text-align: center;
        }
        
        /* Animasi untuk badge notifikasi */
        @keyframes badgePulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }
        
        .badge-pulse {
            animation: badgePulse 0.5s ease-in-out;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark text-white">
            <div class="sidebar-header p-3">
                <h4>Bukit Asri Cluster</h4>
                <h6>Panel RT</h6>
            </div>

            <ul class="list-unstyled components p-3">
                <li class="{{ Request::is('rt/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('rt.dashboard') }}"><i class="fas fa-home me-2"></i> <span>Dashboard</span></a>
                </li>
                <li class="{{ Request::is('rt/usersatpam*') || Request::is('rt/jadwalkerja*') ? 'active' : '' }} dropdown">
                    <a href="#satpamSubmenu" data-bs-toggle="collapse" aria-expanded="{{ Request::is('rt/usersatpam*') || Request::is('rt/jadwalkerja*') ? 'true' : 'false' }}" class="dropdown-toggle">
                        <i class="fas fa-shield-alt me-2"></i> <span>Manajemen Satpam</span>
                    </a>
                    <ul class="collapse list-unstyled {{ Request::is('rt/usersatpam*') || Request::is('rt/jadwalkerja*') ? 'show' : '' }}" id="satpamSubmenu">
                        <li class="{{ Request::is('rt/usersatpam*') ? 'active' : '' }}">
                            <a href="{{ route('rt.DataSatpam.index') }}"><i class="fas fa-users me-2"></i> <span>Data Satpam</span></a>
                        </li>
                        <li class="{{ Request::is('rt/jadwalkerja*') ? 'active' : '' }}">
                            <a href="{{ route('rt.jadwalkerja.index') }}"><i class="fas fa-calendar-alt me-2"></i> <span>Jadwal Kerja</span></a>
                        </li>
                    </ul>
                </li>
                <li class="{{ Request::is('rt/userwarga*') ? 'active' : '' }} dropdown">
                    <a href="#wargaSubmenu" data-bs-toggle="collapse" aria-expanded="{{ Request::is('rt/userwarga*') ? 'true' : 'false' }}" class="dropdown-toggle">
                        <i class="fas fa-users me-2"></i> <span>Data Warga</span>
                    </a>
                    <ul class="collapse list-unstyled {{ Request::is('rt/userwarga*') ? 'show' : '' }}" id="wargaSubmenu">
                        <li class="{{ Request::is('rt/userwarga/kependudukan*') ? 'active' : '' }}">
                            <a href="{{ route('rt.DataWarga.datapenduduk') }}"><i class="fas fa-id-card me-2"></i> <span>Data Kependudukan</span></a>
                        </li>
                        <li class="{{ Request::is('rt/userwarga/kepemilikan*') ? 'active' : '' }}">
                            <a href="{{ route('rt.DataWarga.datarumah') }}"><i class="fas fa-home me-2"></i> <span>Data Rumah Warga</span></a>
                        </li>
                        <li class="{{ Request::is('rt/userwarga/surat*') ? 'active' : '' }}">
                            <a href="{{ route('rt.DataWarga.suratpengantar') }}"><i class="fas fa-envelope me-2"></i> <span>Pengajuan Surat Pengantar</span></a>
                        </li>
                        <li class="{{ Request::is('rt/userwarga/akun*') ? 'active' : '' }}">
                            <a href="{{ route('rt.DataWarga.akunwarga') }}"><i class="fas fa-user me-2"></i> <span>Akun Warga</span></a>
                        </li>
                    </ul>
                </li>
                <li class="{{ Request::is('rt/laporan*') ? 'active' : '' }} dropdown">
                    <a href="#laporanSubmenu" data-bs-toggle="collapse" aria-expanded="{{ Request::is('rt/laporan*') ? 'true' : 'false' }}" class="dropdown-toggle">
                        <i class="fas fa-file-alt me-2"></i> <span>Laporan</span>
                    </a>
                    <ul class="collapse list-unstyled {{ Request::is('rt/laporan*') ? 'show' : '' }}" id="laporanSubmenu">
                        <li class="{{ Request::is('rt/laporan/keluhan*') ? 'active' : '' }}">
                            <a href="{{ route('rt.laporan.keluhan') }}"><i class="fas fa-exclamation-circle me-2"></i> <span>Keluhan</span></a>
                        </li>
                        <li class="{{ Request::is('rt/laporan/aspirasi*') ? 'active' : '' }}">
                            <a href="{{ route('rt.laporan.aspirasi') }}"><i class="fas fa-lightbulb me-2"></i> <span>Aspirasi & Saran</span></a>
                        </li>
                        <li class="{{ Request::is('rt/laporan/tamu*') ? 'active' : '' }}">
                            <a href="{{ route('rt.laporan.tamu') }}"><i class="fas fa-user-friends me-2"></i> <span>Jumlah Tamu</span></a>
                        </li>
                    </ul>
                </li>
                <li class="{{ Request::is('rt/chat*') ? 'active' : '' }}">
                    <a href="{{ route('rt.chat.index') }}">
                        <i class="fas fa-comments me-2"></i> <span>Chat</span>
                        <span id="sidebarChatBadge" class="badge bg-danger rounded-pill ms-2" style="display: none;">0</span>
                    </a>
                </li>
                <li class="{{ Request::is('rt/pengaturan*') ? 'active' : '' }}">
                    <a href="{{ route('rt.pengaturan') }}"><i class="fas fa-cog me-2"></i> <span>Pengaturan</span></a>
                </li>
                <li class="{{ Request::is('rt/logout*') ? 'active' : '' }}">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <!-- Chat Icon -->
                        <a href="{{ route('rt.chat.index') }}" class="nav-link me-3 position-relative">
                            <i class="fas fa-comments fa-lg"></i>
                            <span id="chatBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                                0
                                <span class="visually-hidden">pesan belum dibaca</span>
                            </span>
                        </a>
                        
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->nama }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('rt.pengaturan') }}"><i class="fas fa-user me-2"></i> Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                       <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid content-container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @php
                    $blokRtStatus = \App\Http\Controllers\RTController::getBlokRTStatus();
                @endphp

                @if($blokRtStatus['is_empty'])
                    <div class="alert alert-danger" role="alert">
                        <strong>Blok RT belum diatur!</strong> Silakan atur blok RT pada pengaturan profil Anda agar dapat memanajemen data warga.
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Audio untuk notifikasi -->
    <audio id="notificationSound" preload="auto">
        <source src="{{ asset('storage/sound/chat.mp3') }}" type="audio/mpeg">
    </audio>
    
    <script>
        $(document).ready(function () {
            // Check for stored sidebar state
            if (localStorage.getItem('sidebarState') === 'collapsed') {
                $('#sidebar').addClass('active');
            }
            
            // Inisialisasi dan minta izin audio autoplay
            initNotificationSound();
            
            // Toggle sidebar
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
                
                // Store sidebar state
                if ($('#sidebar').hasClass('active')) {
                    localStorage.setItem('sidebarState', 'collapsed');
                } else {
                    localStorage.setItem('sidebarState', 'expanded');
                }
            });
            
            // Fungsi untuk inisialisasi suara notifikasi
            function initNotificationSound() {
                const sound = document.getElementById('notificationSound');
                if (sound) {
                    // Atur volume rendah untuk test
                    sound.volume = 0.1;
                    
                    // Coba play & pause untuk mendapatkan izin user interaction
                    $(document).one('click keydown', function() {
                        sound.play().then(() => {
                            sound.pause();
                            sound.currentTime = 0;
                            sound.volume = 1; // Kembalikan volume normal
                            console.log('Audio permission granted');
                        }).catch(e => {
                            console.log('Interaction required for audio playback:', e);
                        });
                    });
                }
            }
            
            // Mulai polling untuk jumlah pesan belum dibaca
            startUnreadCountPolling();
            
            // Fungsi untuk memulai polling jumlah pesan belum dibaca
            function startUnreadCountPolling() {
                // Jalankan polling setiap 5 detik (lebih cepat dari sebelumnya)
                let pollingInterval = setInterval(function() {
                    fetchUnreadCount();
                }, 5000);
                
                // Simpan interval ID di localStorage untuk memastikan hanya ada satu polling aktif
                localStorage.setItem('unreadCountPollingInterval', pollingInterval);
                
                // Jalankan sekali saat halaman dimuat
                fetchUnreadCount();
                
                // Tambahkan event listener untuk visibility change
                document.addEventListener('visibilitychange', function() {
                    if (document.visibilityState === 'visible') {
                        // Ketika halaman menjadi visible, segera update badge
                        fetchUnreadCount();
                    }
                });
            }
            
            // Fungsi untuk mengambil jumlah pesan belum dibaca
            function fetchUnreadCount() {
                $.ajax({
                    url: "{{ route('rt.chat.list') }}",
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            updateUnreadBadges(response.chats);
                            
                            // Simpan jumlah pesan belum dibaca di sessionStorage
                            // untuk memastikan konsistensi antar tab/halaman
                            const totalUnread = countTotalUnread(response.chats);
                            sessionStorage.setItem('totalUnreadMessages', totalUnread);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Gagal mengambil jumlah pesan belum dibaca:", error);
                        // Coba lagi setelah 2 detik jika gagal
                        setTimeout(fetchUnreadCount, 2000);
                    }
                });
            }
            
            // Helper function untuk menghitung total pesan belum dibaca
            function countTotalUnread(chats) {
                let total = 0;
                $.each(chats, function(index, chat) {
                    total += chat.unread;
                });
                return total;
            }
            
            // Fungsi untuk memperbarui badge notifikasi
            function updateUnreadBadges(chats) {
                var totalUnread = 0;
                var previousUnread = parseInt($('#chatBadge:visible').text() || "0");
                
                // Hitung total pesan belum dibaca
                $.each(chats, function(index, chat) {
                    totalUnread += chat.unread;
                });
                
                // Perbarui badge di navbar dan sidebar
                $('#chatBadge').text(totalUnread);
                $('#sidebarChatBadge').text(totalUnread);
                
                // Tampilkan atau sembunyikan badge berdasarkan jumlah pesan belum dibaca
                if (totalUnread > 0) {
                    // Pastikan badge ditampilkan dengan CSS !important
                    $('#chatBadge, #sidebarChatBadge').css('display', 'inline-block');
                    
                    // Jika jumlah pesan belum dibaca bertambah dan bukan pertama kali dimuat, putar suara notifikasi
                    // Tambahkan flag untuk halaman baru dimuat
                    var isFirstLoad = sessionStorage.getItem('chatBadgeInitialized') !== 'true';
                    if (totalUnread > previousUnread && previousUnread >= 0 && !isFirstLoad) {
                        playNotificationSound();
                    }
                    // Set flag bahwa badge sudah diinisialisasi
                    sessionStorage.setItem('chatBadgeInitialized', 'true');
                    
                    // Tambahkan animasi untuk menarik perhatian jika ada pesan baru
                    $('#chatBadge, #sidebarChatBadge').addClass('badge-pulse');
                    setTimeout(function() {
                        $('#chatBadge, #sidebarChatBadge').removeClass('badge-pulse');
                    }, 1000);
                    
                    // Jika halaman tidak aktif, tambahkan juga notifikasi di title
                    if (document.hidden) {
                        document.title = `(${totalUnread}) Pesan Baru - Bukit Asri Cluster`;
                    }
                } else {
                    $('#chatBadge, #sidebarChatBadge').hide();
                    // Reset title jika tidak ada pesan baru
                    if (document.title.includes('Pesan Baru')) {
                        document.title = document.title.replace(/^\(\d+\) Pesan Baru - /, '');
                    }
                }
            }
            
            // Fungsi untuk memutar suara notifikasi
            function playNotificationSound() {
                try {
                    // Dapatkan URL saat ini
                    const currentUrl = window.location.pathname;
                    
                    // Jika bukan halaman chat detail, putar suara notifikasi
                    if (!currentUrl.includes('/rt/chat/viewchat/')) {
                        const notificationSound = document.getElementById('notificationSound');
                        if (notificationSound) {
                            // Reset dan putar suara
                            notificationSound.currentTime = 0;
                            
                            // Gunakan Promise untuk menangani pemutaran audio
                            const playPromise = notificationSound.play();
                            
                            if (playPromise !== undefined) {
                                playPromise.catch(error => {
                                    console.error('Gagal memainkan suara notifikasi:', error);
                                });
                            }
                        }
                    }
                } catch (e) {
                    console.error('Error saat memainkan suara notifikasi:', e);
                }
            }
            
            // Mendengarkan pesan dari iframe atau halaman lain
            window.addEventListener('message', function(event) {
                // Periksa apakah pesan adalah untuk memperbarui jumlah pesan belum dibaca
                if (event.data && event.data.type === 'updateUnreadCount') {
                    // Jika forceUpdate=true, langsung update dari server
                    if (event.data.forceUpdate === true) {
                        fetchUnreadCount(); // Ambil data terbaru dari server
                        return;
                    }
                    
                    var count = event.data.count;
                    var previousCount = parseInt($('#chatBadge:visible').text() || "0");
                    
                    // Perbarui badge di navbar dan sidebar
                    $('#chatBadge').text(count);
                    $('#sidebarChatBadge').text(count);
                    
                    // Tampilkan atau sembunyikan badge berdasarkan jumlah pesan belum dibaca
                    if (count > 0) {
                        // Pastikan badge ditampilkan dengan CSS !important
                        $('#chatBadge, #sidebarChatBadge').css('display', 'inline-block');
                        
                        // Jika jumlah pesan belum dibaca bertambah, putar suara notifikasi
                        if (count > previousCount && previousCount >= 0) {
                            playNotificationSound();
                        }
                        
                        // Tambahkan animasi untuk menarik perhatian
                        $('#chatBadge, #sidebarChatBadge').addClass('badge-pulse');
                        setTimeout(function() {
                            $('#chatBadge, #sidebarChatBadge').removeClass('badge-pulse');
                        }, 1000);
                        
                        // Jika halaman tidak aktif, tambahkan juga notifikasi di title
                        if (document.hidden) {
                            document.title = `(${count}) Pesan Baru - Bukit Asri Cluster`;
                        }
                    } else {
                        $('#chatBadge, #sidebarChatBadge').hide();
                        // Reset title jika tidak ada pesan baru
                        if (document.title.includes('Pesan Baru')) {
                            document.title = document.title.replace(/^\(\d+\) Pesan Baru - /, '');
                        }
                    }
                    
                    // Simpan jumlah pesan belum dibaca di sessionStorage
                    sessionStorage.setItem('totalUnreadMessages', count);
                }
                
                // Cek jika ini adalah pesan untuk memainkan suara notifikasi
                if (event.data && event.data.type === 'newNotification') {
                    playNotificationSound();
                }
            });
            
            // Handle responsive behavior
            function checkWidth() {
                if ($(window).width() < 768) {
                    $('#sidebar').addClass('active');
                } else if (localStorage.getItem('sidebarState') !== 'collapsed') {
                    $('#sidebar').removeClass('active');
                }
            }
            
            // Check on load and resize
            checkWidth();
            $(window).resize(function() {
                checkWidth();
            });
            
            // Ensure dropdown toggles have smooth transitions
            $('.dropdown-toggle').on('click', function() {
                // Add a small delay to allow for CSS transitions
                setTimeout(function() {
                    if ($('#sidebar').hasClass('active')) {
                        // Force redraw of icons
                        $('#sidebar i').css('transition', 'none');
                        $('#sidebar i').height(); // force repaint
                        $('#sidebar i').css('transition', 'all 0.3s ease');
                    }
                }, 50);
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>

