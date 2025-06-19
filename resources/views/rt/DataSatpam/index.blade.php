@extends('RTtemplate')

@section('title', 'Data Satpam')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/rt/datasatpam.css') }}">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
@endsection

@section('content')
<h3 class="section-header"><i class="fas fa-user-shield me-2"></i>Data Satpam</h3>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-primary" id="btn-tambah-satpam" data-bs-toggle="modal" data-bs-target="#modalTambahSatpam" {{ count($satpams) >= 8 ? 'disabled' : '' }}>
                <i class="fas fa-plus mr-2"></i> Tambah Data Satpam
            </button>
                <div class="ms-3 satpam-counter">
                    <span class="badge {{ count($satpams) >= 8 ? 'bg-danger' : 'bg-info' }}">
                        <i class="fas fa-users me-1"></i> {{ count($satpams) }}/8 Satpam
                    </span>
                    @if(count($satpams) >= 8)
                        <small class="text-danger ms-2">Batas maksimum tercapai</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped" id="satpamTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th>No Hp</th>
                            <th>Gender</th>
                            <th class="text-center" width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($satpams as $satpam)
                        <tr>
                            <td class="text-center">{{ $satpam->users_id }}</td>
                            <td>{{ $satpam->nama }}</td>
                            <td>{{ $satpam->email }}</td>
                            <td>{{ $satpam->alamat }}</td>
                            <td>{{ $satpam->no_hp }}</td>
                            <td>{{ $satpam->gender }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info btn-sm btn-detail-satpam" data-users_id="{{ $satpam->users_id }}" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" title="Ganti Password">
                                    <i class="fas fa-key"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data satpam.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal Detail Satpam -->
<div class="modal fade" id="modalDetailSatpam" tabindex="-1" aria-labelledby="modalDetailSatpamLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalDetailSatpamLabel">Detail Satpam</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formEditSatpam">
        <table class="table table-borderless mb-0">
          <tr>
            <th style="width: 40%">NIK</th>
            <td>
              <span id="detail-nik"></span>
              <input type="text" class="form-control d-none" id="input-nik" name="nik" maxlength="16">
            </td>
          </tr>
          <tr>
            <th>Tanggal Lahir</th>
            <td>
              <span id="detail-tanggal-lahir"></span>
              <input type="date" class="form-control d-none" id="input-tanggal-lahir" name="tanggal_lahir">
            </td>
          </tr>
          <tr>
            <th>No KEP</th>
            <td>
              <span id="detail-no-kep"></span>
              <input type="text" class="form-control d-none" id="input-no-kep" name="no_kep">
            </td>
          </tr>
          <tr>
            <th>Seksi Unit Gerbang</th>
            <td>
              <span id="detail-seksi-unit"></span>
              <select class="form-control d-none" id="input-seksi-unit" name="seksi_unit_gerbang">
                <option value="">Pilih Seksi Unit</option>
                <option value="Gerbang Utama">Gerbang Utama</option>
                <option value="Gerbang Belakang">Gerbang Belakang</option>
                <option value="Gerbang Timur">Gerbang Timur</option>
                <option value="Gerbang Barat">Gerbang Barat</option>
              </select>
            </td>
          </tr>
        </table>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary" id="btn-edit-satpam">Edit</button>
        <button type="button" class="btn btn-success d-none" id="btn-simpan-satpam">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Hapus Satpam -->
<div class="modal fade" id="modalHapusSatpam" tabindex="-1" aria-labelledby="modalHapusSatpamLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalHapusSatpamLabel">Hapus Akun Satpam</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus akun satpam ini? Tindakan ini tidak dapat dibatalkan.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="btn-confirm-hapus-satpam">Hapus</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Ganti Password Satpam -->
<div class="modal fade" id="modalPasswordSatpam" tabindex="-1" aria-labelledby="modalPasswordSatpamLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="modalPasswordSatpamLabel">Ganti Password Satpam</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formPasswordSatpam">
          <div class="mb-3">
            <label for="password-baru" class="form-label">Password Baru</label>
            <input type="password" class="form-control" id="password-baru" name="password" required minlength="6">
          </div>
          <div class="mb-3">
            <label for="password-konfirmasi" class="form-label">Konfirmasi Password Baru</label>
            <input type="password" class="form-control" id="password-konfirmasi" name="password_confirmation" required minlength="6">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-success" id="btn-simpan-password-satpam">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Tambah Satpam -->
<div class="modal fade" id="modalTambahSatpam" tabindex="-1" aria-labelledby="modalTambahSatpamLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalTambahSatpamLabel">Tambah Data Satpam</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @if(count($satpams) >= 8)
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-triangle me-2"></i> Jumlah maksimal satpam (8 orang) telah tercapai. Hapus data satpam yang tidak aktif terlebih dahulu.
        </div>
        @endif
        <form id="formTambahSatpam" {{ count($satpams) >= 8 ? 'class=disabled-form' : '' }}>
          <div class="mb-3">
            <label for="tambah-nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="tambah-nama" name="nama" required {{ count($satpams) >= 8 ? 'disabled' : '' }}>
          </div>
          <div class="mb-3">
            <label for="tambah-email" class="form-label">Email</label>
            <input type="email" class="form-control" id="tambah-email" name="email" required {{ count($satpams) >= 8 ? 'disabled' : '' }}>
          </div>
          <div class="mb-3">
            <label for="tambah-no_hp" class="form-label">No HP</label>
            <input type="text" class="form-control" id="tambah-no_hp" name="no_hp" required {{ count($satpams) >= 8 ? 'disabled' : '' }}>
          </div>
          <div class="mb-3">
            <label for="tambah-gender" class="form-label">Gender</label>
            <select class="form-control" id="tambah-gender" name="gender" required {{ count($satpams) >= 8 ? 'disabled' : '' }}>
              <option value="">Pilih Gender</option>
              <option value="laki-laki">Laki-laki</option>
              <option value="perempuan">Perempuan</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="tambah-alamat" class="form-label">Alamat</label>
            <input type="text" class="form-control" id="tambah-alamat" name="alamat" required {{ count($satpams) >= 8 ? 'disabled' : '' }}>
          </div>
          <div class="mb-3">
            <label for="tambah-password" class="form-label">Password</label>
            <input type="password" class="form-control" id="tambah-password" name="password" required minlength="6" {{ count($satpams) >= 8 ? 'disabled' : '' }}>
          </div>
          <div class="mb-3">
            <label for="tambah-password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" id="tambah-password_confirmation" name="password_confirmation" required minlength="6" {{ count($satpams) >= 8 ? 'disabled' : '' }}>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btn-simpan-tambah-satpam" {{ count($satpams) >= 8 ? 'disabled' : '' }}>Simpan</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<!-- DataTables JS -->
<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Custom JavaScript -->
<script src="{{ asset('js/rt/DataSatpam/infosatpam.js') }}"></script>
<script src="{{ asset('js/rt/datasatpam.js') }}"></script>
<style>
  .satpam-counter .badge {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
  }
  .disabled-form {
    opacity: 0.6;
    pointer-events: none;
  }
</style>
@endsection
