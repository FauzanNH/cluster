<?php

namespace App\Http\Controllers\Api\tamu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginApiControler extends Controller
{
    public function loginTamu(Request $request)
    {
        $request->validate([
            'tamu_id' => 'required|string',
        ]);

        $tamu = \App\Models\Tamu::where('tamu_id', $request->tamu_id)->first();
        if ($tamu) {
            return response()->json([
                'success' => true,
                'message' => 'Tamu ditemukan',
                'data' => $tamu
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'ID Tamu tidak ditemukan',
            ], 404);
        }
    }
}
