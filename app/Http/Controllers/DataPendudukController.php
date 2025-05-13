<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataPenduduk;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DataPendudukController extends Controller
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
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|digits:16|unique:datawarga,nik',
            'nokk' => 'required|digits:16',
            'domisili' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'status' => 'required|string',
            'pekerjaan' => 'required|string',
            'pendidikan' => 'required|string',
            'blok_rt' => 'required|string|max:50',
            'foto_ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_kk' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Generate random warga_id 7 karakter kombinasi huruf dan angka
        $warga_id = strtoupper(Str::random(7));

        // Handle file upload
        $fotoKtpFile = $request->file('foto_ktp');
        $fotoKkFile = $request->file('foto_kk');
        $fotoKtpName = uniqid('ktp_') . '.' . $fotoKtpFile->getClientOriginalExtension();
        $fotoKkName = uniqid('kk_') . '.' . $fotoKkFile->getClientOriginalExtension();
        $fotoKtpFile->storeAs('public/img', $fotoKtpName);
        $fotoKkFile->storeAs('public/img', $fotoKkName);

        // Simpan data ke database
        DataPenduduk::create([
            'warga_id' => $warga_id,
            'nama' => $validated['nama'],
            'nik' => $validated['nik'],
            'no_kk' => $validated['nokk'],
            'domisili_ktp' => $validated['domisili'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'gender' => $validated['gender'],
            'agama' => $request->input('agama', '-'),
            'status_pernikahan' => $validated['status'],
            'pekerjaan' => $validated['pekerjaan'],
            'pendidikan_terakhir' => $validated['pendidikan'],
            'foto_ktp' => $fotoKtpName,
            'foto_kk' => $fotoKkName,
            'blok_rt' => $validated['blok_rt'],
        ]);

        return redirect()->route('rt.DataWarga.datapenduduk')->with('success', 'Data penduduk berhasil ditambahkan.');
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
    public function edit($id)
    {
        $warga = DataPenduduk::where('warga_id', $id)->firstOrFail();
        return response()->json($warga);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $warga = DataPenduduk::where('warga_id', $id)->firstOrFail();
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|digits:16|unique:datawarga,nik,' . $warga->warga_id . ',warga_id',
            'nokk' => 'required|digits:16',
            'domisili' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'status' => 'required|string',
            'pekerjaan' => 'required|string',
            'pendidikan' => 'required|string',
            'agama' => 'required|string',
            'blok_rt' => 'required|string|max:50',
            'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Update file jika ada upload baru
        if ($request->hasFile('foto_ktp')) {
            $fotoKtpFile = $request->file('foto_ktp');
            $fotoKtpName = uniqid('ktp_') . '.' . $fotoKtpFile->getClientOriginalExtension();
            $fotoKtpFile->storeAs('public/img', $fotoKtpName);
            $warga->foto_ktp = $fotoKtpName;
        }
        if ($request->hasFile('foto_kk')) {
            $fotoKkFile = $request->file('foto_kk');
            $fotoKkName = uniqid('kk_') . '.' . $fotoKkFile->getClientOriginalExtension();
            $fotoKkFile->storeAs('public/img', $fotoKkName);
            $warga->foto_kk = $fotoKkName;
        }

        $warga->nama = $validated['nama'];
        $warga->nik = $validated['nik'];
        $warga->no_kk = $validated['nokk'];
        $warga->domisili_ktp = $validated['domisili'];
        $warga->tanggal_lahir = $validated['tanggal_lahir'];
        $warga->gender = $validated['gender'];
        $warga->status_pernikahan = $validated['status'];
        $warga->pekerjaan = $validated['pekerjaan'];
        $warga->pendidikan_terakhir = $validated['pendidikan'];
        $warga->agama = $validated['agama'];
        $warga->blok_rt = $validated['blok_rt'];
        $warga->save();

        return redirect()->route('rt.DataWarga.datapenduduk')->with('success', 'Data penduduk berhasil diupdate.');
    }

    public function destroy($id)
    {
        $warga = DataPenduduk::where('warga_id', $id)->firstOrFail();
        // Hapus file foto jika ada
        if ($warga->foto_ktp && Storage::exists('public/img/' . $warga->foto_ktp)) {
            Storage::delete('public/img/' . $warga->foto_ktp);
        }
        if ($warga->foto_kk && Storage::exists('public/img/' . $warga->foto_kk)) {
            Storage::delete('public/img/' . $warga->foto_kk);
        }
        $warga->delete();
        return redirect()->route('rt.DataWarga.datapenduduk')->with('success', 'Data penduduk & file foto berhasil dihapus.');
    }
}
