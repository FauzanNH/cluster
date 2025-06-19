@extends('RTtemplate')

@section('title', 'Jadwal Kerja Satpam')

@section('styles')
<style>
    /* Gate Assignment Cards */
    .gate-card {
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        border: none;
    }
    .gate-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
    .gate-card .card-header {
        border-radius: 10px 10px 0 0;
        font-weight: bold;
        padding: 12px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .gate-card .card-body {
        padding: 1rem;
    }
    .gate-guard-count {
        font-size: 0.9rem;
        padding: 3px 8px;
        border-radius: 50px;
        margin-left: 10px;
        font-weight: normal;
    }
    .gate-guard-item {
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
        background-color: #f8f9fa;
        transition: all 0.2s;
        border-left: 4px solid transparent;
        display: flex;
        align-items: center;
    }
    .gate-guard-item:hover {
        background-color: #e9ecef;
        transform: translateX(3px);
    }
    .gate-guard-item:last-child {
        margin-bottom: 0;
    }
    .gate-guard-icon {
        margin-right: 10px;
        width: 32px;
        height: 32px;
        background-color: rgba(0,0,0,0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .gate-guard-name {
        font-weight: 500;
    }
    .gate-full {
        background-color: #dc3545;
        color: white;
    }
    .gate-available {
        background-color: #28a745;
        color: white;
    }
    .gate-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .gate-primary .gate-guard-item {
        border-left-color: #4e73df;
    }
    .gate-success .gate-guard-item {
        border-left-color: #1cc88a;
    }
    .gate-warning .gate-guard-item {
        border-left-color: #f6c23e;
    }
    .gate-info .gate-guard-item {
        border-left-color: #36b9cc;
    }
    
    .gate-empty-state {
        padding: 20px;
        text-align: center;
        color: #6c757d;
    }
    
    .gate-empty-icon {
        font-size: 2rem;
        margin-bottom: 10px;
        opacity: 0.5;
    }
    
    .gate-slot-indicator {
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }
    
    .gate-slot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin: 0 3px;
        background-color: #e9ecef;
    }
    
    .gate-slot.filled {
        background-color: #4e73df;
    }
    
    /* Calendar Styles */
    .shift-badge {
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: bold;
        color: white;
        display: inline-block;
    }
    .shift-pagi {
        background-color: #4e73df;
    }
    .shift-siang {
        background-color: #1cc88a;
    }
    .shift-malam {
        background-color: #36b9cc;
    }
    .shift-libur {
        background-color: #858796;
    }
    
    .filter-container {
        display: flex;
        gap: 10px;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    
    .filter-item {
        padding: 8px 15px;
        border-radius: 50px;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #e3e6f0;
    }
    
    .filter-item:hover {
        background-color: #e9ecef;
    }
    
    .filter-item.active {
        background-color: #4e73df;
        color: white;
        border-color: #4e73df;
    }
    
    .calendar-container {
        margin-top: 1rem;
    }
    
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
    }
    
    .calendar-day-header {
        text-align: center;
        font-weight: bold;
        padding: 10px;
        background-color: #4e73df;
        color: white;
        border-radius: 5px;
    }
    
    .calendar-day {
        min-height: 120px;
        border: 1px solid #e3e6f0;
        border-radius: 5px;
        padding: 5px;
        background-color: white;
        position: relative;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .calendar-day:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1;
    }
    
    .calendar-day.today {
        background-color: #f8f9fc;
        border-color: #4e73df;
        box-shadow: 0 0 0 2px #4e73df;
    }
    
    .calendar-day.other-month {
        background-color: #f8f9fa;
        opacity: 0.5;
    }
    
    .day-number {
        position: absolute;
        top: 5px;
        right: 8px;
        font-weight: bold;
        font-size: 1.1rem;
        color: #5a5c69;
    }
    
    .day-content {
        margin-top: 25px;
        font-size: 0.85rem;
    }
    
    .schedule-item {
        margin-bottom: 8px;
        padding: 5px;
        border-radius: 4px;
        background-color: #f8f9fa;
        transition: background-color 0.2s;
    }
    
    .schedule-item:hover {
        background-color: #e9ecef;
    }
    
    .actions-container {
        margin-bottom: 1.5rem;
    }
    
    /* Button styles */
    .btn-action {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .calendar-grid {
            grid-template-columns: repeat(7, 1fr);
            font-size: 0.85rem;
        }
        
        .calendar-day {
            min-height: 100px;
        }
        
        .day-number {
            font-size: 0.9rem;
        }
        
        .day-content {
            font-size: 0.75rem;
        }
        
        .shift-badge {
            font-size: 0.7rem;
            padding: 2px 5px;
        }
    }
</style>
@endsection

@section('content')
<h3 class="section-header"><i class="fas fa-calendar-alt me-2"></i>Jadwal Kerja Satpam</h3>

<div class="container-fluid">
    <!-- Gate Assignment Summary card removed as requested -->

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kalender Jadwal Kerja</h6>
            <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateJadwalModal">
                    <i class="fas fa-calendar-plus me-2"></i> Buat Jadwal Otomatis
                </button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#resetJadwalModal">
                    <i class="fas fa-calendar-minus me-2"></i> Reset Jadwal
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="actions-container">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex gap-2 mb-3">
                        <a href="{{ route('rt.jadwalkerja.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Tambah Jadwal Manual
                        </a>
                    </div>

                    <form action="{{ route('rt.jadwalkerja.index') }}" method="GET" class="d-flex gap-2 mb-3">
                        <select name="bulan" class="form-select">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $i, 1)->locale('id')->monthName }}
                                </option>
                            @endfor
                        </select>
                        <select name="tahun" class="form-select">
                            @for ($i = 2023; $i <= 2030; $i++)
                                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search me-1"></i> Tampilkan
                        </button>
                    </form>
                </div>
                
                <div class="filter-container">
                    <div class="filter-item active" data-shift="all">Semua</div>
                    <div class="filter-item" data-shift="pagi">
                        <span class="shift-badge shift-pagi">Pagi</span>
                    </div>
                    <div class="filter-item" data-shift="siang">
                        <span class="shift-badge shift-siang">Siang</span>
                    </div>
                    <div class="filter-item" data-shift="malam">
                        <span class="shift-badge shift-malam">Malam</span>
                    </div>
                </div>
                
                <div class="calendar-container">
                    <div class="calendar-grid">
                        <!-- Calendar header (days of week) -->
                        @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                            <div class="calendar-day-header">{{ $day }}</div>
                        @endforeach
                        
                        <!-- Calendar days -->
                        @php
                            $date = \Carbon\Carbon::create($tahun, $bulan, 1);
                            $startOfMonth = $date->copy()->startOfMonth();
                            $endOfMonth = $date->copy()->endOfMonth();
                            
                            // Add empty cells for days before the first day of month
                            $firstDayOfWeek = $startOfMonth->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
                        @endphp
                        
                        @for ($i = 0; $i < $firstDayOfWeek; $i++)
                            <div class="calendar-day other-month"></div>
                        @endfor
                        
                        @for ($day = 1; $day <= $endOfMonth->day; $day++)
                            @php
                                $currentDate = \Carbon\Carbon::create($tahun, $bulan, $day);
                                $isToday = $currentDate->isToday();
                                
                                // Get schedules for this day
                                $daySchedules = $jadwal->filter(function($item) use ($currentDate) {
                                    return $item->tanggal->format('Y-m-d') === $currentDate->format('Y-m-d');
                                });
                            @endphp
                            
                            <div class="calendar-day {{ $isToday ? 'today' : '' }}">
                                <div class="day-number">{{ $day }}</div>
                                <div class="day-content">
                                    @if($daySchedules->count() > 0)
                                        @foreach($daySchedules as $schedule)
                                            <div class="mb-1 schedule-item" data-shift="{{ $schedule->shift }}">
                                                <span class="shift-badge shift-{{ $schedule->shift }}">
                                                    {{ \App\Models\JadwalKerjaSatpam::getShiftLabel($schedule->shift) }}
                                                </span>
                                                <small>{{ $schedule->satpam->nama }}</small>
                                                <div class="mt-1">
                                                    <a href="{{ route('rt.jadwalkerja.edit', $schedule->id) }}" class="btn btn-sm btn-outline-primary btn-action">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-action delete-btn" 
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                            data-id="{{ $schedule->id }}" 
                                                            data-name="{{ $schedule->satpam->nama }}" 
                                                            data-date="{{ $schedule->tanggal->format('d/m/Y') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <small class="text-muted">Tidak ada jadwal</small>
                                    @endif
                                </div>
                            </div>
                        @endfor
                        
                        @php
                            // Add empty cells to complete the grid
                            $lastDayOfWeek = $endOfMonth->dayOfWeek;
                            $remainingDays = 6 - $lastDayOfWeek;
                        @endphp
                        
                        @for ($i = 0; $i < $remainingDays; $i++)
                            <div class="calendar-day other-month"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Jadwal Modal -->
<div class="modal fade" id="generateJadwalModal" tabindex="-1" aria-labelledby="generateJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="generateJadwalModalLabel">Buat Jadwal Otomatis</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('rt.jadwalkerja.generate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Jadwal akan digenerate untuk semua satpam dengan maksimal 2 satpam per gerbang.
                    </div>
                    
                    <div class="alert alert-primary">
                        <i class="fas fa-clock me-2"></i> Jadwal akan dibuat dengan pola rotasi shift: <strong>Pagi → Siang → Malam</strong>. Tidak ada libur otomatis, libur hanya dapat ditambahkan secara manual.
                    </div>
                    
                    <div class="mb-3">
                        <label for="gen-bulan" class="form-label">Bulan</label>
                        <select name="bulan" id="gen-bulan" class="form-control" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create(null, $i, 1)->locale('id')->monthName }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="gen-tahun" class="form-label">Tahun</label>
                        <select name="tahun" id="gen-tahun" class="form-control" required>
                            @for($i = 2023; $i <= 2030; $i++)
                                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> Perhatian: Proses ini akan menghapus jadwal yang sudah ada untuk bulan yang dipilih.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Generate Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset Jadwal Modal -->
<div class="modal fade" id="resetJadwalModal" tabindex="-1" aria-labelledby="resetJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="resetJadwalModalLabel">Reset Jadwal Kerja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('rt.jadwalkerja.reset') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i> Perhatian: Proses ini akan menghapus semua jadwal untuk bulan yang dipilih.
                    </div>
                    
                    <div class="mb-3">
                        <label for="reset-bulan" class="form-label">Bulan</label>
                        <select name="bulan" id="reset-bulan" class="form-control" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create(null, $i, 1)->locale('id')->monthName }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reset-tahun" class="form-label">Tahun</label>
                        <select name="tahun" id="reset-tahun" class="form-control" required>
                            @for($i = 2023; $i <= 2030; $i++)
                                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="confirm_reset" id="confirm_reset" required>
                        <label class="form-check-label" for="confirm_reset">
                            Saya yakin ingin menghapus semua jadwal untuk periode ini
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Reset Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Hapus Jadwal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus jadwal untuk <span id="deleteName" class="fw-bold"></span> pada tanggal <span id="deleteDate" class="fw-bold"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle delete button click
        $('.delete-btn').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const date = $(this).data('date');
            
            $('#deleteName').text(name);
            $('#deleteDate').text(date);
            $('#deleteForm').attr('action', `{{ url('rt/jadwalkerja') }}/${id}/delete`);
        });
        
        // Handle filter clicks
        $('.filter-item').on('click', function() {
            const shift = $(this).data('shift');
            
            // Toggle active class
            $('.filter-item').removeClass('active');
            $(this).addClass('active');
            
            // Show/hide schedule items based on filter
            if (shift === 'all') {
                $('.schedule-item').show();
            } else {
                $('.schedule-item').hide();
                $(`.schedule-item[data-shift="${shift}"]`).show();
            }
        });
        
        // Hover effects for calendar days
        $('.calendar-day').hover(
            function() {
                $(this).css('z-index', '10');
            },
            function() {
                $(this).css('z-index', '1');
            }
        );
    });
</script>
@endsection 