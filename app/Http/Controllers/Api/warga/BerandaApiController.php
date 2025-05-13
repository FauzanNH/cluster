<?php

namespace App\Http\Controllers\Api\warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DataRumah;

class BerandaApiController extends Controller
{
    public function getUserAndRumah(Request $request)
    {
        $users_id = $request->input('users_id');
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
}
