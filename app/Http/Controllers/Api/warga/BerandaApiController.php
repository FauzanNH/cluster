<?php

namespace App\Http\Controllers\Api\warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DataRumah;

class BerandaApiController extends Controller
{
    
    public function showUserRumah($users_id)
    {
        $user = User::where('users_id', $users_id)->first();
        $rumah = DataRumah::where('users_id', $users_id)->first();
        if (!$user || !$rumah) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
        return response()->json([
            'nama' => $user->nama,
            'rumah_id' => $rumah->rumah_id
        ]);
    }

   
    public function getTotalAnggotaRumah($rumah_id)
    {
        $rumah = DataRumah::where('rumah_id', $rumah_id)->first();
        if (!$rumah) {
            return response()->json(['success' => false, 'message' => 'Data rumah tidak ditemukan'], 404);
        }
        $total = 0;
        foreach(['warga_id1','warga_id2','warga_id3','warga_id4','warga_id5'] as $col) {
            if (!empty($rumah->$col)) $total++;
        }
        return response()->json(['success' => true, 'total_anggota' => $total]);
    }
}
