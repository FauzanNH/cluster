<?php

namespace App\Http\Controllers\Api\tamu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileApiController extends Controller
{
    /**
     * Mendapatkan data profil tamu berdasarkan tamu_id
     */
    public function getProfile(Request $request)
    {
        // Validasi request
        $request->validate([
            'tamu_id' => 'required|string|max:7|exists:tamu,tamu_id',
        ]);

        $tamu_id = $request->tamu_id;

        try {
            // Ambil data dari tabel tamu dan detail_tamu
            $profile = DB::table('tamu')
                ->join('detail_tamu', 'tamu.tamu_id', '=', 'detail_tamu.tamu_id')
                ->select(
                    'tamu.tamu_id',
                    'tamu.no_hp',
                    'tamu.email',
                    'detail_tamu.nama',
                    'detail_tamu.nik',
                    'detail_tamu.kewarganegaraan',
                    'detail_tamu.alamat',
                    'detail_tamu.rt',
                    'detail_tamu.rw',
                    'detail_tamu.kel_desa',
                    'detail_tamu.kecamatan',
                    'detail_tamu.kabupaten'
                )
                ->where('tamu.tamu_id', $tamu_id)
                ->first();

            if (!$profile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tamu tidak ditemukan'
                ], 404);
            }

            // Membuat format alamat lengkap
            $alamat_lengkap = $profile->alamat . ", RT." . $profile->rt . ", RW." . $profile->rw . ", " . $profile->kel_desa . ", " . $profile->kecamatan . ", " . $profile->kabupaten;

            // Menambahkan alamat lengkap ke data profil
            $profileArray = (array) $profile;
            $profileArray['alamat_ktp'] = $alamat_lengkap;

            return response()->json([
                'status' => 'success',
                'data' => $profileArray
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
