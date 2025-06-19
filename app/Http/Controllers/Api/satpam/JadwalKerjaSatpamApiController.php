<?php

namespace App\Http\Controllers\Api\satpam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalKerjaSatpam;
use App\Models\User;
use App\Models\Satpam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class JadwalKerjaSatpamApiController extends Controller
{
    /**
     * Get jadwal kerja for a specific satpam
     */
    public function getJadwalBySatpam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,users_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $users_id = $request->users_id;
        
        // Check if the user has data in datasatpam table
        $satpamData = Satpam::where('users_id', $users_id)->first();
        if (!$satpamData) {
            return response()->json([
                'success' => false,
                'message' => 'Data satpam tidak ditemukan. Silakan lengkapi data satpam terlebih dahulu.',
            ], 404);
        }
        
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        
        // Get satpam data to get their assigned location
        $defaultLokasi = $satpamData->seksi_unit_gerbang ?: 'Gerbang Utama';
        
        $jadwal = JadwalKerjaSatpam::where('users_id', $users_id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->get();
            
        // Transform data for the mobile app
        $transformedJadwal = $jadwal->map(function ($item) use ($defaultLokasi) {
            // Use the satpam's seksi_unit_gerbang as the location if not explicitly set
            $lokasi = $item->lokasi === 'Pos Utama' || $item->lokasi === 'Gerbang Utama' ? $defaultLokasi : $item->lokasi;
            
            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal->format('Y-m-d'),
                'hari' => Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd'),
                'shift' => $item->shift,
                'shift_label' => JadwalKerjaSatpam::getShiftLabel($item->shift),
                'jam_mulai' => $item->jam_mulai ? Carbon::parse($item->jam_mulai)->format('H:i') : null,
                'jam_selesai' => $item->jam_selesai ? Carbon::parse($item->jam_selesai)->format('H:i') : null,
                'lokasi' => $lokasi,
                'lokasi_detail' => $item->lokasi_detail,
                'catatan' => $item->catatan,
                'is_active' => $item->is_active,
                'is_today' => Carbon::parse($item->tanggal)->isToday(),
            ];
        });
        
        // Get calendar data for the month
        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        $calendarData = [];
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $daySchedule = $jadwal->first(function ($item) use ($dateStr) {
                return $item->tanggal->format('Y-m-d') === $dateStr;
            });
            
            $calendarData[] = [
                'date' => $dateStr,
                'day' => $date->format('d'),
                'weekday' => $date->locale('id')->isoFormat('dd'),
                'shift' => $daySchedule ? $daySchedule->shift : null,
                'is_today' => $date->isToday(),
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'jadwal' => $transformedJadwal,
                'calendar' => $calendarData,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'bulan_label' => Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY'),
            ]
        ]);
    }
    
    /**
     * Get jadwal kerja for a specific month
     */
    public function getJadwalByBulan(Request $request, $bulan, $tahun)
    {
        $validator = Validator::make([
            'users_id' => $request->input('users_id'),
            'bulan' => $bulan,
            'tahun' => $tahun
        ], [
            'users_id' => 'required|exists:users,users_id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2023|max:2100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $users_id = $request->input('users_id');
        
        // Check if the user has data in datasatpam table
        $satpamData = Satpam::where('users_id', $users_id)->first();
        if (!$satpamData) {
            return response()->json([
                'success' => false,
                'message' => 'Data satpam tidak ditemukan. Silakan lengkapi data satpam terlebih dahulu.',
            ], 404);
        }
        
        // Get satpam data to get their assigned location
        $defaultLokasi = $satpamData->seksi_unit_gerbang ?: 'Gerbang Utama';
        
        $jadwal = JadwalKerjaSatpam::where('users_id', $users_id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->get();
            
        // Transform data for the mobile app
        $transformedJadwal = $jadwal->map(function ($item) use ($defaultLokasi) {
            // Use the satpam's seksi_unit_gerbang as the location if not explicitly set
            $lokasi = $item->lokasi === 'Pos Utama' || $item->lokasi === 'Gerbang Utama' ? $defaultLokasi : $item->lokasi;
            
            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal->format('Y-m-d'),
                'hari' => Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd'),
                'shift' => $item->shift,
                'shift_label' => JadwalKerjaSatpam::getShiftLabel($item->shift),
                'jam_mulai' => $item->jam_mulai ? Carbon::parse($item->jam_mulai)->format('H:i') : null,
                'jam_selesai' => $item->jam_selesai ? Carbon::parse($item->jam_selesai)->format('H:i') : null,
                'lokasi' => $lokasi,
                'lokasi_detail' => $item->lokasi_detail,
                'catatan' => $item->catatan,
                'is_active' => $item->is_active,
                'is_today' => Carbon::parse($item->tanggal)->isToday(),
            ];
        });
        
        // Get calendar data for the month
        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        $calendarData = [];
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $daySchedule = $jadwal->first(function ($item) use ($dateStr) {
                return $item->tanggal->format('Y-m-d') === $dateStr;
            });
            
            $calendarData[] = [
                'date' => $dateStr,
                'day' => $date->format('d'),
                'weekday' => $date->locale('id')->isoFormat('dd'),
                'shift' => $daySchedule ? $daySchedule->shift : null,
                'is_today' => $date->isToday(),
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'jadwal' => $transformedJadwal,
                'calendar' => $calendarData,
                'bulan' => (int)$bulan,
                'tahun' => (int)$tahun,
                'bulan_label' => Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY'),
            ]
        ]);
    }
    
    /**
     * Get jadwal kerja for a specific date
     */
    public function getJadwalByTanggal(Request $request, $tanggal)
    {
        $validator = Validator::make([
            'users_id' => $request->input('users_id'),
            'tanggal' => $tanggal
        ], [
            'users_id' => 'required|exists:users,users_id',
            'tanggal' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $users_id = $request->input('users_id');
        
        // Check if the user has data in datasatpam table
        $satpamData = Satpam::where('users_id', $users_id)->first();
        if (!$satpamData) {
            return response()->json([
                'success' => false,
                'message' => 'Data satpam tidak ditemukan. Silakan lengkapi data satpam terlebih dahulu.',
            ], 404);
        }
        
        // Get satpam data to get their assigned location
        $defaultLokasi = $satpamData->seksi_unit_gerbang ?: 'Gerbang Utama';
        
        $jadwal = JadwalKerjaSatpam::where('users_id', $users_id)
            ->where('tanggal', $tanggal)
            ->first();
            
        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan',
            ], 404);
        }
        
        // Use the satpam's seksi_unit_gerbang as the location if not explicitly set
        $lokasi = $jadwal->lokasi === 'Pos Utama' || $jadwal->lokasi === 'Gerbang Utama' ? $defaultLokasi : $jadwal->lokasi;
        
        // Get team members for this location and shift
        $teamMembers = JadwalKerjaSatpam::where('tanggal', $tanggal)
            ->where('lokasi', $jadwal->lokasi)
            ->where('shift', $jadwal->shift)
            ->where('users_id', '!=', $users_id)
            ->with('satpam:users_id,nama')
            ->get();
            
        $teammates = $teamMembers->map(function ($item) {
            return [
                'users_id' => $item->users_id,
                'nama' => $item->satpam->nama,
                'posisi' => 'Petugas', // Default position, can be updated later
            ];
        });
        
        // Transform data for the mobile app
        $transformedJadwal = [
            'id' => $jadwal->id,
            'tanggal' => $jadwal->tanggal->format('Y-m-d'),
            'hari' => Carbon::parse($jadwal->tanggal)->locale('id')->isoFormat('dddd'),
            'tanggal_lengkap' => Carbon::parse($jadwal->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY'),
            'shift' => $jadwal->shift,
            'shift_label' => JadwalKerjaSatpam::getShiftLabel($jadwal->shift),
            'jam_mulai' => $jadwal->jam_mulai ? Carbon::parse($jadwal->jam_mulai)->format('H:i') : null,
            'jam_selesai' => $jadwal->jam_selesai ? Carbon::parse($jadwal->jam_selesai)->format('H:i') : null,
            'lokasi' => $lokasi,
            'lokasi_detail' => $jadwal->lokasi_detail,
            'catatan' => $jadwal->catatan,
            'is_active' => $jadwal->is_active,
            'is_today' => Carbon::parse($jadwal->tanggal)->isToday(),
            'teammates' => $teammates,
        ];
        
        return response()->json([
            'success' => true,
            'data' => $transformedJadwal
        ]);
    }
}
