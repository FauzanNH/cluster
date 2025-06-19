<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DataPenduduk;
use App\Models\SuratPengajuan;
use App\Models\DataRumah;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Auth;

class RtController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $blok_rt = $user->rt_blok;
        $totalWarga = \App\Models\DataPenduduk::where('blok_rt', $blok_rt)->count();
        $totalRumah = \App\Models\DataRumah::where('blok_rt', $blok_rt)->count();
        $totalSuratMenunggu = \App\Models\SuratPengajuan::join('datawarga', 'suratpengajuan.warga_id', '=', 'datawarga.warga_id')
            ->where('datawarga.blok_rt', $blok_rt)
            ->where('suratpengajuan.status_penegerjaan', 'menunggu verifikasi')
            ->count();
        $totalSuratValidasi = \App\Models\SuratPengajuan::join('datawarga', 'suratpengajuan.warga_id', '=', 'datawarga.warga_id')
            ->where('datawarga.blok_rt', $blok_rt)
            ->where('suratpengajuan.status_penegerjaan', 'sedang di validasi')
            ->count();
        $totalSuratDisetujui = \App\Models\SuratPengajuan::join('datawarga', 'suratpengajuan.warga_id', '=', 'datawarga.warga_id')
            ->where('datawarga.blok_rt', $blok_rt)
            ->where('suratpengajuan.status_penegerjaan', 'disetujui')
            ->count();
        $totalSuratDitolak = \App\Models\SuratPengajuan::join('datawarga', 'suratpengajuan.warga_id', '=', 'datawarga.warga_id')
            ->where('datawarga.blok_rt', $blok_rt)
            ->where('suratpengajuan.status_penegerjaan', 'ditolak')
            ->count();
        return view('rt.dashboard', compact(
            'totalWarga',
            'totalRumah',
            'totalSuratMenunggu',
            'totalSuratValidasi',
            'totalSuratDisetujui',
            'totalSuratDitolak'
        ));
    }
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (\Auth::user()->role != 'RT') {
                return redirect()->route('login');
            }
            return $next($request);
        });
    }
    public function datapenduduk()
    {
        $user = auth()->user();
        $blok_rt = $user->rt_blok; // pastikan field ini sesuai di tabel users
        $datawarga = DataPenduduk::where('blok_rt', $blok_rt)->get();
        $blok_rt_list = [$blok_rt]; // hanya blok RT milik RT yang login
        return view('rt.DataWarga.datapenduduk', compact('datawarga', 'blok_rt_list'));
    }
    public function datasatpam()
    {
        // Get all satpam users
        $satpams = User::where('role', 'Satpam')->get();
        
        // Check if we've reached the maximum limit
        $maxReached = count($satpams) >= 8;
        
        return view('rt.DataSatpam.index', compact('satpams', 'maxReached'));
    }
    public function datarumah()
    {
        return view('rt.DataWarga.datarumah');
    }
    public function suratpengantar()
    {
        $suratpengantar = \App\Models\SuratPengajuan::join('datawarga', 'suratpengajuan.warga_id', '=', 'datawarga.warga_id')
            ->select(
                'suratpengajuan.*',
                'datawarga.nama',
                'datawarga.nik',
                'datawarga.blok_rt'
            )
            ->orderBy('suratpengajuan.created_at', 'desc')
            ->get();
        return view('rt.DataWarga.suratpengantar', compact('suratpengantar'));
    }
    public function akunwarga()
    {
        $warga = User::where('role', 'Warga')->get();
        return view('rt.DataWarga.akunwarga', compact('warga'));
    }
    public function pengaturan()
    {
        return view('rt.Pengaturan.index');
    }
    public function keluhan()
    {
        $user = auth()->user();
        $blok_rt = $user->rt_blok;
        // Ambil data pengaduan, join ke users untuk dapat nama pelapor, filter blok_rt dan jenis_pengaduan keluhan/gangguan
        $keluhan = \App\Models\Pengaduan::select(
            'pengaduan.*',
            'users.nama as nama_pelapor'
        )
        ->leftJoin('users', 'pengaduan.users_id', '=', 'users.users_id')
        ->whereIn('pengaduan.jenis_pengaduan', ['keluhan', 'gangguan'])
        ->where('pengaduan.blok_rt', $blok_rt)
        ->orderByDesc('pengaduan.created_at')
        ->get();
        return view('rt.Laporan.keluhan', compact('keluhan'));
    }
    public function aspirasi()
    {
        $user = auth()->user();
        $blok_rt = $user->rt_blok;
        $aspirasi = \App\Models\Pengaduan::select(
            'pengaduan.*',
            'users.nama as nama_pelapor'
        )
        ->leftJoin('users', 'pengaduan.users_id', '=', 'users.users_id')
        ->where('pengaduan.jenis_pengaduan', 'aspirasi')
        ->where('pengaduan.blok_rt', $blok_rt)
        ->orderByDesc('pengaduan.created_at')
        ->get();
        return view('rt.Laporan.aspirasi', compact('aspirasi'));
    }
    public static function getBlokRTStatus()
    {
        $user = auth()->user();
        return [
            'is_empty' => empty($user->rt_blok) || $user->rt_blok === null,
            'blok_rt' => $user->rt_blok
        ];
    }

    /**
     * Set status pengguna menjadi online
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setUserOnline()
    {
        $user = Auth::user();
        $user->setOnline();
        
        return response()->json([
            'success' => true,
            'message' => 'Status set to online'
        ]);
    }

    /**
     * Set status pengguna menjadi offline
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setUserOffline()
    {
        $user = Auth::user();
        $user->setOffline();
        
        return response()->json([
            'success' => true,
            'message' => 'Status set to offline'
        ]);
    }

    /**
     * Set status pengguna menjadi away
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setUserAway()
    {
        $user = Auth::user();
        $user->is_online = false;
        $user->last_active = now();
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Status set to away'
        ]);
    }

    /**
     * Mendapatkan status pengguna
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserStatus($userId)
    {
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'is_online' => $user->isOnline(),
            'status_text' => $user->getOnlineStatusText(),
            'last_active' => $user->last_active ? $user->last_active->diffForHumans() : null,
            'last_active_time' => $user->getLastActiveTime(),
            'last_active_timestamp' => $user->last_active ? $user->last_active->toIso8601String() : null
        ]);
    }
}
