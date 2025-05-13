<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Satpam;

class DataSatpamController extends Controller
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

    public function showDetail($users_id)
    {
        $user = User::where('users_id', $users_id)->first();
        $satpam = Satpam::where('users_id', $users_id)->first();
        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }
        $data = [
            'users_id' => $user->users_id,
            'nama' => $user->nama,
            'email' => $user->email,
            'alamat' => $user->alamat,
            'no_hp' => $user->no_hp,
            'nik' => $satpam ? $satpam->nik : null,
            'tanggal_lahir' => $satpam ? $satpam->tanggal_lahir : null,
            'no_kep' => $satpam ? $satpam->no_kep : null,
            'seksi_unit_gerbang' => $satpam ? $satpam->seksi_unit_gerbang : null,
        ];
        return response()->json($data);
    }

    public function storeOrUpdate(Request $request, $users_id)
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:32',
            'tanggal_lahir' => 'required|date',
            'no_kep' => 'required|string|max:64',
            'seksi_unit_gerbang' => 'required|in:Gerbang Utama,Gerbang Belakang,Gerbang Timur,Gerbang Barat',
        ]);
        $data = [
            'users_id' => $users_id,
            'nik' => $validated['nik'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'no_kep' => $validated['no_kep'],
            'seksi_unit_gerbang' => $validated['seksi_unit_gerbang'],
        ];
        $satpam = \App\Models\Satpam::updateOrCreate(['users_id' => $users_id], $data);
        return response()->json(['success' => true, 'data' => $satpam]);
    }

    public function destroySatpam($users_id)
    {
        $user = User::where('users_id', $users_id)->first();
        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }
        $user->delete(); // akan otomatis menghapus datasatpam karena foreign key onDelete cascade
        return response()->json(['success' => true]);
    }

    public function updatePasswordSatpam(Request $request, $users_id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);
        $user = User::where('users_id', $users_id)->first();
        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['success' => true]);
    }
}
