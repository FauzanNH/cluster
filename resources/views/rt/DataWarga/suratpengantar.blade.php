@extends('RTtemplate')

@section('title', 'Surat Pengantar')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/rt/suratpengantar.css') }}">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
@endsection

@section('content')
<h3 class="section-header"><i class="fas fa-file-alt me-2"></i>Surat Pengantar</h3>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Data Surat Pengantar</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped" id="suratPengantarTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Blok RT</th>
                            <th>Jenis Surat</th>
                            <th>Status Validasi</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suratpengantar as $i => $surat)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $surat->nama }}</td>
                            <td>{{ $surat->nik }}</td>
                            <td>{{ $surat->blok_rt }}</td>
                            <td>{{ $surat->jenis_surat }}</td>
                            <td class="text-center">
                                @if($surat->status_penegerjaan == 'disetujui')
                                    <span class="status-badge status-validated">Disetujui</span>
                                @elseif($surat->status_penegerjaan == 'menunggu verifikasi')
                                    <span class="status-badge status-pending">Menunggu verifikasi</span>
                                @elseif($surat->status_penegerjaan == 'ditolak')
                                    <span class="status-badge status-rejected">Ditolak</span>
                                @else
                                    <span class="status-badge status-pending">{{ ucfirst($surat->status_penegerjaan) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip" title="Detail"
                                        data-nama="{{ $surat->nama }}"
                                        data-nik="{{ $surat->nik }}"
                                        data-blok="{{ $surat->blok_rt }}"
                                        data-jenis="{{ $surat->jenis_surat }}"
                                        data-status="{{ $surat->status_penegerjaan }}"
                                        data-tanggal="{{ $surat->created_at ? $surat->created_at->toIso8601String() : '' }}"
                                        data-keperluan="{{ $surat->keperluan_keramaian }}"
                                        data-tempat="{{ $surat->tempat_keramaian }}"
                                        data-tglkeramaian="{{ $surat->tanggal_keramaian }}"
                                        data-jamkeramaian="{{ $surat->jam_keramaian }}"
                                        data-foto-ktp="{{ $surat->foto_ktp }}"
                                        data-kartu-keluarga="{{ $surat->kartu_keluarga }}"
                                        data-dokumen-lainnya1="{{ $surat->dokumen_lainnya1 }}"
                                        data-dokumen-lainnya2="{{ $surat->dokumen_lainnya2 }}"
                                        data-surat-id="{{ $surat->surat_id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-toggle="tooltip" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<!-- Detail Surat Pengantar Modal -->
<div class="modal fade" id="detailSuratModal" tabindex="-1" role="dialog" aria-labelledby="detailSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailSuratModalLabel">Detail Surat Pengantar</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="detail-header mb-4 p-3">
                    <div class="row">
                        <div class="col-md-1 text-center">
                            <div class="profile-image">
                                <i class="fas fa-file-alt fa-5x text-info"></i>
                            </div>
                        </div>
                        <div class="col-md-11">
                            <h4 id="detail-nama" class="mb-1 font-weight-bold"></h4>
                            <p id="detail-nik" class="mb-0"></p>
                            <p id="detail-jenis-surat" class="mb-0"></p>
                            <p id="detail-surat-id" class="mb-0"></p>
                        </div>
                    </div>
                </div>

                <div class="detail-content">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="detail-section">
                                <h6 class="section-title"><i class="fas fa-info-circle mr-2"></i>Informasi Surat</h6>
                                <div class="section-content">
                                    <div class="detail-item">
                                        <span class="detail-label">Tanggal Pengajuan</span>
                                        <span id="detail-tanggal" class="detail-value"></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Status</span>
                                        <span id="detail-status" class="detail-value"></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Blok/RT</span>
                                        <span id="detail-blok" class="detail-value"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="detail-section">
                                <h6 class="section-title"><i class="fas fa-clipboard-list mr-2"></i>Keterangan tambahan</h6>
                                <div class="section-content">
                                    <p id="detail-keterangan" class="mb-0">
                                        <!--isi keterangan -->
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Pendukung -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="detail-section">
                                <h6 class="section-title"><i class="fas fa-images mr-2"></i>Dokumen Pendukung</h6>
                                <div class="section-content">
                                    <div class="row document-gallery">
                                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                                            <div class="document-thumbnail">
                                                <div class="img-placeholder" id="ktp-placeholder">
                                                    <i class="fas fa-id-card fa-3x"></i>
                                                    <span>KTP</span>
                                                </div>
                                                <img id="foto-ktp-preview" class="img-fluid document-preview d-none" src="" alt="KTP">
                                                <div class="document-label">KTP</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                                            <div class="document-thumbnail">
                                                <div class="img-placeholder" id="kk-placeholder">
                                                    <i class="fas fa-users fa-3x"></i>
                                                    <span>Kartu Keluarga</span>
                                                </div>
                                                <img id="kartu-keluarga-preview" class="img-fluid document-preview d-none" src="" alt="Kartu Keluarga">
                                                <div class="document-label">Kartu Keluarga</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                                            <div class="document-thumbnail">
                                                <div class="img-placeholder" id="doc1-placeholder">
                                                    <i class="fas fa-file-invoice fa-3x"></i>
                                                    <span>Dokumen Lainnya 1</span>
                                                </div>
                                                <img id="dokumen-lainnya1-preview" class="img-fluid document-preview d-none" src="" alt="Dokumen Lainnya 1">
                                                <div class="document-label">Dokumen Lainnya 1</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                                            <div class="document-thumbnail">
                                                <div class="img-placeholder" id="doc2-placeholder">
                                                    <i class="fas fa-file-invoice fa-3x"></i>
                                                    <span>Dokumen Lainnya 2</span>
                                                </div>
                                                <img id="dokumen-lainnya2-preview" class="img-fluid document-preview d-none" src="" alt="Dokumen Lainnya 2">
                                                <div class="document-label">Dokumen Lainnya 2</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-3">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success d-none" id="btn-validasi-status">Validasi</button>
                <button type="button" class="btn btn-primary d-none" id="btn-selesai-status">Selesaikan validasi</button>
                <button type="button" class="btn btn-danger d-none" id="btn-tolak-status">Tolak</button>
            </div>
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
<script>
    $(document).ready(function() {
        $('#suratPengantarTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari data...",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "<i class='fas fa-chevron-right'></i>",
                    previous: "<i class='fas fa-chevron-left'></i>"
                },
            }
        });

        // Enable tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Show detail modal when detail button is clicked
        $(document).on('click', '.btn-info', function() {
            var btn = $(this);
            $('#detail-nama').text(btn.data('nama'));
            $('#detail-nik').html('<span class="badge badge-info">NIK:</span> ' + btn.data('nik'));
            $('#detail-jenis-surat').html('<span class="badge badge-info">Jenis Surat:</span> ' + btn.data('jenis'));
            $('#detail-surat-id').html('<span class="badge badge-info">ID Surat:</span> ' + btn.data('surat-id'));
            
            // Tanggal pengajuan: convert ke waktu lokal Asia/Jakarta
            var tanggalUtc = btn.data('tanggal');
            if (tanggalUtc) {
                var localDate = new Date(tanggalUtc);
                // Format: dd-mm-yyyy HH:ii
                var pad = n => n < 10 ? '0'+n : n;
                var tgl = pad(localDate.getDate()) + '-' + pad(localDate.getMonth()+1) + '-' + localDate.getFullYear();
                var jam = pad(localDate.getHours()) + ':' + pad(localDate.getMinutes());
                $('#detail-tanggal').text(tgl + ' ' + jam);
            } else {
                $('#detail-tanggal').text('-');
            }
            var status = btn.data('status');
            var statusHtml = '';
            if(status === 'disetujui') statusHtml = '<span class="status-badge status-validated">Divalidasi</span>';
            else if(status === 'menunggu verifikasi') statusHtml = '<span class="status-badge status-pending">Menunggu Verifikasi</span>';
            else if(status === 'ditolak') statusHtml = '<span class="status-badge status-rejected">Ditolak</span>';
            else statusHtml = '<span class="status-badge status-pending">'+status+'</span>';
            $('#detail-status').html(statusHtml);
            $('#detail-blok').text(btn.data('blok'));
            
            // Handling dokumen pendukung
            // KTP
            var fotoKtp = btn.data('foto-ktp');
            if (fotoKtp) {
                $('#ktp-placeholder').addClass('d-none');
                $('#foto-ktp-preview').attr('src', fotoKtp).removeClass('d-none');
            } else {
                $('#ktp-placeholder').removeClass('d-none');
                $('#foto-ktp-preview').addClass('d-none');
            }
            
            // Kartu Keluarga
            var kartuKeluarga = btn.data('kartu-keluarga');
            if (kartuKeluarga) {
                $('#kk-placeholder').addClass('d-none');
                $('#kartu-keluarga-preview').attr('src', kartuKeluarga).removeClass('d-none');
            } else {
                $('#kk-placeholder').removeClass('d-none');
                $('#kartu-keluarga-preview').addClass('d-none');
            }
            
            // Dokumen Lainnya 1
            var dokumenLainnya1 = btn.data('dokumen-lainnya1');
            if (dokumenLainnya1) {
                $('#doc1-placeholder').addClass('d-none');
                $('#dokumen-lainnya1-preview').attr('src', dokumenLainnya1).removeClass('d-none');
            } else {
                $('#doc1-placeholder').removeClass('d-none');
                $('#dokumen-lainnya1-preview').addClass('d-none');
            }
            
            // Dokumen Lainnya 2
            var dokumenLainnya2 = btn.data('dokumen-lainnya2');
            if (dokumenLainnya2) {
                $('#doc2-placeholder').addClass('d-none');
                $('#dokumen-lainnya2-preview').attr('src', dokumenLainnya2).removeClass('d-none');
            } else {
                $('#doc2-placeholder').removeClass('d-none');
                $('#dokumen-lainnya2-preview').addClass('d-none');
            }
            
            $('#detailSuratModal').modal('show');

            // Keterangan tambahan untuk Surat Izin Keramaian
            if(btn.data('jenis') === 'Surat Izin Keramaian') {
                var keterangan = '<div class="section-content">';
                
                keterangan += '<div class="detail-item">';
                keterangan += '<span class="detail-label">Keperluan Keramaian</span>';
                keterangan += '<span class="detail-value">' + (btn.data('keperluan') || '-') + '</span>';
                keterangan += '</div>';
                
                keterangan += '<div class="detail-item">';
                keterangan += '<span class="detail-label">Tempat Keramaian</span>';
                keterangan += '<span class="detail-value">' + (btn.data('tempat') || '-') + '</span>';
                keterangan += '</div>';
                
                keterangan += '<div class="detail-item">';
                keterangan += '<span class="detail-label">Tanggal Keramaian</span>';
                keterangan += '<span class="detail-value">' + (btn.data('tglkeramaian') || '-') + '</span>';
                keterangan += '</div>';
                
                keterangan += '<div class="detail-item">';
                keterangan += '<span class="detail-label">Jam Keramaian</span>';
                keterangan += '<span class="detail-value">' + (btn.data('jamkeramaian') || '-') + '</span>';
                keterangan += '</div>';
                
                keterangan += '</div>';
                
                $('#detail-keterangan').html(keterangan);
            } else {
                $('#detail-keterangan').html('');
            }

            // Tampilkan tombol aksi status sesuai status_penegerjaan
            $('#btn-validasi-status').addClass('d-none');
            $('#btn-selesai-status').addClass('d-none');
            $('#btn-tolak-status').addClass('d-none');
            if(status === 'menunggu verifikasi') {
                $('#btn-validasi-status').removeClass('d-none');
                $('#btn-tolak-status').removeClass('d-none');
            } else if(status === 'sedang di validasi') {
                $('#btn-selesai-status').removeClass('d-none');
            }
        });
        
        // Add click event for document preview to open in new tab/full size
        $('.document-preview').on('click', function() {
            var imgSrc = $(this).attr('src');
            if (imgSrc) {
                window.open(imgSrc, '_blank');
            }
        });

        // Tombol aksi update status
        function updateStatusSurat(status) {
            var suratId = $('#detail-surat-id').text().replace('ID Surat:', '').trim();
            $.ajax({
                url: "{{ route('rt.suratpengantar.updateStatus') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    surat_id: suratId,
                    status_penegerjaan: status
                },
                success: function(res) {
                    if(res.success) {
                        alert('Status berhasil diupdate!');
                        location.reload();
                    } else {
                        alert(res.message || 'Gagal update status');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat update status');
                }
            });
        }
        $('#btn-validasi-status').on('click', function() {
            updateStatusSurat('sedang di validasi');
        });
        $('#btn-selesai-status').on('click', function() {
            updateStatusSurat('disetujui');
        });
        $('#btn-tolak-status').on('click', function() {
            updateStatusSurat('ditolak');
        });
    });
</script>
@endsection

<style>
    /* Add this to your styles */
    .document-preview {
        cursor: pointer;
        max-height: 150px;
        object-fit: contain;
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .document-thumbnail {
        text-align: center;
    }
</style>

