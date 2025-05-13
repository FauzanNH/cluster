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
                        <!-- Example data row 1 -->
                        <tr>
                            <td class="text-center">1</td>
                            <td>Ahmad Sudrajat</td>
                            <td>3201010101010001</td>
                            <td>Blok A2/10</td>
                            <td>Surat Keterangan Domisili</td>
                            <td class="text-center">
                                <span class="status-badge status-validated">Divalidasi</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning" data-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-toggle="tooltip" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Example data row 2 -->
                        <tr>
                            <td class="text-center">2</td>
                            <td>Siti Nurhaliza</td>
                            <td>3201020202020002</td>
                            <td>Blok C3/05</td>
                            <td>Surat Keterangan Usaha</td>
                            <td class="text-center">
                                <span class="status-badge status-pending">Menunggu</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning" data-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-toggle="tooltip" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Example data row 3 -->
                        <tr>
                            <td class="text-center">3</td>
                            <td>Budi Santoso</td>
                            <td>3201030303030003</td>
                            <td>Blok B1/08</td>
                            <td>Surat Pengantar SKCK</td>
                            <td class="text-center">
                                <span class="status-badge status-rejected">Ditolak</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning" data-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-toggle="tooltip" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
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
                            <h4 id="detail-nama" class="mb-1 font-weight-bold">Ahmad Sudrajat</h4>
                            <p id="detail-nik" class="mb-0"><span class="badge badge-info">NIK:</span> 3201010101010001</p>
                            <p id="detail-jenis-surat" class="mb-0"><span class="badge badge-info">Jenis Surat:</span> Surat Keterangan Domisili</p>
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
                                        <span id="detail-tanggal" class="detail-value">01-06-2024</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Status</span>
                                        <span id="detail-status" class="detail-value">
                                            <span class="status-badge status-validated">Divalidasi</span>
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Blok/RT</span>
                                        <span id="detail-blok" class="detail-value">Blok A2/10</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="detail-section">
                                <h6 class="section-title"><i class="fas fa-clipboard-list mr-2"></i>Keterangan</h6>
                                <div class="section-content">
                                    <p id="detail-keterangan" class="mb-0">
                                        Surat pengantar ini diajukan untuk keperluan administrasi di kantor kecamatan.
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
                                                <div class="img-placeholder">
                                                    <i class="fas fa-id-card fa-3x"></i>
                                                    <span>KTP</span>
                                                </div>
                                                <div class="document-label">KTP</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                                            <div class="document-thumbnail">
                                                <div class="img-placeholder">
                                                    <i class="fas fa-users fa-3x"></i>
                                                    <span>Kartu Keluarga</span>
                                                </div>
                                                <div class="document-label">Kartu Keluarga</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                                            <div class="document-thumbnail">
                                                <div class="img-placeholder">
                                                    <i class="fas fa-file-invoice fa-3x"></i>
                                                    <span>Dokumen Lainnya</span>
                                                </div>
                                                <div class="document-label">Dokumen Lainnya</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                                            <div class="document-thumbnail">
                                                <div class="img-placeholder">
                                                    <i class="fas fa-file-invoice fa-3x"></i>
                                                    <span>Dokumen lainnya</span>
                                                </div>
                                                <div class="document-label">Dokumen Lainnya</div>
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
                <button type="button" class="btn btn-warning edit-from-detail btn-lg">
                    <i class="fas fa-edit mr-1"></i> Edit Surat
                </button>
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
            $('#detailSuratModal').modal('show');
        });
    });
</script>
@endsection

