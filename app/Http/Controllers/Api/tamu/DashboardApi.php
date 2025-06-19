<?php

namespace App\Http\Controllers\Api\tamu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tamu;
use App\Models\DetailTamu;

class DashboardApi extends Controller
{
    public function getTamuInfo(Request $request)
    {
        $tamu_id = $request->input('tamu_id');
        $detail = DetailTamu::where('tamu_id', $tamu_id)->first();
        $tamu = Tamu::where('tamu_id', $tamu_id)->first();

        if (!$detail || !$tamu) {
            return response()->json(['message' => 'Tamu tidak ditemukan'], 404);
        }

        return response()->json([
            'tamu_id' => $tamu->tamu_id,
            'nama' => $detail->nama
        ]);
    }
}
