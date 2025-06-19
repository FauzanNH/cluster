@extends('RTtemplate')

@section('title', 'Laporan Jumlah Tamu')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/rt/jumlahtamu.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Laporan Rekap Harian Tamu - RT Blok {{ $rt_blok }}</h2>
        <hr>
        <div>
            <button type="button" id="btnExportPDF" class="btn btn-danger ms-2">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </button>
            <button type="button" id="btnPrint" class="btn btn-primary ms-2" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>
    
    <!-- Filter Tanggal -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('rt.laporan.tamu.filter') }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <input type="hidden" id="rtBlok" value="{{ $rt_blok }}">
                <div class="col-md-4">
                    <label for="tanggal" class="form-label">Pilih Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ $tanggal }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Statistik Kunjungan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Kunjungan</h5>
                    <h2 class="card-text">{{ $total_kunjungan }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Menunggu Menuju Cluster</h5>
                    <h2 class="card-text">{{ $menunggu }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Sedang Berlangsung</h5>
                    <h2 class="card-text">{{ $sedang_berlangsung }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Meninggalkan Cluster</h5>
                    <h2 class="card-text">{{ $meninggalkan }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabel Data Kunjungan -->
    <div class="card" id="printArea">
        <div class="card-header">
            <h5>Data Kunjungan Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }} - RT Blok {{ $rt_blok }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tabelKunjungan">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Kunjungan</th>
                            <th>Tujuan</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Keluar</th>
                            <th>Durasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kunjungan as $index => $k)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $k->kunjungan_id }}</td>
                            <td>{{ $k->tujuan_kunjungan }}</td>
                            <td>Blok {{ $k->rumah->blok_rt }} - {{ $k->rumah->alamat_cluster }}</td>
                            <td>
                                @if($k->status_kunjungan == 'Menunggu Menuju Cluster')
                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                @elseif($k->status_kunjungan == 'Sedang Berlangsung')
                                    <span class="badge bg-success">Berlangsung</span>
                                @elseif($k->status_kunjungan == 'Meninggalkan Cluster')
                                    <span class="badge bg-secondary">Meninggalkan</span>
                                @endif
                            </td>
                            <td>{{ $k->waktu_masuk ? \Carbon\Carbon::parse($k->waktu_masuk)->format('H:i:s') : '-' }}</td>
                            <td>{{ $k->waktu_keluar ? \Carbon\Carbon::parse($k->waktu_keluar)->format('H:i:s') : '-' }}</td>
                            <td>
                                @if($k->waktu_masuk && $k->waktu_keluar)
                                    @php
                                        $masuk = \Carbon\Carbon::parse($k->waktu_masuk);
                                        $keluar = \Carbon\Carbon::parse($k->waktu_keluar);
                                        $durasi = $masuk->diff($keluar);
                                        echo $durasi->format('%H:%I:%S');
                                    @endphp
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data kunjungan pada tanggal ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
<script src="{{ asset('js/rt/laporan/jumlahtamu.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#tabelKunjungan').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Laporan Tamu RT Blok {{ $rt_blok }} - ' + $('#tanggal').val(),
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn btn-primary btn-sm',
                    title: 'Laporan Tamu RT Blok {{ $rt_blok }} - ' + $('#tanggal').val(),
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ]
        });
        
        flatpickr("#tanggal", {
            dateFormat: "Y-m-d",
            maxDate: "today"
        });
    });
</script>
@endsection
