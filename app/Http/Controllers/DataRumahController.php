<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataRumah;
use App\Models\User;
use App\Models\DataPenduduk;

class DataRumahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $blok_rt = $user->rt_blok;
        
        // Ambil semua user warga dengan data lengkap
        $users = User::where('role', 'warga')->get();
        
        // Ambil semua data penduduk
        $warga = DataPenduduk::all();
        
        // Data rumah untuk RT yang login (untuk ditampilkan di tabel)
        $datarumah = DataRumah::where('blok_rt', $blok_rt)
            ->with('kepalaKeluarga', 'anggota1', 'anggota2', 'anggota3', 'anggota4', 'anggota5')
            ->get();
        
        // Mendapatkan semua data rumah (untuk mengecek warga_id yang sudah digunakan)
        $allDataRumah = DataRumah::all();
        
        // Mendapatkan daftar users_id yang sudah digunakan
        $usedUsersIds = DataRumah::pluck('users_id')->toArray();
        
        // Mendapatkan daftar warga_id yang sudah digunakan sebagai anggota keluarga
        $usedWargaIds = [];
        foreach ($allDataRumah as $rumah) {
            for ($i = 1; $i <= 5; $i++) {
                $field = 'warga_id' . $i;
                if (!empty($rumah->$field)) {
                    if (!isset($usedWargaIds[$rumah->$field])) {
                        $usedWargaIds[$rumah->$field] = [];
                    }
                    $usedWargaIds[$rumah->$field][] = $rumah->rumah_id;
                }
            }
        }
        
        // Buat mapping users_id ke nama user untuk mempermudah akses di JavaScript
        $usersMap = [];
        foreach ($users as $user) {
            $usersMap[$user->users_id] = [
                'nama' => $user->nama,
                'users_id' => $user->users_id
            ];
        }
        
        // Buat mapping warga_id ke nama warga untuk mempermudah akses di JavaScript
        $wargaMap = [];
        foreach ($warga as $w) {
            $wargaMap[$w->warga_id] = [
                'nama' => $w->nama,
                'warga_id' => $w->warga_id
            ];
        }
        
        return view('rt.DataWarga.datarumah', compact(
            'users', 
            'warga', 
            'datarumah', 
            'usedUsersIds', 
            'usedWargaIds', 
            'usersMap', 
            'wargaMap'
        ));
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
        $request->validate([
            'rumah_id' => 'required|string',
            'no_kk' => 'nullable|string',
            'users_id' => 'required',
            'warga_id1' => 'nullable',
            'warga_id2' => 'nullable',
            'warga_id3' => 'nullable',
            'warga_id4' => 'nullable',
            'warga_id5' => 'nullable',
            'blok_rt' => 'required|string',
            'status_kepemilikan' => 'required|string',
            'alamat_cluster' => 'required|string',
        ]);
        DataRumah::create($request->all());
        return redirect()->back()->with('success', 'Data rumah berhasil ditambahkan!');
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
    public function update(Request $request, $rumah_id)
    {
        $request->validate([
            'users_id' => 'required',
            'warga_id1' => 'nullable',
            'warga_id2' => 'nullable',
            'warga_id3' => 'nullable',
            'warga_id4' => 'nullable',
            'warga_id5' => 'nullable',
            'blok_rt' => 'required|string',
            'status_kepemilikan' => 'required|string',
            'alamat_cluster' => 'required|string',
        ]);
        $rumah = DataRumah::where('rumah_id', $rumah_id)->firstOrFail();
        $rumah->update($request->all());
        return redirect()->back()->with('success', 'Data rumah berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($rumah_id)
    {
        $rumah = DataRumah::where('rumah_id', $rumah_id)->firstOrFail();
        $rumah->delete();
        return redirect()->back()->with('success', 'Data rumah berhasil dihapus!');
    }
}
