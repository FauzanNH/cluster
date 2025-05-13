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
        $users = User::where('role', 'warga')->get();
        $warga = DataPenduduk::all();
        $datarumah = DataRumah::all();
        return view('rt.DataWarga.datarumah', compact('users', 'warga', 'datarumah'));
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
