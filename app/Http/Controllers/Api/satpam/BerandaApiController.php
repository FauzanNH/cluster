<?php

namespace App\Http\Controllers\api\satpam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BerandaApiController extends Controller
{
    public function getSatpamInfo(Request $request)
    {
        $users_id = $request->input('users_id');
        if (!$users_id) {
            return response()->json([
                'success' => false,
                'message' => 'users_id wajib diisi.'
            ], 400);
        }

        $user = \App\Models\User::where('users_id', $users_id)->first();
        $satpam = \App\Models\Satpam::where('users_id', $users_id)->first();

        if (!$user || !$satpam) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nama' => $user->nama,
                'no_kep' => $satpam->no_kep,
                'users_id' => $user->users_id
            ]
        ]);
    }
    
    public function getRecentActivities(Request $request)
    {
        $users_id = $request->input('users_id');
        
        if (!$users_id) {
            return response()->json([
                'success' => false,
                'message' => 'users_id wajib diisi.'
            ], 400);
        }
        
        // Mengambil 2 aktivitas terbaru berdasarkan waktu
        $activities = DB::table('aktivitas')
            ->where('users_id', $users_id)
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get();
            
        // Format data untuk kebutuhan frontend
        $formattedActivities = [];
        foreach ($activities as $activity) {
            $createdAt = Carbon::parse($activity->created_at);
            
            // Cek jika aktivitas berkaitan dengan kunjungan
            $kunjungan = null;
            $tamu = null;
            
            if (!empty($activity->kunjungan_id)) {
                $kunjungan = DB::table('kunjungan')
                    ->where('kunjungan_id', $activity->kunjungan_id)
                    ->first();
                
                if ($kunjungan) {
                    $tamu = DB::table('tamu')
                        ->where('tamu_id', $kunjungan->tamu_id)
                        ->first();
                    
                    $rumah = DB::table('datarumah')
                        ->where('rumah_id', $kunjungan->rumah_id)
                        ->first();
                }
            }
            
            $formattedActivities[] = [
                'id' => $activity->id,
                'type' => $activity->jenis_aktivitas === 'check-in' ? 'check-in' : 'check-out',
                'name' => $tamu ? $tamu->nama : 'Tamu',
                'location' => $rumah ? $rumah->alamat : '-',
                'time' => $createdAt->format('H:i') . ' WIB',
                'date' => $createdAt->format('d/m/Y'),
                'description' => $activity->deskripsi
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $formattedActivities
        ]);
    }
}
