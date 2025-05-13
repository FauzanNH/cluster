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
                <li class="{{ Request::is('rt/usersatpam*') ? 'active' : '' }}">
                    <a href="{{ route('rt.DataSatpam.index') }}"><i class="fas fa-calendar-alt me-2"></i> <span>Data Satpam</span></a>
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
                            <a href="#"><i class="fas fa-exclamation-circle me-2"></i> <span>Keluhan</span></a>
                        </li>
                        <li class="{{ Request::is('rt/laporan/aspirasi*') ? 'active' : '' }}">
                            <a href="#"><i class="fas fa-lightbulb me-2"></i> <span>Aspirasi & Saran</span></a>
                        </li>
                        <li class="{{ Request::is('rt/laporan/tamu*') ? 'active' : '' }}">
                            <a href="#"><i class="fas fa-user-friends me-2"></i> <span>Jumlah Tamu</span></a>
                        </li>
                    </ul>
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

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function () {
            // Check for stored sidebar state
            if (localStorage.getItem('sidebarState') === 'collapsed') {
                $('#sidebar').addClass('active');
            }
            
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
