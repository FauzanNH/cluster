<?php

namespace App\Http\Controllers\Api\satpam\profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Datasatpam;
use Carbon\Carbon;

class ProfileApiController extends Controller
{
    public function getProfile(Request $request)
    {
        try {
            $usersId = $request->input('users_id');

            // Validasi parameter
            if (!$usersId) {
                return response()->json([
                    'status' => false,
                    'message' => 'ID pengguna diperlukan',
                    'data' => null
                ], 400);
            }

            // Ambil data user dan datasatpam
            $userProfile = DB::table('users')
                ->join('datasatpam', 'users.users_id', '=', 'datasatpam.users_id')
                ->where('users.users_id', $usersId)
                ->select(
                    'users.nama',
                    'users.email',
                    'users.no_hp',
                    'datasatpam.nik',
                    'datasatpam.tanggal_lahir',
                    'datasatpam.no_kep',
                    'datasatpam.seksi_unit_gerbang',
                    'users.created_at'
                )
                ->first();

            if (!$userProfile) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data satpam tidak ditemukan',
                    'data' => null
                ], 404);
            }

            // Format tanggal untuk tampilan
            $formattedTanggalLahir = Carbon::parse($userProfile->tanggal_lahir)->format('d F Y');
            $formattedCreatedAt = Carbon::parse($userProfile->created_at)->format('d F Y');

            // Siapkan data untuk response
            $formattedProfile = [
                'nama' => $userProfile->nama,
                'email' => $userProfile->email,
                'no_hp' => $userProfile->no_hp,
                'nik' => $userProfile->nik,
                'tanggal_lahir' => $formattedTanggalLahir,
                'no_kep' => $userProfile->no_kep,
                'seksi_unit_gerbang' => $userProfile->seksi_unit_gerbang,
                'terdaftar_pada' => $formattedCreatedAt
            ];

            return response()->json([
                'status' => true,
                'message' => 'Data profile berhasil dimuat',
                'data' => $formattedProfile
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
