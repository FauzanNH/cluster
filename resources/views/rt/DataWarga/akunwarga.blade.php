@extends('RTtemplate')

@section('title', 'Akun Warga')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/rt/akunwarga.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
@endsection

@section('content')
<div class="container-fluid px-4">
    <h3 class="section-header"><i class="fas fa-user me-2"></i>Akun Kepala Keluarga</h3>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Data Akun Warga</h6>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAkunWargaModal">
                        <i class="fas fa-plus mr-2"></i> Tambah Akun Warga
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="akunWargaTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th>Gender</th>
                                    <th class="text-center" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($warga as $index => $user)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $user->users_id }}</td>
                                    <td>{{ $user->nama }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->no_hp }}</td>
                                    <td>{{ $user->gender }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-info view-details" data-toggle="tooltip" title="Detail"
                                                data-id="{{ $user->users_id }}"
                                                data-nama="{{ $user->nama }}"
                                                data-email="{{ $user->email }}"
                                                data-nohp="{{ $user->no_hp }}"
                                                data-gender="{{ $user->gender }}"
                                                data-role="{{ $user->role }}"
                                                data-rt-blok="{{ $user->rt_blok }}"
                                                data-created-at="{{ $user->created_at ? $user->created_at->format('d-m-Y') : '' }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        
                                            <button type="button" class="btn btn-secondary change-password-btn" data-id="{{ $user->users_id }}" data-nama="{{ $user->nama }}" data-bs-toggle="tooltip" title="Ganti Password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <form action="{{ route('rt.DataWarga.akunwarga.destroy', $user->users_id) }}" method="POST" class="d-inline delete-akunwarga-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" data-toggle="tooltip" title="Hapus">
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
    </div>
</div>

<!-- Detail Akun Warga Modal -->
<div class="modal fade" id="detailAkunWargaModal" tabindex="-1" role="dialog" aria-labelledby="detailAkunWargaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailAkunWargaModalLabel">Detail Akun Warga</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="detail-header mb-4">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="profile-image">
                                <i class="fas fa-user-circle fa-5x text-info"></i>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <h4 id="detail-nama" class="mb-1 font-weight-bold">Ahmad Sudrajat</h4>
                            <p id="detail-id" class="mb-0"><span class="badge badge-info">ID:</span> USR001</p>
                            <p id="detail-email" class="mb-1"><span class="badge badge-info">Email:</span> ahmad.sudrajat@example.com</p>
                            <p id="detail-role-badge" class="mb-0"><span class="badge badge-primary">Warga</span></p>
                        </div>
                    </div>
                </div>

                <div class="detail-content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h6 class="section-title"><i class="fas fa-id-card mr-2"></i>Informasi Akun</h6>
                                <div class="section-content">
                                    <div class="detail-item">
                                        <span class="detail-label">No HP</span>
                                        <span id="detail-nohp" class="detail-value">081234567891</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Gender</span>
                                        <span id="detail-gender" class="detail-value">Laki-laki</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Role</span>
                                        <span id="detail-role" class="detail-value">Warga</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h6 class="section-title"><i class="fas fa-map-marker-alt mr-2"></i>Informasi Lokasi</h6>
                                <div class="section-content">
                                    <div class="detail-item">
                                        <span class="detail-label">Dibuat Pada</span>
                                        <span id="detail-created-at" class="detail-value">15-08-2023</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning edit-from-detail">
                    <i class="fas fa-edit mr-1"></i> Edit Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Akun Warga -->
<div class="modal fade" id="editAkunWargaModal" tabindex="-1" role="dialog" aria-labelledby="editAkunWargaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editAkunWargaModalLabel">Edit Akun Warga</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAkunWargaForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <input type="hidden" name="users_id" id="edit-users_id">
                    <div class="form-group row">
                        <label for="edit-nama" class="col-sm-3 col-form-label">Nama Lengkap</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="edit-nama" name="nama" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit-email" class="col-sm-3 col-form-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="edit-email" name="email" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit-no_hp" class="col-sm-3 col-form-label">No HP</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="edit-no_hp" name="no_hp" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit-gender" class="col-sm-3 col-form-label">Gender</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="edit-gender" name="gender" required>
                                <option value="laki-laki">Laki-laki</option>
                                <option value="perempuan">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit-alamat" class="col-sm-3 col-form-label">Alamat</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="edit-alamat" name="alamat" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit-password" class="col-sm-3 col-form-label">Password (opsional)</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="edit-password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Akun Warga Modal -->
<div class="modal fade" id="addAkunWargaModal" tabindex="-1" role="dialog" aria-labelledby="addAkunWargaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addAkunWargaModalLabel">Tambah Akun Warga</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addAkunWargaForm" action="{{ route('rt.DataWarga.akunwarga.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="Warga">
                    <div class="form-group row">
                        <label for="nama" class="col-sm-3 col-form-label">Nama Lengkap</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-sm-3 col-form-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="no_hp" class="col-sm-3 col-form-label">No HP</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="gender" class="col-sm-3 col-form-label">Gender</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Pilih Gender</option>
                                <option value="laki-laki">Laki-laki</option>
                                <option value="perempuan">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="alamat" class="col-sm-3 col-form-label">Alamat</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="alamat" name="alamat" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-sm-3 col-form-label">Password</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password_confirmation" class="col-sm-3 col-form-label">Konfirmasi Password</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-9 offset-sm-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="agreement" id="agreement" required>
                                <label class="form-check-label" for="agreement">
                                    Saya Bersedia dan bertanggung jawab atas data yang saya masukkan dan peraturan yang ditetapkan oleh Bukit Indah Cluster.
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" form="addAkunWargaForm">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ganti Password Akun Warga -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="changePasswordModalLabel">Ganti Password Akun Warga</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changePasswordForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <input type="hidden" name="users_id" id="change-password-users_id">
                    <div class="mb-3">
                        <label for="change-password-nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="change-password-nama" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="change-password-password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="change-password-password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="change-password-password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="change-password-password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-save mr-1"></i> Simpan Password
                    </button>
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
<script src="{{ asset('js/rt/akunwarga.js') }}"></script>
@endsection

