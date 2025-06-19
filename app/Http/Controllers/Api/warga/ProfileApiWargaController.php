<?php

namespace App\Http\Controllers\Api\warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataRumah;
use App\Models\User;

class ProfileApiWargaController extends Controller
{
    public function getProfile(Request $request)
    {
        $rumah_id = $request->input('rumah_id');
        $dataRumah = DataRumah::where('rumah_id', $rumah_id)->first();
        if (!$dataRumah) {
            return response()->json(['message' => 'Data rumah tidak ditemukan'], 404);
        }
        $user = User::where('users_id', $dataRumah->users_id)->first();
        $totalAnggota = collect([
            $dataRumah->warga_id1,
            $dataRumah->warga_id2,
            $dataRumah->warga_id3,
            $dataRumah->warga_id4,
            $dataRumah->warga_id5
        ])->filter()->count();

        return response()->json([
            'nama' => $user ? $user->nama : null,
            'rumah_id' => $dataRumah->rumah_id,
            'no_kk' => $dataRumah->no_kk,
            'total_anggota_keluarga' => $totalAnggota,
            'status_kepemilikan' => $dataRumah->status_kepemilikan,
            'blok_rt' => $dataRumah->blok_rt,
            'alamat_cluster' => $dataRumah->alamat_cluster,
        ]);
    }
}
