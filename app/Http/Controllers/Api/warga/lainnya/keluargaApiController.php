<?php

namespace App\Http\Controllers\Api\warga\lainnya;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class keluargaApiController extends Controller
{
    public function getRumahInfo($rumah_id)
    {
        $rumah = \App\Models\DataRumah::where('rumah_id', $rumah_id)->first();

        if (!$rumah) {
            return response()->json([
                'success' => false,
                'message' => 'Data rumah tidak ditemukan'
            ], 404);
        }

        // Ambil semua warga_id yang ada
        $anggota_ids = [];
        for ($i = 1; $i <= 5; $i++) {
            $id = $rumah->{'warga_id'.$i};
            if ($id) $anggota_ids[] = $id;
        }

        // Ambil data anggota dari datawarga
        $anggota = [];
        if (count($anggota_ids) > 0) {
            $anggota = \DB::table('datawarga')
                ->whereIn('warga_id', $anggota_ids)
                ->get(['warga_id', 'nama', 'nik', 'tanggal_lahir', 'gender']);
        }

        $urutan_warga_id = [];
        for ($i = 1; $i <= 5; $i++) {
            $id = $rumah->{'warga_id'.$i};
            if ($id) $urutan_warga_id[] = (string)$id;
        }

        $total_anggota = count($anggota_ids);

        return response()->json([
            'success' => true,
            'data' => [
                'rumah_id' => $rumah->rumah_id,
                'no_kk' => $rumah->no_kk,
                'status_kepemilikan' => $rumah->status_kepemilikan,
                'anggota' => $anggota,
                'warga_id1' => $rumah->warga_id1,
                'urutan_warga_id' => $urutan_warga_id,
                'total_anggota' => $total_anggota
            ]
        ]);
    }
}
