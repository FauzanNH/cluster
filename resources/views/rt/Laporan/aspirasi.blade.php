@extends('RTtemplate')

@section('title', 'Aspirasi Warga')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('css/rt/keluhan.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="page-title">Aspirasi Warga</h2>
            <p class="page-subtitle">Daftar aspirasi yang disampaikan oleh warga</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="search-container">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari...">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <div class="table-responsive">
                    <table id="aspirasiTable" class="table modern-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama</th>
                                <th>Pengaduan ID</th>
                                <th>Jenis Pengaduan</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aspirasi as $i => $item)
                            <tr>
                                <td class="text-center">
                                    <span class="number-badge">{{ $i+1 }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-success-gradient">
                                            <span>{{ strtoupper(\Illuminate\Support\Str::substr($item->nama_pelapor,0,2)) }}</span>
                                        </div>
                                        <div class="ms-3">
                                            <span class="mb-0">{{ $item->nama_pelapor }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="id-pill">
                                        <i class="fas fa-hashtag me-1"></i>
                                        <span>{{ $item->pengaduan_id }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="category-tag cleanliness">
                                        <i class="fas fa-broom me-1"></i> Aspirasi
                                    </div>
                                </td>
                                <td>
                                    <div class="status-pill {{ $item->status_pengaduan == 'Tersampaikan' ? 'new' : 'read' }}">
                                        <i class="fas fa-circle me-1"></i>
                                        <span>{{ $item->status_pengaduan == 'Tersampaikan' ? 'Perlu Tindakan' : $item->status_pengaduan }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button class="action-btn view-btn" data-id="{{ $item->pengaduan_id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center text-muted">Tidak ada data aspirasi</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="table-footer mt-3">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <div class="status-legend">
                            <div class="legend-item">
                                <span class="legend-dot new"></span>
                                <span class="legend-text">Tersampaikan</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-dot read"></span>
                                <span class="legend-text">Dibaca RT</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="pagination-info text-md-end">
                            <div id="paginationInfo" class="text-muted"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Aspirasi -->
<div class="modal fade" id="aspirasiDetailModal" tabindex="-1" aria-labelledby="aspirasiDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-success-gradient text-white p-3">
        <h5 class="modal-title fw-bold" id="aspirasiDetailModalLabel">Detail Aspirasi</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div id="aspirasiDetailLoading" class="text-center py-5 my-5">
          <div class="spinner-border text-success mb-3" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <div class="text-success fw-medium">Memuat detail aspirasi...</div>
        </div>
        <div id="aspirasiDetailContent" style="display:none;">
          <!-- Hero Section -->
          <div class="detail-hero bg-success-gradient p-3 text-white">
            <div class="row align-items-center">
              <div class="col-auto">
                <div class="avatar-xl bg-white text-success shadow-lg">
                  <span id="aspirasiDetailInitials" class="fs-2"></span>
                </div>
              </div>
              <div class="col">
                <div class="mb-1">
                  <span class="badge bg-white text-success px-3 py-2">ID: <span id="aspirasiDetailPengaduanId"></span></span>
                  <span id="aspirasiDetailStatusPengaduan" class="ms-2"></span>
                </div>
                <h4 id="aspirasiDetailNamaPelapor" class="mb-1 fw-bold"></h4>
                <div class="d-flex align-items-center text-white-50">
                  <span id="aspirasiDetailCreatedAt"></span>
                </div>
              </div>
            </div>
          </div>
          <!-- Content Section -->
          <div class="detail-content px-4 py-3">
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <div class="info-card bg-white rounded-3 p-3 h-100">
                  <h6 class="mb-2 fw-semibold text-success">Jenis Pengaduan</h6>
                  <div id="aspirasiDetailJenisPengaduan" class="fs-5 fw-medium"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-card bg-white rounded-3 p-3 h-100">
                  <h6 class="mb-2 fw-semibold text-info">Lokasi</h6>
                  <div id="aspirasiDetailLokasi" class="fs-5 fw-medium"></div>
                </div>
              </div>
            </div>
            <div class="content-card bg-white rounded-3 p-3 mb-3">
              <h6 class="mb-2 fw-semibold text-success">Detail Aspirasi</h6>
              <div id="aspirasiDetailDetailPengaduan" class="p-3 bg-light rounded-3 border-start border-success border-3 fs-6 lh-base mb-3"></div>
            </div>
            <div class="content-card bg-white rounded-3 p-3 mb-1">
              <h6 class="mb-2 fw-semibold text-warning">Dokumen Pendukung</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="dokumen-container bg-light rounded-3 overflow-hidden">
                    <div id="aspirasiDetailDokumen1" class="text-center p-2"></div>
                    <div class="dokumen-label bg-white py-2 px-3">
                      <span class="fw-medium">Dokumen 1</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="dokumen-container bg-light rounded-3 overflow-hidden">
                    <div id="aspirasiDetailDokumen2" class="text-center p-2"></div>
                    <div class="dokumen-label bg-white py-2 px-3">
                      <span class="fw-medium">Dokumen 2</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer py-3 px-4 d-flex justify-content-between align-items-center">
        <div>
          <button type="button" id="aspirasiMarkAsReadBtn" class="btn btn-success px-4 py-2 rounded-pill d-none">
            Tandai Dibaca RT
          </button>
        </div>
        <button type="button" class="btn btn-primary px-4 py-2 rounded-pill" data-bs-dismiss="modal">
          Tutup
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Remark Aspirasi -->
<div class="modal fade" id="aspirasiRemarkModal" tabindex="-1" aria-labelledby="aspirasiRemarkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aspirasiRemarkModalLabel">Pesan respon untuk pelapor (Opsional)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label for="aspirasiRemarkInput" class="form-label">Tambahkan jika diperlukan:</label>
        <textarea id="aspirasiRemarkInput" class="form-control" rows="3" placeholder="Tulis di sini (opsional)..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-success" id="aspirasiSubmitRemarkBtn">OK & Tandai Dibaca RT</button>
      </div>
    </div>
  </div>
</div>

<div id="aspirasiSuccessAlert" class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-4" style="z-index: 9999; display:none; min-width:320px; max-width:90vw;" role="alert">
  <span id="aspirasiSuccessMsg"></span>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('js/rt/laporan/keluhan.js') }}"></script>
<script>
$(document).ready(function() {
    var table = $('#aspirasiTable').DataTable({
        responsive: true,
        info: false,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
            paginate: {
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>'
            },
            emptyTable: '<div class="text-center p-4"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><p>Tidak ada aspirasi yang ditemukan</p></div>',
            zeroRecords: '<div class="text-center p-4"><i class="fas fa-search fa-3x text-muted mb-3"></i><p>Tidak ada hasil yang ditemukan</p></div>'
        },
        columnDefs: [
            { orderable: false, targets: 5 }
        ],
        order: [[0, 'asc']],
        dom: '<"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        lengthMenu: [5, 10, 25, 50],
        pageLength: 10,
        drawCallback: function(settings) {
            var api = this.api();
            var pageInfo = api.page.info();
            $('#paginationInfo').html('Menampilkan ' + (pageInfo.start + 1) + ' - ' + pageInfo.end + ' dari ' + pageInfo.recordsTotal + ' aspirasi');
            $('tbody tr').each(function(index) {
                $(this).css('animation-delay', (index * 0.05) + 's');
                $(this).addClass('animate-fade-in');
            });
        }
    });
    $('#searchInput').on('keyup', function() {
        table.search($(this).val()).draw();
    });
});

$(document).on('click', '.view-btn', function() {
    var pengaduanId = $(this).data('id');
    $('#aspirasiDetailModal').modal('show');
    $('#aspirasiDetailContent').hide();
    $('#aspirasiDetailLoading').show();
    $.ajax({
        url: '/rt/keluhan/' + pengaduanId,
        method: 'GET',
        success: function(data) {
            $('#aspirasiDetailPengaduanId').text(data.pengaduan_id);
            $('#aspirasiDetailNamaPelapor').text(data.nama_pelapor);
            var initials = data.nama_pelapor.substring(0, 2).toUpperCase();
            $('#aspirasiDetailInitials').text(initials);
            $('#aspirasiDetailJenisPengaduan').text(data.jenis_pengaduan);
            $('#aspirasiDetailStatusPengaduan').html('<span class="badge ' + (data.status_pengaduan === 'Tersampaikan' ? 'bg-white text-info' : 'bg-white text-success') + ' rounded-pill px-3 py-2">' + (data.status_pengaduan === 'Tersampaikan' ? 'Perlu Tindakan' : data.status_pengaduan) + '</span>');
            $('#aspirasiDetailDetailPengaduan').text(data.detail_pengaduan);
            $('#aspirasiDetailLokasi').text(data.lokasi);
            $('#aspirasiDetailCreatedAt').text(new Date(data.created_at).toLocaleString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }));
            // Dokumen 1
            if (data.dokumen1) {
                $('#aspirasiDetailDokumen1').html('<a href="' + data.dokumen1 + '" target="_blank" class="dokumen-preview"><img src="' + data.dokumen1 + '" class="img-fluid" style="max-height:180px;width:100%;object-fit:contain;"></a>');
            } else {
                $('#aspirasiDetailDokumen1').html('<div class="py-4 text-center text-muted">Tidak ada dokumen</div>');
            }
            // Dokumen 2
            if (data.dokumen2) {
                $('#aspirasiDetailDokumen2').html('<a href="' + data.dokumen2 + '" target="_blank" class="dokumen-preview"><img src="' + data.dokumen2 + '" class="img-fluid" style="max-height:180px;width:100%;object-fit:contain;"></a>');
            } else {
                $('#aspirasiDetailDokumen2').html('<div class="py-4 text-center text-muted">Tidak ada dokumen</div>');
            }
            // Show/hide mark as read button
            if (data.status_pengaduan === 'Tersampaikan') {
                $('#aspirasiMarkAsReadBtn').removeClass('d-none').data('id', data.pengaduan_id);
            } else {
                $('#aspirasiMarkAsReadBtn').addClass('d-none');
            }
            $('#aspirasiDetailLoading').hide();
            $('#aspirasiDetailContent').fadeIn(200);
        },
        error: function() {
            $('#aspirasiDetailLoading').html('<div class="text-danger">Gagal memuat detail aspirasi.</div>');
        }
    });
});

$('#aspirasiMarkAsReadBtn').on('click', function() {
    $('#aspirasiRemarkInput').val('');
    $('#aspirasiDetailModal').modal('hide');
    setTimeout(function() {
        $('#aspirasiRemarkModal').modal('show');
        $('#aspirasiSubmitRemarkBtn').data('id', $('#aspirasiMarkAsReadBtn').data('id'));
    }, 300);
});

$('#aspirasiSubmitRemarkBtn').on('click', function() {
    var pengaduanId = $(this).data('id');
    var remark = $('#aspirasiRemarkInput').val();
    var btn = $('#aspirasiMarkAsReadBtn');
    $('#aspirasiSubmitRemarkBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
    $.ajax({
        url: '/rt/keluhan/' + pengaduanId + '/mark-as-read',
        method: 'POST',
        data: { remark: remark },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
            $('#aspirasiRemarkModal').modal('hide');
            btn.addClass('d-none');
            $('#aspirasiDetailStatusPengaduan').html('<span class="badge bg-white text-success rounded-pill px-3 py-2">Dibaca RT</span>');
            $('#aspirasiSuccessMsg').text(res.message || 'Status berhasil diupdate!');
            $('#aspirasiSuccessAlert').fadeIn(200);
            setTimeout(function() {
                location.reload();
            }, 1500);
        },
        error: function() {
            $('#aspirasiSubmitRemarkBtn').prop('disabled', false).html('<i class="fas fa-check-double me-2"></i>OK & Tandai Dibaca RT');
            alert('Gagal mengubah status.');
        }
    });
});
</script>
@endsection
