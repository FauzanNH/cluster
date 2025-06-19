<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TotalTamuHariIni extends Controller
{
    public function getTotalTamuHariIni($rumah_id = null)
    {
        $today = Carbon::today();
        
        $query = DB::table('kunjungan')
            ->whereDate('created_at', $today);
        
        if ($rumah_id) {
            $query->where('rumah_id', $rumah_id);
        }
        
        $total = $query->count();
        
        return response()->json([
            'status' => 'success',
            'total_tamu' => $total
        ]);
    }
    
    public function getTamuSedangBerkunjung()
    {
        $total = DB::table('kunjungan')
            ->where('status_kunjungan', 'Sedang Berlangsung')
            ->count();
            
        return response()->json([
            'status' => 'success',
            'tamu_berkunjung' => $total
        ]);
    }
    
    public function getTamuOngoing()
    {
        // Mendapatkan tamu dengan status "Sedang Berlangsung" tanpa filter tanggal
        $total = DB::table('kunjungan')
            ->where('status_kunjungan', 'Sedang Berlangsung')
            ->count();
            
        return response()->json([
            'status' => 'success',
            'tamu_ongoing' => $total
        ]);
    }
}
