<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kunjungan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class JumlahTamuController extends Controller
{
    public function index()
    {
        // Ambil data kunjungan hari ini sebagai default
        $tanggal = Carbon::now()->format('Y-m-d');
        
        // Ambil blok RT dari user yang login
        $rt_blok = Auth::user()->rt_blok;
        
        // Filter kunjungan berdasarkan blok RT
        $kunjungan = Kunjungan::whereDate('created_at', $tanggal)
            ->with(['tamu', 'rumah'])
            ->whereHas('rumah', function($query) use ($rt_blok) {
                $query->where('blok_rt', $rt_blok);
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Hitung statistik
        $total_kunjungan = $kunjungan->count();
        $sedang_berlangsung = $kunjungan->where('status_kunjungan', 'Sedang Berlangsung')->count();
        $menunggu = $kunjungan->where('status_kunjungan', 'Menunggu Menuju Cluster')->count();
        $meninggalkan = $kunjungan->where('status_kunjungan', 'Meninggalkan Cluster')->count();
        
        return view('rt.Laporan.jumlahtamu', compact(
            'kunjungan', 
            'tanggal', 
            'total_kunjungan', 
            'sedang_berlangsung',
            'menunggu',
            'meninggalkan',
            'rt_blok'
        ));
    }
    
    public function filterByDate(Request $request)
    {
        $tanggal = $request->tanggal;
        
        // Ambil blok RT dari user yang login
        $rt_blok = Auth::user()->rt_blok;
        
        // Filter kunjungan berdasarkan blok RT
        $kunjungan = Kunjungan::whereDate('created_at', $tanggal)
            ->with(['tamu', 'rumah'])
            ->whereHas('rumah', function($query) use ($rt_blok) {
                $query->where('blok_rt', $rt_blok);
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Hitung statistik
        $total_kunjungan = $kunjungan->count();
        $sedang_berlangsung = $kunjungan->where('status_kunjungan', 'Sedang Berlangsung')->count();
        $menunggu = $kunjungan->where('status_kunjungan', 'Menunggu Menuju Cluster')->count();
        $meninggalkan = $kunjungan->where('status_kunjungan', 'Meninggalkan Cluster')->count();
        
        return view('rt.Laporan.jumlahtamu', compact(
            'kunjungan', 
            'tanggal', 
            'total_kunjungan', 
            'sedang_berlangsung',
            'menunggu',
            'meninggalkan',
            'rt_blok'
        ));
    }
}
