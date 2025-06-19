<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratPengajuan;

class SuratPengantarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Update status penegerjaan surat pengajuan.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'surat_id' => 'required|string',
            'status_penegerjaan' => 'required|string',
        ]);
        $surat = SuratPengajuan::where('surat_id', $request->surat_id)->first();
        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan'], 404);
        }
        $surat->status_penegerjaan = $request->status_penegerjaan;
        $surat->save();
        return response()->json(['success' => true, 'message' => 'Status berhasil diupdate']);
    }

    /**
     * Tampilkan halaman surat pengantar (web).
     */
    public function suratpengantar()
    {
        $user = auth()->user();
        $blok_rt = $user->rt_blok;
        $suratpengantar = \App\Models\SuratPengajuan::join('datawarga', 'suratpengajuan.warga_id', '=', 'datawarga.warga_id')
            ->select('suratpengajuan.*', 'datawarga.nama', 'datawarga.nik', 'datawarga.blok_rt')
            ->where('datawarga.blok_rt', $blok_rt)
            ->get();

        foreach ($suratpengantar as $surat) {
            foreach (['foto_ktp', 'kartu_keluarga', 'dokumen_lainnya1', 'dokumen_lainnya2'] as $field) {
                if ($surat->$field) {
                    $relativePath = ltrim($surat->$field, '/');
                    if (strpos($relativePath, 'gambar/suratpengantar/') === 0) {
                        $surat->$field = asset($relativePath);
                    } else {
                        $surat->$field = asset('gambar/suratpengantar/' . $relativePath);
                    }
                } else {
                    $surat->$field = null;
                }
            }
        }
        return view('rt.DataWarga.suratpengantar', compact('suratpengantar'));
    }
}
