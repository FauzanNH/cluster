<?php

namespace App\Http\Controllers\Api\satpam\beranda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class LaporanHarianApiController extends Controller
{
    /**
     * Mendapatkan daftar kunjungan tamu hari ini
     */
    public function getKunjunganHariIni(Request $request)
    {
        try {
            $today = Carbon::today();
            
            $kunjungan = DB::table('kunjungan')
                ->select(
                    'kunjungan.id',
                    'kunjungan.kunjungan_id',
                    'detail_tamu.nama as nama_tamu',
                    'kunjungan.tujuan_kunjungan as tujuan',
                    'kunjungan.status_kunjungan as status',
                    'datarumah.blok_rt as blok',
                    'kunjungan.waktu_masuk',
                    'kunjungan.waktu_keluar',
                    'kunjungan.created_at as tanggal'
                )
                ->leftJoin('tamu', 'kunjungan.tamu_id', '=', 'tamu.tamu_id')
                ->leftJoin('detail_tamu', 'tamu.tamu_id', '=', 'detail_tamu.tamu_id')
                ->leftJoin('datarumah', 'kunjungan.rumah_id', '=', 'datarumah.rumah_id')
                ->whereDate('kunjungan.created_at', $today)
                ->orderBy('kunjungan.created_at', 'desc')
                ->get();
            
            $formattedData = [];
            foreach ($kunjungan as $item) {
                $tanggal = Carbon::parse($item->tanggal);
                $waktuMasuk = $item->waktu_masuk ? Carbon::parse($item->waktu_masuk)->format('H:i') : null;
                $waktuKeluar = $item->waktu_keluar ? Carbon::parse($item->waktu_keluar)->format('H:i') : null;
                
                $formattedData[] = [
                    'id_kunjungan' => $item->kunjungan_id,
                    'nama_tamu' => $item->nama_tamu ?? 'Tamu',
                    'tanggal' => $tanggal->format('d/m/Y'),
                    'tujuan' => $item->tujuan,
                    'blok' => $item->blok ?? '-',
                    'status' => $item->status,
                    'waktu_masuk' => $waktuMasuk,
                    'waktu_keluar' => $waktuKeluar
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'total' => count($formattedData)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan daftar kunjungan tamu berdasarkan tanggal tertentu
     */
    public function getKunjunganByTanggal(Request $request)
    {
        try {
            $tanggal = $request->input('tanggal');
            
            if (!$tanggal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal wajib diisi'
                ], 400);
            }
            
            $date = Carbon::parse($tanggal);
            
            $kunjungan = DB::table('kunjungan')
                ->select(
                    'kunjungan.id',
                    'kunjungan.kunjungan_id',
                    'detail_tamu.nama as nama_tamu',
                    'kunjungan.tujuan_kunjungan as tujuan',
                    'kunjungan.status_kunjungan as status',
                    'datarumah.blok_rt as blok',
                    'kunjungan.waktu_masuk',
                    'kunjungan.waktu_keluar',
                    'kunjungan.created_at as tanggal'
                )
                ->leftJoin('tamu', 'kunjungan.tamu_id', '=', 'tamu.tamu_id')
                ->leftJoin('detail_tamu', 'tamu.tamu_id', '=', 'detail_tamu.tamu_id')
                ->leftJoin('datarumah', 'kunjungan.rumah_id', '=', 'datarumah.rumah_id')
                ->whereDate('kunjungan.created_at', $date)
                ->orderBy('kunjungan.created_at', 'desc')
                ->get();
            
            $formattedData = [];
            foreach ($kunjungan as $item) {
                $tanggal = Carbon::parse($item->tanggal);
                $waktuMasuk = $item->waktu_masuk ? Carbon::parse($item->waktu_masuk)->format('H:i') : null;
                $waktuKeluar = $item->waktu_keluar ? Carbon::parse($item->waktu_keluar)->format('H:i') : null;
                
                $formattedData[] = [
                    'id_kunjungan' => $item->kunjungan_id,
                    'nama_tamu' => $item->nama_tamu ?? 'Tamu',
                    'tanggal' => $tanggal->format('d/m/Y'),
                    'tujuan' => $item->tujuan,
                    'blok' => $item->blok ?? '-',
                    'status' => $item->status,
                    'waktu_masuk' => $waktuMasuk,
                    'waktu_keluar' => $waktuKeluar
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'total' => count($formattedData)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan daftar kunjungan tamu bulan ini
     */
    public function getKunjunganBulanIni()
    {
        try {
            $today = Carbon::today();
            $startOfMonth = Carbon::today()->startOfMonth();
            
            $kunjungan = DB::table('kunjungan')
                ->select(
                    'kunjungan.id',
                    'kunjungan.kunjungan_id',
                    'detail_tamu.nama as nama_tamu',
                    'kunjungan.tujuan_kunjungan as tujuan',
                    'kunjungan.status_kunjungan as status',
                    'datarumah.blok_rt as blok',
                    'kunjungan.waktu_masuk',
                    'kunjungan.waktu_keluar',
                    'kunjungan.created_at as tanggal'
                )
                ->leftJoin('tamu', 'kunjungan.tamu_id', '=', 'tamu.tamu_id')
                ->leftJoin('detail_tamu', 'tamu.tamu_id', '=', 'detail_tamu.tamu_id')
                ->leftJoin('datarumah', 'kunjungan.rumah_id', '=', 'datarumah.rumah_id')
                ->whereBetween('kunjungan.created_at', [$startOfMonth, $today->endOfDay()])
                ->orderBy('kunjungan.created_at', 'desc')
                ->get();
            
            $formattedData = [];
            foreach ($kunjungan as $item) {
                $tanggal = Carbon::parse($item->tanggal);
                $waktuMasuk = $item->waktu_masuk ? Carbon::parse($item->waktu_masuk)->format('H:i') : null;
                $waktuKeluar = $item->waktu_keluar ? Carbon::parse($item->waktu_keluar)->format('H:i') : null;
                
                $formattedData[] = [
                    'id_kunjungan' => $item->kunjungan_id,
                    'nama_tamu' => $item->nama_tamu ?? 'Tamu',
                    'tanggal' => $tanggal->format('d/m/Y'),
                    'tujuan' => $item->tujuan,
                    'blok' => $item->blok ?? '-',
                    'status' => $item->status,
                    'waktu_masuk' => $waktuMasuk,
                    'waktu_keluar' => $waktuKeluar
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'total' => count($formattedData)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan statistik jumlah kunjungan (hari ini dan bulan ini)
     */
    public function getStatistikKunjungan()
    {
        try {
            $today = Carbon::today();
            $startOfMonth = Carbon::today()->startOfMonth();
            
            $totalHariIni = DB::table('kunjungan')
                ->whereDate('created_at', $today)
                ->count();
                
            $totalBulanIni = DB::table('kunjungan')
                ->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])
                ->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_hari_ini' => $totalHariIni,
                    'total_bulan_ini' => $totalBulanIni
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
