<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Aktivitas;

class AktivitasApiController extends Controller
{
    /**
     * Mendapatkan daftar aktivitas berdasarkan users_id
     *
     * @param string $users_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByUserId($users_id)
    {
        try {
            // Ambil data aktivitas dari user
            $aktivitas = Aktivitas::where('users_id', $users_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $aktivitas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data aktivitas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan daftar aktivitas berdasarkan tamu_id
     *
     * @param string $tamu_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByTamuId($tamu_id)
    {
        try {
            // Ambil data aktivitas dari tamu
            $aktivitas = Aktivitas::where('tamu_id', $tamu_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $aktivitas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data aktivitas: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan daftar aktivitas terbaru (untuk beranda)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActivityDashboard(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $users_id = $request->input('users_id');
            
            $query = Aktivitas::orderBy('created_at', 'desc');
            
            // Filter berdasarkan user jika users_id disediakan
            if ($users_id) {
                $query->where('users_id', $users_id);
            }
            
            $aktivitas = $query->limit($limit)->get();
            
            return response()->json([
                'success' => true,
                'data' => $aktivitas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data aktivitas: ' . $e->getMessage()
            ], 500);
        }
    }
} 