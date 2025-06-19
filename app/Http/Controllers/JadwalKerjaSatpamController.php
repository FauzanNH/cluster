<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKerjaSatpam;
use App\Models\User;
use App\Models\Satpam;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JadwalKerjaSatpamController extends Controller
{
    /**
     * Display a listing of the schedules.
     */
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        
        $jadwal = JadwalKerjaSatpam::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->get();
            
        $satpam = User::where('role', 'Satpam')->get();
        
        return view('rt.jadwalkerja.index', compact('jadwal', 'satpam', 'bulan', 'tahun'));
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create()
    {
        $satpam = User::where('role', 'Satpam')->get();
        return view('rt.jadwalkerja.create', compact('satpam'));
    }

    /**
     * Store a newly created schedule in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,users_id',
            'tanggal' => 'required|date',
            'shift' => 'required|in:pagi,siang,malam,libur',
            'lokasi' => 'required|string|max:255',
            'lokasi_detail' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if the satpam has data in datasatpam table
        $satpamData = Satpam::where('users_id', $request->users_id)->first();
        if (!$satpamData) {
            return redirect()->back()
                ->with('error', 'Data satpam tidak lengkap. Silakan lengkapi data satpam terlebih dahulu.')
                ->withInput();
        }

        // Check if schedule already exists for this user on this date
        $existingJadwal = JadwalKerjaSatpam::where('users_id', $request->users_id)
            ->where('tanggal', $request->tanggal)
            ->first();
            
        if ($existingJadwal) {
            return redirect()->back()
                ->with('error', 'Jadwal untuk satpam ini pada tanggal tersebut sudah ada!')
                ->withInput();
        }
        
        // Get shift times based on shift type
        $shiftTimes = JadwalKerjaSatpam::getShiftTime($request->shift);
        
        // Get satpam's assigned location if the default location is selected
        $lokasi = $request->lokasi;
        if ($lokasi === 'Gerbang Utama') {
            if ($satpamData->seksi_unit_gerbang) {
                $lokasi = $satpamData->seksi_unit_gerbang;
            }
        }
        
        $jadwal = new JadwalKerjaSatpam();
        $jadwal->users_id = $request->users_id;
        $jadwal->tanggal = $request->tanggal;
        $jadwal->shift = $request->shift;
        $jadwal->jam_mulai = $shiftTimes['jam_mulai'];
        $jadwal->jam_selesai = $shiftTimes['jam_selesai'];
        $jadwal->lokasi = $lokasi;
        $jadwal->lokasi_detail = $request->lokasi_detail;
        $jadwal->catatan = $request->catatan;
        $jadwal->is_active = true;
        $jadwal->save();

        return redirect()->route('rt.jadwalkerja.index')
            ->with('success', 'Jadwal kerja satpam berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified schedule.
     */
    public function edit($id)
    {
        $jadwal = JadwalKerjaSatpam::findOrFail($id);
        $satpam = User::where('role', 'Satpam')->get();
        
        return view('rt.jadwalkerja.edit', compact('jadwal', 'satpam'));
    }

    /**
     * Update the specified schedule in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,users_id',
            'tanggal' => 'required|date',
            'shift' => 'required|in:pagi,siang,malam,libur',
            'lokasi' => 'required|string|max:255',
            'lokasi_detail' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if the satpam has data in datasatpam table
        $satpamData = Satpam::where('users_id', $request->users_id)->first();
        if (!$satpamData) {
            return redirect()->back()
                ->with('error', 'Data satpam tidak lengkap. Silakan lengkapi data satpam terlebih dahulu.')
                ->withInput();
        }

        $jadwal = JadwalKerjaSatpam::findOrFail($id);
        
        // Check if schedule already exists for this user on this date (excluding current record)
        $existingJadwal = JadwalKerjaSatpam::where('users_id', $request->users_id)
            ->where('tanggal', $request->tanggal)
            ->where('id', '!=', $id)
            ->first();
            
        if ($existingJadwal) {
            return redirect()->back()
                ->with('error', 'Jadwal untuk satpam ini pada tanggal tersebut sudah ada!')
                ->withInput();
        }
        
        // Get shift times based on shift type
        $shiftTimes = JadwalKerjaSatpam::getShiftTime($request->shift);
        
        // Get satpam's assigned location if the default location is selected
        $lokasi = $request->lokasi;
        if ($lokasi === 'Gerbang Utama') {
            if ($satpamData->seksi_unit_gerbang) {
                $lokasi = $satpamData->seksi_unit_gerbang;
            }
        }
        
        $jadwal->users_id = $request->users_id;
        $jadwal->tanggal = $request->tanggal;
        $jadwal->shift = $request->shift;
        $jadwal->jam_mulai = $shiftTimes['jam_mulai'];
        $jadwal->jam_selesai = $shiftTimes['jam_selesai'];
        $jadwal->lokasi = $lokasi;
        $jadwal->lokasi_detail = $request->lokasi_detail;
        $jadwal->catatan = $request->catatan;
        $jadwal->is_active = $request->has('is_active');
        $jadwal->save();

        return redirect()->route('rt.jadwalkerja.index')
            ->with('success', 'Jadwal kerja satpam berhasil diperbarui!');
    }

    /**
     * Remove the specified schedule from storage.
     */
    public function destroy($id)
    {
        $jadwal = JadwalKerjaSatpam::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('rt.jadwalkerja.index')
            ->with('success', 'Jadwal kerja satpam berhasil dihapus!');
    }
    
    /**
     * Generate schedules automatically for a given month.
     */
    public function generateSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2023|max:2100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        // Get all security guards who have data in the datasatpam table
        $satpam = User::where('role', 'Satpam')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('datasatpam')
                      ->whereColumn('datasatpam.users_id', 'users.users_id');
            })
            ->get();
        
        if ($satpam->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada satpam dengan data lengkap yang terdaftar untuk dijadwalkan!');
        }
        
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            // Delete existing schedules for the month
            JadwalKerjaSatpam::whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->delete();
                
            // Generate dates for the month
            $startDate = Carbon::createFromDate($tahun, $bulan, 1);
            $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
            
            $dates = [];
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dates[] = $date->copy();
            }
            
            // Define shift patterns (3 days each) - removed 'libur' shifts
            $shifts = ['pagi', 'pagi', 'pagi', 'siang', 'siang', 'siang', 'malam', 'malam', 'malam'];
            
            // Group satpam by their assigned gate
            $gateAssignments = [
                'Gerbang Utama' => [],
                'Gerbang Belakang' => [],
                'Gerbang Timur' => [],
                'Gerbang Barat' => []
            ];
            
            // Assign satpam to gates based on their preferences, with a maximum of 2 per gate
            foreach ($satpam as $guard) {
                $satpamData = Satpam::where('users_id', $guard->users_id)->first();
                
                if (!$satpamData || !$satpamData->seksi_unit_gerbang) {
                    continue;
                }
                
                $gate = $satpamData->seksi_unit_gerbang;
                
                // Check if this gate already has 2 guards assigned
                if (isset($gateAssignments[$gate]) && count($gateAssignments[$gate]) < 2) {
                    $gateAssignments[$gate][] = $guard;
                } else {
                    // If gate is full or not valid, assign to a gate with fewer than 2 guards
                    $assignedGate = $this->findAvailableGate($gateAssignments);
                    if ($assignedGate) {
                        $gateAssignments[$assignedGate][] = $guard;
                        
                        // Update the guard's assigned gate in the database
                        $satpamData->seksi_unit_gerbang = $assignedGate;
                        $satpamData->save();
                    } else {
                        // All gates have 2 guards, this shouldn't happen with max 8 guards total
                        // But just in case, assign to their preferred gate anyway
                        $gateAssignments[$gate][] = $guard;
                    }
                }
            }
            
            // Assign shifts to each security guard
            $shiftOffset = 0;
            
            // Generate schedules for each gate
            foreach ($gateAssignments as $gate => $guards) {
                foreach ($guards as $index => $guard) {
                    // Get satpam's data
                    $satpamData = Satpam::where('users_id', $guard->users_id)->first();
                    
                    // Skip if no data found in datasatpam table
                    if (!$satpamData) {
                        continue;
                    }
                    
                    // Each guard starts at a different point in the shift pattern
                    $guardShiftOffset = ($shiftOffset + ($index * 3)) % count($shifts);
                    
                    // Set location details
                    $lokasi = $gate;
                    $lokasiDetail = $gate . ' Cluster Bukit Asri';
                    
                    foreach ($dates as $dateIndex => $date) {
                        $shiftIndex = ($dateIndex + $guardShiftOffset) % count($shifts);
                        $shift = $shifts[$shiftIndex];
                        
                        // Get shift times
                        $shiftTimes = JadwalKerjaSatpam::getShiftTime($shift);
                        
                        // Catatan berdasarkan shift
                        $catatan = null;
                        if ($shift === 'malam') {
                            $catatan = 'Lakukan patroli setiap 2 jam sekali';
                        }
                        
                        // Create schedule
                        JadwalKerjaSatpam::create([
                            'users_id' => $guard->users_id,
                            'tanggal' => $date->format('Y-m-d'),
                            'shift' => $shift,
                            'jam_mulai' => $shiftTimes['jam_mulai'],
                            'jam_selesai' => $shiftTimes['jam_selesai'],
                            'lokasi' => $lokasi,
                            'lokasi_detail' => $lokasiDetail,
                            'catatan' => $catatan,
                            'is_active' => true
                        ]);
                    }
                    
                    // Increment shift offset for next gate
                    $shiftOffset += 3;
                }
            }
            
            // Commit the transaction
            DB::commit();
            
            return redirect()->route('rt.jadwalkerja.index', ['bulan' => $bulan, 'tahun' => $tahun])
                ->with('success', 'Jadwal kerja satpam berhasil digenerate untuk bulan ' . Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->monthName . ' ' . $tahun);
                
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat generate jadwal: ' . $e->getMessage());
        }
    }
    
    /**
     * Find a gate with fewer than 2 guards assigned
     */
    private function findAvailableGate($gateAssignments)
    {
        foreach ($gateAssignments as $gate => $guards) {
            if (count($guards) < 2) {
                return $gate;
            }
        }
        return null;
    }
    
    /**
     * Reset/delete all schedules for a specific month and year.
     */
    public function resetSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2023|max:2100',
            'confirm_reset' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            // Delete all schedules for the specified month and year
            $deletedCount = JadwalKerjaSatpam::whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->delete();
                
            // Commit the transaction
            DB::commit();
            
            $bulanLabel = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->monthName;
            
            return redirect()->route('rt.jadwalkerja.index', ['bulan' => $bulan, 'tahun' => $tahun])
                ->with('success', "Berhasil menghapus $deletedCount jadwal kerja satpam untuk bulan $bulanLabel $tahun");
                
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus jadwal: ' . $e->getMessage());
        }
    }
}
