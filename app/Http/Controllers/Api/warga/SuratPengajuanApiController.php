<?php

namespace App\Http\Controllers\Api\warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\SuratPengajuan;
use App\Models\DataPenduduk; 
use Illuminate\Support\Facades\Storage;

class SuratPengajuanApiController extends Controller
{
  
    public function getWarga()
    {
        $warga = DataPenduduk::select('warga_id', 'nama')->get();
        return response()->json($warga);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'warga_id' => 'required|string',
            'rumah_id' => 'required|string',
            'jenis_surat' => 'required|string',
            'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'kartu_keluarga' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'dokumen_lainnya1' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'dokumen_lainnya2' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'keperluan_keramaian' => 'nullable|string',
            'tempat_keramaian' => 'nullable|string',
            'tanggal_keramaian' => 'nullable|string',
            'jam_keramaian' => 'nullable|string',
        ]);

        $data = $validated;
        $data['surat_id'] = 'SRT' . mt_rand(1000000000, 9999999999);
        $data['status_penegerjaan'] = 'menunggu verifikasi';

     
        foreach(['foto_ktp','kartu_keluarga','dokumen_lainnya1','dokumen_lainnya2'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('gambar/suratpengantar'), $filename);
                $data[$field] = 'gambar/suratpengantar/' . $filename;
            }
        }

        $surat = SuratPengajuan::create($data);
        return response()->json(['success' => true, 'data' => $surat]);
    }

    public function getAnggotaRumah(Request $request)
    {
        $users_id = $request->query('users_id');
        $rumah_id = $request->query('rumah_id');
        $query = \App\Models\DataRumah::query();
        if ($users_id) $query->where('users_id', $users_id);
        if ($rumah_id) $query->where('rumah_id', $rumah_id);
        $rumah = $query->first();

        $anggota = [];
        if ($rumah) {
         
            foreach (['warga_id1','warga_id2','warga_id3','warga_id4','warga_id5'] as $col) {
                if ($rumah->$col) {
                    $warga = \App\Models\DataPenduduk::where('warga_id', $rumah->$col)->first();
                    if ($warga) $anggota[] = ['warga_id' => $warga->warga_id, 'nama' => $warga->nama];
                }
            }
        }
        return response()->json($anggota);
    }

    public function getSuratByRumahId(Request $request)
    {
        $rumah_id = $request->query('rumah_id');
        if (!$rumah_id) {
            return response()->json(['success' => false, 'message' => 'rumah_id is required'], 400);
        }
        $surat = SuratPengajuan::where('rumah_id', $rumah_id)->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $surat]);
    }

    public function viewsurat(Request $request, $surat_id)
    {
        $surat = \App\Models\SuratPengajuan::where('surat_id', $surat_id)->first();
        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan'], 404);
        }
        $warga = \App\Models\DataPenduduk::where('warga_id', $surat->warga_id)->first();
        $result = [
            'surat_id' => $surat->surat_id,
            'jenis_surat' => $surat->jenis_surat,
            'warga_id' => $surat->warga_id,
            'nama' => $warga ? $warga->nama : null,
            'nik' => $warga ? $warga->nik : null,
            'status_penegerjaan' => $surat->status_penegerjaan,
            'created_at' => $surat->created_at,
        ];
        return response()->json(['success' => true, 'data' => $result]);
    }

    public function getSuratStatsByRumahId(Request $request)
    {
        $rumah_id = $request->query('rumah_id');
        if (!$rumah_id) {
            return response()->json(['success' => false, 'message' => 'rumah_id is required'], 400);
        }
        $total = \App\Models\SuratPengajuan::where('rumah_id', $rumah_id)->count();
        $menunggu = \App\Models\SuratPengajuan::where('rumah_id', $rumah_id)->where('status_penegerjaan', 'menunggu verifikasi')->count();
        $validasi = \App\Models\SuratPengajuan::where('rumah_id', $rumah_id)->where('status_penegerjaan', 'sedang di validasi')->count();
        $disetujui = \App\Models\SuratPengajuan::where('rumah_id', $rumah_id)->where('status_penegerjaan', 'disetujui')->count();
        $ditolak = \App\Models\SuratPengajuan::where('rumah_id', $rumah_id)->where('status_penegerjaan', 'ditolak')->count();
        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'menunggu_verifikasi' => $menunggu,
                'sedang_divalidasi' => $validasi,
                'disetujui' => $disetujui,
                'ditolak' => $ditolak,
            ]
        ]);
    }

    public function destroy($surat_id)
    {
        $surat = SuratPengajuan::where('surat_id', $surat_id)->first();
        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan'], 404);
        }

 
        foreach(['foto_ktp','kartu_keluarga','dokumen_lainnya1','dokumen_lainnya2'] as $field) {
            if ($surat->$field) {
                $publicPath = $surat->$field;
                $fullPath = public_path($publicPath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        }

        $surat->delete();
        return response()->json(['success' => true, 'message' => 'Surat berhasil dihapus']);
    }
}
