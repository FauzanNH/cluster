@extends('RTtemplate')

@section('title', 'Data Rumah Warga')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/rt/datarumah.css') }}">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<h3 class="section-header"><i class="fas fa-home me-2"></i>Data Rumah Warga</h3>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Data Rumah</h5>
            <button class="btn btn-success" data-toggle="modal" data-target="#tambahRumahModal"><i class="fas fa-plus"></i> Tambah Data Rumah</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped" id="rumahTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>ID Rumah</th>
                            <th>Akun Yang dihubungkan</th>
                            <th>Blok RT</th>
                            <th>Total Anggota Keluarga</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($datarumah as $rumah)
                        @php
                            $anggotaArr = collect([
                                $rumah->anggota1 ? $rumah->anggota1->nama . ' (' . $rumah->anggota1->warga_id . ')' : null,
                                $rumah->anggota2 ? $rumah->anggota2->nama . ' (' . $rumah->anggota2->warga_id . ')' : null,
                                $rumah->anggota3 ? $rumah->anggota3->nama . ' (' . $rumah->anggota3->warga_id . ')' : null,
                                $rumah->anggota4 ? $rumah->anggota4->nama . ' (' . $rumah->anggota4->warga_id . ')' : null,
                                $rumah->anggota5 ? $rumah->anggota5->nama . ' (' . $rumah->anggota5->warga_id . ')' : null,
                            ])->filter()->values();
                        @endphp
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $rumah->rumah_id }}</td>
                            <td>{{ $rumah->kepalaKeluarga ? $rumah->kepalaKeluarga->nama . ' (' . $rumah->kepalaKeluarga->users_id . ')' : '-' }}</td>
                            <td>{{ $rumah->blok_rt }}</td>
                            <td>
                                @php
                                    $anggota = collect([
                                        $rumah->anggota1,
                                        $rumah->anggota2,
                                        $rumah->anggota3,
                                        $rumah->anggota4,
                                        $rumah->anggota5
                                    ])->filter();
                                @endphp
                                {{ $anggota->count() }}
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-info view-detail" data-toggle="tooltip" title="Detail"
                                        data-id_rumah="{{ $rumah->rumah_id }}"
                                        data-status_kepemilikan="{{ $rumah->status_kepemilikan }}"
                                        data-alamat="{{ $rumah->alamat_cluster }}"
                                        data-kepala_keluarga="{{ $rumah->kepalaKeluarga ? $rumah->kepalaKeluarga->nama . ' (' . $rumah->kepalaKeluarga->users_id . ')' : '-' }}"
                                        data-anggota_list='@json($anggotaArr)'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-edit" data-toggle="tooltip" title="Edit"
                                        data-rumah_id="{{ $rumah->rumah_id }}"
                                        data-users_id="{{ $rumah->users_id }}"
                                        data-warga_id1="{{ $rumah->warga_id1 }}"
                                        data-warga_id2="{{ $rumah->warga_id2 }}"
                                        data-warga_id3="{{ $rumah->warga_id3 }}"
                                        data-warga_id4="{{ $rumah->warga_id4 }}"
                                        data-warga_id5="{{ $rumah->warga_id5 }}"
                                        data-blok_rt="{{ $rumah->blok_rt }}"
                                        data-status_kepemilikan="{{ $rumah->status_kepemilikan }}"
                                        data-alamat_cluster="{{ $rumah->alamat_cluster }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('rt.DataWarga.datarumah.destroy', $rumah->rumah_id) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-delete" data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i></button>
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

<!-- Modal Detail Rumah -->
<div class="modal fade" id="detailRumahModal" tabindex="-1" role="dialog" aria-labelledby="detailRumahModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailRumahModalLabel">Detail Rumah</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>ID Rumah:</strong> <span id="detail-id_rumah"></span>
                </div>
                <div class="mb-3">
                    <strong>Akun Yang dihubungkan:</strong> <span id="detail-kepala-keluarga"></span>
                </div>
                <div class="mb-3">
                    <strong>Anggota Keluarga:</strong>
                    <ol id="detail-anggota-list"></ol>
                </div>
                <div class="mb-3">
                    <strong>Status Kepemilikan:</strong> <span id="detail-status_kepemilikan"></span>
                </div>
                <div class="mb-3">
                    <strong>Alamat Lengkap:</strong> <span id="detail-alamat"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Data Rumah -->
<div class="modal fade" id="tambahRumahModal" tabindex="-1" role="dialog" aria-labelledby="tambahRumahModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('rt.DataWarga.datarumah.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="tambahRumahModalLabel">Tambah Data Rumah</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rumah_id">Rumah ID</label>
                        <input type="text" class="form-control" name="rumah_id" required>
                    </div>
                    <div class="form-group">
                        <label for="no_kk">No KK</label>
                        <input type="text" class="form-control" name="no_kk">
                    </div>
                    <div class="form-group">
                        <label for="users_id">Akun Yang dihubungkan</label>
                        <select class="form-control select2" name="users_id" required>
                            <option value="">-- Pilih Akun --</option>
                            @foreach($users as $user)
                                @if(!in_array($user->users_id, $usedUsersIds))
                                <option value="{{ $user->users_id }}">{{ $user->nama }} ({{ $user->users_id }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @for($i=1; $i<=5; $i++)
                    <div class="form-group">
                        <label for="warga_id{{ $i }}">Anggota Keluarga {{ $i }}</label>
                        <select class="form-control select2" name="warga_id{{ $i }}">
                            <option value="">-- Pilih Anggota Keluarga --</option>
                            @foreach($warga as $w)
                                @if(!isset($usedWargaIds[$w->warga_id]))
                                <option value="{{ $w->warga_id }}">{{ $w->nama }} ({{ $w->warga_id }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endfor
                    <div class="form-group">
                        <label for="blok_rt">Blok RT</label>
                        <input type="text" class="form-control" name="blok_rt" required>
                    </div>
                    <div class="form-group">
                        <label for="status_kepemilikan">Status Kepemilikan</label>
                        <select class="form-control" name="status_kepemilikan" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Milik Pribadi">Milik Pribadi</option>
                            <option value="Sewa Bulanan/Tahunan">Sewa Bulanan/Tahunan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="alamat_cluster">Alamat Cluster</label>
                        <input type="text" class="form-control" name="alamat_cluster" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Data Rumah -->
<div class="modal fade" id="editRumahModal" tabindex="-1" role="dialog" aria-labelledby="editRumahModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formEditRumah" method="POST">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editRumahModalLabel">Edit Data Rumah</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="rumah_id" id="edit-rumah_id">
                    <div class="form-group">
                        <label for="edit-users_id">Akun Yang dihubungkan</label>
                        <select class="form-control select2" name="users_id" id="edit-users_id" required>
                            <option value="">-- Pilih Akun --</option>
                            <!-- Users akan diisi melalui JavaScript -->
                        </select>
                    </div>
                    @for($i=1; $i<=5; $i++)
                    <div class="form-group">
                        <label for="edit-warga_id{{ $i }}">Anggota Keluarga {{ $i }}</label>
                        <select class="form-control select2" name="warga_id{{ $i }}" id="edit-warga_id{{ $i }}">
                            <option value="">-- Pilih Anggota Keluarga --</option>
                            <!-- Warga akan diisi melalui JavaScript -->
                        </select>
                    </div>
                    @endfor
                    <div class="form-group">
                        <label for="edit-blok_rt">Blok RT</label>
                        <input type="text" class="form-control" name="blok_rt" id="edit-blok_rt" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-status_kepemilikan">Status Kepemilikan</label>
                        <select class="form-control" name="status_kepemilikan" id="edit-status_kepemilikan" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Milik Pribadi">Milik Pribadi</option>
                            <option value="Sewa Bulanan/Tahunan">Sewa Bulanan/Tahunan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-alamat_cluster">Alamat Cluster</label>
                        <input type="text" class="form-control" name="alamat_cluster" id="edit-alamat_cluster" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<!-- jQuery and Bootstrap JS (required for modal) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom JavaScript -->
<script src="{{ asset('js/rt/datarumah.js') }}"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk modal tambah
    $('.select2').select2({
        dropdownParent: $('#tambahRumahModal'),
        width: '100%',
        placeholder: 'Cari...',
        allowClear: true
    });
    
    // Data users dan warga
    var allUsers = @json($users);
    var allWarga = @json($warga);
    var usedUsersIds = @json($usedUsersIds);
    var usedWargaIds = @json($usedWargaIds);
    var datarumah = @json($datarumah);
    var usersMap = @json($usersMap);
    var wargaMap = @json($wargaMap);
    
    // Debug - tampilkan di console untuk memastikan data benar
    console.log('Data Rumah:', datarumah);
    console.log('Users Map:', usersMap);
    console.log('Warga Map:', wargaMap);

    // Handler for view detail button
    $(document).on('click', '.view-detail', function() {
        const id_rumah = $(this).data('id_rumah');
        const status_kepemilikan = $(this).data('status_kepemilikan');
        const alamat = $(this).data('alamat');
        const kepalaKeluarga = $(this).data('kepala_keluarga');
        const anggotaList = $(this).data('anggota_list') || [];
        $('#detail-id_rumah').text(id_rumah);
        $('#detail-status_kepemilikan').text(status_kepemilikan);
        $('#detail-alamat').text(alamat);
        $('#detail-kepala-keluarga').text(kepalaKeluarga || '-');
        let anggotaHtml = '';
        if (anggotaList.length > 0) {
            anggotaList.forEach(function(nama) {
                anggotaHtml += '<li>' + nama + '</li>';
            });
        } else {
            anggotaHtml = '<li>-</li>';
        }
        $('#detail-anggota-list').html(anggotaHtml);
        $('#detailRumahModal').modal('show');
    });

    // Edit button handler
    $(document).on('click', '.btn-edit', function() {
        const rumah_id = $(this).data('rumah_id');
        
        // Cari data rumah yang sedang diedit
        let currentRumah = null;
        datarumah.forEach(function(rumah) {
            if (rumah.rumah_id === rumah_id) {
                currentRumah = rumah;
            }
        });
        
        // Jika data rumah tidak ditemukan, keluar dari fungsi
        if (!currentRumah) {
            console.error('Data rumah tidak ditemukan:', rumah_id);
            return;
        }
        
        console.log('Current Rumah:', currentRumah);
        
        // Set nilai rumah_id
        $('#edit-rumah_id').val(rumah_id);
        
        // Dapatkan current_users_id dari data rumah
        const current_users_id = currentRumah.users_id;
        
        // Dapatkan current_warga_ids dari data rumah
        const currentWargaIds = [];
        for (let i = 1; i <= 5; i++) {
            const fieldName = 'warga_id' + i;
            if (currentRumah[fieldName]) {
                currentWargaIds.push(currentRumah[fieldName]);
            }
        }
        
        console.log('Current Users ID:', current_users_id);
        console.log('Current Warga IDs:', currentWargaIds);
        
        // Inisialisasi Select2 khusus untuk modal edit
        $('#edit-users_id').select2({
            dropdownParent: $('#editRumahModal'),
            width: '100%',
            placeholder: 'Cari Akun...',
            allowClear: true
        }).empty();
        
        // Inisialisasi Select2 untuk anggota keluarga di modal edit
        for(let i=1; i<=5; i++) {
            $('#edit-warga_id'+i).select2({
                dropdownParent: $('#editRumahModal'),
                width: '100%',
                placeholder: 'Cari Anggota Keluarga...',
                allowClear: true
            }).empty();
        }
        
        // Tambahkan opsi default ke dropdown users_id
        $('#edit-users_id').append(new Option('-- Pilih Akun Yang dihubungkan --', '', true, false));
        
        // Tambahkan user saat ini sebagai opsi pertama jika ada
        if (current_users_id && usersMap[current_users_id]) {
            const currentUser = usersMap[current_users_id];
            const optionText = currentUser.nama + ' (' + currentUser.users_id + ') - Akun Saat Ini';
            const option = new Option(optionText, currentUser.users_id, false, true);
            $('#edit-users_id').append(option);
        }
        
        // Tambahkan semua users yang belum digunakan
        allUsers.forEach(function(user) {
            // Jika bukan user saat ini dan belum digunakan
            if (user.users_id !== current_users_id && !usedUsersIds.includes(user.users_id)) {
                const optionText = user.nama + ' (' + user.users_id + ')';
                const option = new Option(optionText, user.users_id, false, false);
                $('#edit-users_id').append(option);
            }
        });
        
        // Set nilai users_id ke user saat ini
        $('#edit-users_id').val(current_users_id).trigger('change');
        
        // Kosongkan dan isi ulang dropdown warga_id untuk setiap anggota keluarga
        for(let i=1; i<=5; i++) {
            const fieldName = 'warga_id' + i;
            const currentWargaId = currentRumah[fieldName];
            
            // Tambahkan opsi default
            $('#edit-warga_id'+i).append(new Option('-- Pilih Anggota Keluarga --', '', true, false));
            
            // Tambahkan warga saat ini jika ada
            if (currentWargaId && wargaMap[currentWargaId]) {
                const currentWarga = wargaMap[currentWargaId];
                const optionText = currentWarga.nama + ' (' + currentWarga.warga_id + ') - Anggota Saat Ini';
                const option = new Option(optionText, currentWarga.warga_id, false, true);
                $('#edit-warga_id'+i).append(option);
            }
            
            // Tambahkan semua warga yang belum digunakan
            allWarga.forEach(function(warga) {
                // Jika bukan warga saat ini dan belum digunakan
                if (warga.warga_id !== currentWargaId && !usedWargaIds[warga.warga_id]) {
                    const optionText = warga.nama + ' (' + warga.warga_id + ')';
                    const option = new Option(optionText, warga.warga_id, false, false);
                    $('#edit-warga_id'+i).append(option);
                }
            });
            
            // Set nilai warga_id
            $('#edit-warga_id'+i).val(currentWargaId).trigger('change');
        }
        
        // Set nilai lainnya
        $('#edit-blok_rt').val(currentRumah.blok_rt);
        $('#edit-status_kepemilikan').val(currentRumah.status_kepemilikan);
        $('#edit-alamat_cluster').val(currentRumah.alamat_cluster);
        
        // Set form action
        $('#formEditRumah').attr('action', '/rt/datarumah/' + rumah_id);
        $('#editRumahModal').modal('show');
    });

    // Delete button handler
    $(document).on('click', '.btn-delete', function() {
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Yakin hapus data rumah?',
            text: 'Data yang dihapus tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection
