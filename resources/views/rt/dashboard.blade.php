@extends('RTtemplate')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-header"><i class="fas fa-tachometer-alt me-2"></i>Dashboard RT</h3>
            <p class="lead">Selamat datang di panel kontrol RT Bukit Asri Cluster.</p>
            <p class="small"><i class="fas fa-home"></i> RT - Home</p>
            <hr>
        </div>
    </div>

    <div class="row">
        <!-- Statistik Warga -->
        <div class="col-md-3 mb-4">
            <div class="card dashboard-card card-warga">
                <div class="card-body text-center">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title">Total Warga</h5>
                    <p class="card-text counter-animation">{{ $totalWarga }}</p>
                </div>
            </div>
        </div>

        <!-- Statistik Rumah -->
        <div class="col-md-3 mb-4">
            <div class="card dashboard-card card-rumah">
                <div class="card-body text-center">
                    <div class="card-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h5 class="card-title">Total Rumah</h5>
                    <p class="card-text counter-animation">{{ $totalRumah }}</p>
                </div>
            </div>
        </div>

        <!-- Pengumuman -->
        <div class="col-md-3 mb-4">
            <div class="card dashboard-card card-pengumuman">
                <div class="card-body text-center">
                    <div class="card-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h5 class="card-title">Pengumuman</h5>
                    <p class="card-text counter-animation">8</p>
                </div>
            </div>
        </div>

        <!-- Kegiatan -->
        <div class="col-md-3 mb-4">
            <div class="card dashboard-card card-kegiatan">
                <div class="card-body text-center">
                    <div class="card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h5 class="card-title">Kegiatan</h5>
                    <p class="card-text counter-animation">3</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Dokumen -->
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h4 class="section-header"><i class="fas fa-file-alt me-2"></i>Status Surat Pengajuan</h4>
        </div>
        
        <!-- Menunggu Persetujuan -->
        <div class="col-md-3 mb-4">
            <div class="card status-card status-waiting">
                <div class="card-body text-center">
                    <div class="status-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5 class="card-title">Menunggu Verikasi</h5>
                    <p class="card-text counter-animation">{{ $totalSuratMenunggu }}</p>
                    <a href="#" class="btn btn-warning mt-3"><i class="fas fa-arrow-right me-1"></i> Lihat Detail</a>
                </div>
            </div>
        </div>
        
        <!-- Sedang Ditinjau -->
        <div class="col-md-3 mb-4">
            <div class="card status-card status-review">
                <div class="card-body text-center">
                    <div class="status-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5 class="card-title">Sedang Di Validasi</h5>
                    <p class="card-text counter-animation">{{ $totalSuratValidasi }}</p>
                    <a href="#" class="btn btn-info mt-3 text-white"><i class="fas fa-arrow-right me-1"></i> Lihat Detail</a>
                </div>
            </div>
        </div>
        
        <!-- Disetujui -->
        <div class="col-md-3 mb-4">
            <div class="card status-card status-approved">
                <div class="card-body text-center">
                    <div class="status-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h5 class="card-title">Disetujui</h5>
                    <p class="card-text counter-animation">{{ $totalSuratDisetujui }}</p>
                    <a href="#" class="btn btn-success mt-3"><i class="fas fa-arrow-right me-1"></i> Lihat Detail</a>
                </div>
            </div>
        </div>
        
        <!-- Ditolak -->
        <div class="col-md-3 mb-4">
            <div class="card status-card status-rejected">
                <div class="card-body text-center">
                    <div class="status-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h5 class="card-title">Ditolak</h5>
                    <p class="card-text counter-animation">{{ $totalSuratDitolak }}</p>
                    <a href="#" class="btn btn-danger mt-3"><i class="fas fa-arrow-right me-1"></i> Lihat Detail</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pengumuman Terbaru -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0"><i class="fas fa-bullhorn me-2"></i>Pengumuman Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Jadwal Pemadaman Listrik</h6>
                                <small>3 hari yang lalu</small>
                            </div>
                            <p class="mb-1">Akan ada pemadaman listrik untuk perawatan rutin pada tanggal 15 Juni 2023.</p>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Pemberitahuan Rapat Warga</h6>
                                <small>5 hari yang lalu</small>
                            </div>
                            <p class="mb-1">Rapat warga akan diadakan pada tanggal 20 Juni 2023 pukul 19.00 WIB.</p>
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="#" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
            </div>
        </div>

        <!-- Kegiatan Mendatang -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i>Kegiatan Mendatang</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Kerja Bakti</h6>
                                <small>20 Juni 2023</small>
                            </div>
                            <p class="mb-1">Kerja bakti pembersihan lingkungan cluster.</p>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Perayaan HUT RI</h6>
                                <small>17 Agustus 2023</small>
                            </div>
                            <p class="mb-1">Lomba dan perayaan HUT RI di lingkungan cluster.</p>
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="#" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informasi Login -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0"><i class="fas fa-user me-2"></i>Informasi Login</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No HP</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ Auth::user()->users_id }}</td>
                                <td>{{ Auth::user()->nama }}</td>
                                <td>{{ Auth::user()->email }}</td>
                                <td>{{ Auth::user()->no_hp }}</td>
                                <td>{{ Auth::user()->role }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Animation for counter numbers
    document.addEventListener('DOMContentLoaded', function() {
        const counterElements = document.querySelectorAll('.counter-animation');
        
        counterElements.forEach(counter => {
            const target = parseInt(counter.innerText);
            let start = 0;
            const duration = 2000;
            const frameDuration = 1000 / 60;
            const totalFrames = Math.round(duration / frameDuration);
            
            const animate = () => {
                const frame = Math.min(1, start / totalFrames);
                const easeOut = Math.pow(frame - 1, 3) + 1;
                const value = Math.floor(target * easeOut);
                
                counter.innerText = value;
                start++;
                
                if (start <= totalFrames) {
                    requestAnimationFrame(animate);
                } else {
                    counter.innerText = target;
                }
            };
            
            requestAnimationFrame(animate);
        });
    });
</script>
@endpush
@endsection 