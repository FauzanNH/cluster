<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PengaturanController extends Controller
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
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama'    => 'required|string|max:255',
            'email'   => 'required|email|max:255|unique:users,email,' . $user->users_id . ',users_id',
            'no_hp'   => 'nullable|string|max:20',
            'rt_blok' => [
                'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($user) {
                    if ($value) {
                        $exists = \App\Models\User::where('rt_blok', $value)
                            ->where('role', 'RT')
                            ->where('users_id', '!=', $user->users_id)
                            ->exists();
                        if ($exists) {
                            $fail('Blok RT ' . $value . ' sudah digunakan oleh RT lain.');
                        }
                    }
                }
            ],
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah.');
    }
}
