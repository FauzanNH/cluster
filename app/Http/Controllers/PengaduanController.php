<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan;
use App\Models\User;

class PengaduanController extends Controller
{
    public function showDetail($id)
    {
        $keluhan = Pengaduan::where('pengaduan_id', $id)->first();
        $user = $keluhan ? User::where('users_id', $keluhan->users_id)->first() : null;

        if (!$keluhan) {
            return response()->json(['message' => 'Keluhan tidak ditemukan'], 404);
        }

        return response()->json([
            'pengaduan_id' => $keluhan->pengaduan_id,
            'nama_pelapor' => $user ? $user->nama : ($keluhan->nama_pelapor ?? '-'),
            'jenis_pengaduan' => $keluhan->jenis_pengaduan,
            'detail_pengaduan' => $keluhan->detail_pengaduan,
            'lokasi' => $keluhan->lokasi,
            'dokumen1' => $keluhan->dokumen1 ? asset($keluhan->dokumen1) : null,
            'dokumen2' => $keluhan->dokumen2 ? asset($keluhan->dokumen2) : null,
            'created_at' => $keluhan->created_at,
            'status_pengaduan' => $keluhan->status_pengaduan,
            'remark' => $keluhan->remark,
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $keluhan = \App\Models\Pengaduan::where('pengaduan_id', $id)->first();
        if (!$keluhan) {
            return response()->json(['success' => false, 'message' => 'Keluhan tidak ditemukan'], 404);
        }
        if ($request->has('remark')) {
            $keluhan->remark = $request->input('remark');
        }
        $keluhan->status_pengaduan = 'Dibaca RT';
        $keluhan->save();
        return response()->json(['success' => true, 'message' => 'Status berhasil diubah menjadi Dibaca RT']);
    }
}
