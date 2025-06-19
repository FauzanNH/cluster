<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AkunWargaController extends Controller
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
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'required|in:laki-laki,perempuan',
            'alamat' => 'required|string',
            'rt_blok' => 'nullable|string',
        ]);
        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = 'Warga';
        // Generate users_id unik
        do {
            $randomId = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (\App\Models\User::where('users_id', $randomId)->exists());
        $validated['users_id'] = $randomId;

        \App\Models\User::create($validated);

        return redirect()->route('rt.DataWarga.akunwarga')->with('success', 'Akun warga berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $users_id)
    {
        $user = User::findOrFail($users_id);
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $users_id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $users_id)
    {
        $user = User::findOrFail($users_id);
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->users_id . ',users_id',
            'no_hp' => 'required|string|max:20',
            'gender' => 'required|in:laki-laki,perempuan',
            'alamat' => 'required|string',
            'rt_blok' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        } else {
            unset($validated['password']);
        }
        $user->update($validated);
        return redirect()->route('rt.DataWarga.akunwarga')->with('success', 'Data akun warga berhasil diupdate.');
    }

    /**
     * Update the specified resource password.
     */
    public function updatePassword(Request $request, string $users_id)
    {
        $user = User::findOrFail($users_id);
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);
        $user->password = bcrypt($validated['password']);
        $user->save();
        return redirect()->route('rt.DataWarga.akunwarga')->with('success', 'Password berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $users_id)
    {
        $user = User::findOrFail($users_id);
        $user->delete();
        return redirect()->route('rt.DataWarga.akunwarga')->with('success', 'Akun warga berhasil dihapus.');
    }
}
