<?php

namespace App\Http\Controllers\api\warga\beranda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaduan;
use Illuminate\Support\Str;

class PengaduanApiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jenis_pengaduan' => 'required|string|max:32',
            'detail_pengaduan' => 'required|string',
            'lokasi' => 'nullable|string|max:255',
            'dokumen1' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:5120',
            'dokumen2' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:5120',
            'users_id' => 'required|integer',
            'blok_rt' => 'nullable|string|max:255',
        ]);

        // Generate random 6 digit alphanumeric ID
        $pengaduan_id = Str::upper(Str::random(6));

        $dokumen1Path = null;
        $dokumen2Path = null;
        if ($request->hasFile('dokumen1')) {
            $filename1 = $pengaduan_id.'_1.'.$request->file('dokumen1')->getClientOriginalExtension();
            $request->file('dokumen1')->move(public_path('gambar/pengaduan'), $filename1);
            $dokumen1Path = 'gambar/pengaduan/'.$filename1;
        }
        if ($request->hasFile('dokumen2')) {
            $filename2 = $pengaduan_id.'_2.'.$request->file('dokumen2')->getClientOriginalExtension();
            $request->file('dokumen2')->move(public_path('gambar/pengaduan'), $filename2);
            $dokumen2Path = 'gambar/pengaduan/'.$filename2;
        }

        $pengaduan = Pengaduan::create([
            'pengaduan_id' => $pengaduan_id,
            'users_id' => $request->users_id,
            'jenis_pengaduan' => $request->jenis_pengaduan,
            'detail_pengaduan' => $request->detail_pengaduan,
            'lokasi' => $request->lokasi,
            'status_pengaduan' => 'Tersampaikan',
            'dokumen1' => $dokumen1Path,
            'dokumen2' => $dokumen2Path,
            'blok_rt' => $request->blok_rt,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil disimpan',
            'data' => $pengaduan
        ]);
    }

    public function list(Request $request)
    {
        $users_id = $request->query('users_id');
        if (!$users_id) {
            return response()->json([
                'success' => false,
                'message' => 'users_id is required',
                'data' => []
            ], 400);
        }
        $data = \App\Models\Pengaduan::where('users_id', $users_id)
            ->orderByDesc('created_at')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function detail(Request $request)
    {
        $pengaduan_id = $request->query('pengaduan_id');
        if (!$pengaduan_id) {
            return response()->json([
                'success' => false,
                'message' => 'pengaduan_id is required',
                'data' => null
            ], 400);
        }

        $data = \App\Models\Pengaduan::select(
                'pengaduan.*', 
                'users.nama as nama_user'
            )
            ->leftJoin('users', 'pengaduan.users_id', '=', 'users.users_id')
            ->where('pengaduan.pengaduan_id', $pengaduan_id)
            ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaduan not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function destroy($pengaduan_id)
    {
        $pengaduan = \App\Models\Pengaduan::where('pengaduan_id', $pengaduan_id)->first();
        if (!$pengaduan) {
            return response()->json(['success' => false, 'message' => 'Pengaduan tidak ditemukan'], 404);
        }
        $pengaduan->delete();
        return response()->json(['success' => true, 'message' => 'Pengaduan berhasil dibatalkan/dihapus']);
    }
}
