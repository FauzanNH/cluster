@extends('RTtemplate')

@section('title', 'Pengaturan Akun')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/rt/pengaturan.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title">
                <h2><i class="fas fa-cog me-2"></i>Pengaturan Akun</h2>
                <p class="text-muted">Kelola informasi akun dan preferensi Anda</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <img src="{{ asset('images/default-avatar.png') }}" alt="Foto Profil" id="avatar-preview">
                        <div class="avatar-overlay">
                            <label for="avatar-upload" class="avatar-edit-btn">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="avatar-upload" class="d-none">
                        </div>
                    </div>
                </div>
                <div class="profile-info">
                    <h4>{{ Auth::user()->nama }}</h4>
                    <p class="text-muted">Ketua RT</p>
                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-value">{{ Auth::user()->email }}</span>
                            <span class="stat-label">Email</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="settings-card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="settings-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                                <i class="fas fa-user me-2"></i>Profil
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                                <i class="fas fa-lock me-2"></i>Keamanan
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="settings-tab-content">
                        <!-- Tab Profil -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <form id="profile-form" method="POST" action="{{ route('rt.pengaturan.update') }}">
                                @csrf
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Harap Bijak Saat mengganti informasi akun 
                                </div>
                                
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="{{ Auth::user()->nama }}" disabled>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" disabled>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">Nomor HP</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ Auth::user()->no_hp }}" disabled>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="rt_blok" class="form-label">RT/Blok</label>
                                    <input type="text" pattern="^[0-9,]+$" title="Hanya angka dan koma, contoh: 1,2,3,4,5" class="form-control @error('rt_blok') is-invalid @enderror" id="rt_blok" name="rt_blok" value="{{ old('rt_blok', Auth::user()->rt_blok) }}" disabled>
                                    <small class="form-text text-muted rt-blok-note" style="display:none;">Contoh: 1,2,3,4,5 <b>bukan</b> 01,02,03,04,05. Masukan nomor RT nya saja tanpa kata RT</small>
                                    @error('rt_blok')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-secondary" id="edit-profile-btn">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </button>
                                    <button type="submit" class="btn btn-primary d-none" id="save-profile-btn">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                    <button type="button" class="btn btn-danger d-none" id="cancel-profile-btn">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Tab Keamanan -->
                        <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <form id="security-form" method="POST" action="{{ route('rt.pengaturan.updatePassword') }}">
                                @csrf
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Pastikan password baru Anda kuat dan tidak digunakan di situs lain.
                                </div>
                                
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Password Saat Ini</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="current_password" name="current_password" disabled>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password" name="new_password" disabled>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength mt-2">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">Kekuatan password: <span id="password-strength-text">Belum diisi</span></small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" disabled>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password_confirmation">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-secondary" id="edit-password-btn">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </button>
                                    <button type="submit" class="btn btn-primary d-none" id="save-password-btn">
                                        <i class="fas fa-key me-2"></i>Ubah Password
                                    </button>
                                    <button type="button" class="btn btn-danger d-none" id="cancel-password-btn">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/rt/pengaturan/pengaturan.js') }}"></script>
@endsection