@extends('RTtemplate')

@section('title', 'Data Penduduk')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/rt/datapenduduk.css') }}">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<style>
    .data-invalid {
        color: red;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
@php
if (!function_exists('mask_nik')) {
    function mask_nik($nik) {
        if(strlen($nik) < 8) return $nik;
        return substr($nik,0,4) . str_repeat('*', strlen($nik)-8) . substr($nik,-4);
    }
}
@endphp
<h3 class="section-header"><i class="fas fa-users me-2"></i>Data Penduduk</h3>
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPendudukModal">
            <i class="fas fa-plus"></i> Tambah Data Penduduk
        </button>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Data Penduduk</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped" id="pendudukTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama</th>
                            <th>Warga ID</th>
                            <th>NIK</th>
                            <th>Domisili KTP</th>
                            <th>Tanggal Lahir</th>
                            <th>Blok     RT</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($datawarga as $warga)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $warga->nama }}</td>
                            <td>{{ $warga->warga_id }}</td>
                            <td class="{{ strlen($warga->nik) < 16 ? 'data-invalid' : '' }}">{{ mask_nik($warga->nik) }}</td>
                            <td>{{ $warga->domisili_ktp }}</td>
                            <td>{{ \Carbon\Carbon::parse($warga->tanggal_lahir)->format('d-m-Y') }}</td>
                            <td>{{ $warga->blok_rt }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-info view-details" data-bs-toggle="tooltip" title="Detail"
                                        data-nama="{{ $warga->nama }}"
                                        data-nik="{{ mask_nik($warga->nik) }}"
                                        data-domisili="{{ $warga->domisili_ktp }}"
                                        data-tanggal-lahir="{{ \Carbon\Carbon::parse($warga->tanggal_lahir)->format('d-m-Y') }}"
                                        data-gender="{{ $warga->gender }}"
                                        data-nokk="{{ mask_nik($warga->no_kk) }}"
                                        data-status="{{ $warga->status_pernikahan }}"
                                        data-pekerjaan="{{ $warga->pekerjaan }}"
                                        data-agama="{{ $warga->agama }}"
                                        data-pendidikan="{{ $warga->pendidikan_terakhir }}"
                                        data-link-foto-ktp="{{ $warga->foto_ktp ? asset('gambar/datapenduduk/'.$warga->foto_ktp) : '' }}"
                                        data-link-foto-kk="{{ $warga->foto_kk ? asset('gambar/datapenduduk/'.$warga->foto_kk) : '' }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-edit" data-bs-toggle="tooltip" title="Edit" data-warga-id="{{ $warga->warga_id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('rt.DataWarga.datapenduduk.destroy', $warga->warga_id) }}" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-delete" data-bs-toggle="tooltip" title="Hapus" data-warga-id="{{ $warga->warga_id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Detail Penduduk Modal -->
<div class="modal fade" id="detailPendudukModal" tabindex="-1" role="dialog" aria-labelledby="detailPendudukModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailPendudukModalLabel">Detail Penduduk</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="detail-header mb-4">
                    <div class="row">
                        <div class="col-md-2 text-center">
                            <div class="profile-image">
                                <i class="fas fa-user-circle fa-5x text-info"></i>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <h4 id="detail-nama" class="mb-1 font-weight-bold">Ahmad Sudrajat</h4>
                            <p id="detail-nik" class="mb-0"><span class="badge badge-info">NIK:</span> <span id="detail-nik-value">3201010101010001</span></p>
                            <p id="detail-nokk" class="mb-0"><span class="badge badge-info">No. KK:</span> <span id="detail-nokk-value">3201010908154321</span></p>
                        </div>
                    </div>
                </div>

                <div class="detail-content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h6 class="section-title"><i class="fas fa-id-card mr-2"></i>Informasi Pribadi</h6>
                                <div class="section-content">
                                    <div class="detail-item">
                                        <span class="detail-label">Gender</span>
                                        <span id="detail-gender" class="detail-value">Laki-laki</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Tanggal Lahir</span>
                                        <span id="detail-tanggal-lahir" class="detail-value">15-08-1985</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Agama</span>
                                        <span id="detail-agama" class="detail-value">Islam</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Status Pernikahan</span>
                                        <span id="detail-status" class="detail-value">Menikah</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h6 class="section-title"><i class="fas fa-briefcase mr-2"></i>Informasi Lainnya</h6>
                                <div class="section-content">
                                    <div class="detail-item">
                                        <span class="detail-label">Pekerjaan</span>
                                        <span id="detail-pekerjaan" class="detail-value">Pegawai Swasta</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Pendidikan Terakhir</span>
                                        <span id="detail-pendidikan" class="detail-value">S1</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Domisili KTP</span>
                                        <span id="detail-domisili" class="detail-value">Jl. Mawar No. 10, Jakarta Timur</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-footer mt-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="detail-section">
                                <h6 class="section-title"><i class="fas fa-file-alt mr-2"></i>Dokumen</h6>
                                <div class="section-content">
                                    <div class="document-list">
                                        <div class="document-item">
                                            <i class="far fa-file-image text-primary mr-2"></i>
                                            <span>Foto KTP</span>
                                            <a href="#" id="link-foto-ktp" class="btn btn-sm btn-outline-primary ml-2" target="_blank">Lihat</a>
                                        </div>
                                        <div class="document-item">
                                            <i class="far fa-file-image text-success mr-2"></i>
                                            <span>Foto KK</span>
                                            <a href="#" id="link-foto-kk" class="btn btn-sm btn-outline-success ml-2" target="_blank">Lihat</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

<!-- Modal Tambah Data Penduduk -->
<div class="modal fade" id="addPendudukModal" tabindex="-1" role="dialog" aria-labelledby="addPendudukModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addPendudukForm" method="POST" enctype="multipart/form-data" action="{{ route('rt.DataWarga.datapenduduk.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addPendudukModalLabel">Tambah Data Penduduk</h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="form-group">
                                <label for="nik">NIK</label>
                                <input type="text" class="form-control" id="nik" name="nik" maxlength="16" required>
                                <small id="nikHelp" class="form-text text-muted">Masukkan 16 digit NIK</small>
                            </div>
                            <div class="form-group">
                                <label for="nokk">No KK</label>
                                <input type="text" class="form-control" id="nokk" name="nokk" maxlength="16" required>
                                <small id="nokkHelp" class="form-text text-muted">Masukkan 16 digit No KK</small>
                            </div>
                            <div class="form-group">
                                <label for="domisili">Domisili KTP</label>
                                <input type="text" class="form-control" id="domisili" name="domisili" required>
                            </div>
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                            </div>
                            <div class="form-group">
                                <label for="blok_rt">Blok RT</label>
                                <select class="form-control" id="blok_rt" name="blok_rt" required>
                                    <option value="">Pilih Blok RT</option>
                                    @foreach($blok_rt_list as $blok)
                                        <option value="{{ $blok }}">{{ $blok }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="">Pilih Gender</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Status Pernikahan</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Menikah">Menikah</option>
                                    <option value="Belum Menikah">Belum Menikah</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pekerjaan">Pekerjaan</label>
                                <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" required>
                            </div>
                            <div class="form-group">
                                <label for="pendidikan">Pendidikan Terakhir</label>
                                <input type="text" class="form-control" id="pendidikan" name="pendidikan" required>
                            </div>
                            <div class="form-group">
                                <label for="agama">Agama</label>
                                <input type="text" class="form-control" id="agama" name="agama" required>
                            </div>
                            <div class="form-group">
                                <label for="foto_ktp">Foto KTP</label>
                                <input type="file" class="form-control-file" id="foto_ktp" name="foto_ktp" accept="image/*,application/pdf">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="belum_punya_ktp" name="belum_punya_ktp">
                                    <label class="form-check-label" for="belum_punya_ktp">
                                        Belum punya KTP
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="foto_kk">Foto Kartu Keluarga (KK)</label>
                                <input type="file" class="form-control-file" id="foto_kk" name="foto_kk" accept="image/*,application/pdf" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="savePenduduk">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Data Penduduk -->
<div class="modal fade" id="editPendudukModal" tabindex="-1" role="dialog" aria-labelledby="editPendudukModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editPendudukForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editPendudukModalLabel">Edit Data Penduduk</h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nama">Nama</label>
                                <input type="text" class="form-control" id="edit_nama" name="nama" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_nik">NIK</label>
                                <input type="text" class="form-control" id="edit_nik" name="nik" maxlength="16" required>
                                <small id="editNikHelp" class="form-text text-muted">Masukkan 16 digit NIK</small>
                            </div>
                            <div class="form-group">
                                <label for="edit_nokk">No KK</label>
                                <input type="text" class="form-control" id="edit_nokk" name="nokk" maxlength="16" required>
                                <small id="editNokkHelp" class="form-text text-muted">Masukkan 16 digit No KK</small>
                            </div>
                            <div class="form-group">
                                <label for="edit_domisili">Domisili KTP</label>
                                <input type="text" class="form-control" id="edit_domisili" name="domisili" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="edit_tanggal_lahir" name="tanggal_lahir" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_blok_rt">Blok RT</label>
                                <select class="form-control" id="edit_blok_rt" name="blok_rt" required>
                                    <option value="">Pilih Blok RT</option>
                                    @foreach($blok_rt_list as $blok)
                                        <option value="{{ $blok }}">{{ $blok }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_gender">Gender</label>
                                <select class="form-control" id="edit_gender" name="gender" required>
                                    <option value="">Pilih Gender</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_status">Status Pernikahan</label>
                                <select class="form-control" id="edit_status" name="status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Menikah">Menikah</option>
                                    <option value="Belum Menikah">Belum Menikah</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_pekerjaan">Pekerjaan</label>
                                <input type="text" class="form-control" id="edit_pekerjaan" name="pekerjaan" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_pendidikan">Pendidikan Terakhir</label>
                                <input type="text" class="form-control" id="edit_pendidikan" name="pendidikan" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_agama">Agama</label>
                                <input type="text" class="form-control" id="edit_agama" name="agama" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_foto_ktp">Foto KTP (kosongkan jika tidak diubah)</label>
                                <input type="file" class="form-control-file" id="edit_foto_ktp" name="foto_ktp" accept="image/*,application/pdf">
                            </div>
                            <div class="form-group">
                                <label for="edit_foto_kk">Foto Kartu Keluarga (KK) (kosongkan jika tidak diubah)</label>
                                <input type="file" class="form-control-file" id="edit_foto_kk" name="foto_kk" accept="image/*,application/pdf">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" id="updatePenduduk">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

<!-- Custom JavaScript -->
<script src="{{ asset('js/rt/datapenduduk.js') }}"></script>
@endsection
