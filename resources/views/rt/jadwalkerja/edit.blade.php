@extends('RTtemplate')

@section('title', 'Edit Jadwal Kerja Satpam')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Edit Jadwal Kerja Satpam</h5>
                    <a href="{{ route('rt.jadwalkerja.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('rt.jadwalkerja.update', $jadwal->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="users_id" class="form-label">Satpam</label>
                                <select name="users_id" id="users_id" class="form-select" required>
                                    <option value="">-- Pilih Satpam --</option>
                                    @foreach ($satpam as $guard)
                                        <option value="{{ $guard->users_id }}" {{ $jadwal->users_id == $guard->users_id ? 'selected' : '' }}>
                                            {{ $guard->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $jadwal->tanggal->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="shift" class="form-label">Shift</label>
                                <select name="shift" id="shift" class="form-select" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="pagi" {{ $jadwal->shift == 'pagi' ? 'selected' : '' }}>Pagi (06:00 - 14:00)</option>
                                    <option value="siang" {{ $jadwal->shift == 'siang' ? 'selected' : '' }}>Siang (14:00 - 22:00)</option>
                                    <option value="malam" {{ $jadwal->shift == 'malam' ? 'selected' : '' }}>Malam (22:00 - 06:00)</option>
                                    <option value="libur" {{ $jadwal->shift == 'libur' ? 'selected' : '' }}>Libur</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="lokasi" class="form-label">Seksi Unit</label>
                                <select name="lokasi" id="lokasi" class="form-select" required>
                                    <option value="">-- Pilih Seksi Unit --</option>
                                    <option value="Gerbang Utama" {{ $jadwal->lokasi == 'Gerbang Utama' ? 'selected' : '' }}>Gerbang Utama</option>
                                    <option value="Gerbang Belakang" {{ $jadwal->lokasi == 'Gerbang Belakang' ? 'selected' : '' }}>Gerbang Belakang</option>
                                    <option value="Gerbang Timur" {{ $jadwal->lokasi == 'Gerbang Timur' ? 'selected' : '' }}>Gerbang Timur</option>
                                    <option value="Gerbang Barat" {{ $jadwal->lokasi == 'Gerbang Barat' ? 'selected' : '' }}>Gerbang Barat</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="lokasi_detail" class="form-label">Detail Lokasi</label>
                            <input type="text" name="lokasi_detail" id="lokasi_detail" class="form-control" value="{{ $jadwal->lokasi_detail }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="3">{{ $jadwal->catatan }}</textarea>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ $jadwal->is_active ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Aktif</label>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-secondary">Reset</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-update lokasi_detail when lokasi changes
        $('#lokasi').on('change', function() {
            const lokasi = $(this).val();
            $('#lokasi_detail').val(lokasi + ' Cluster Bukit Asri');
        });
    });
</script>
@endsection 