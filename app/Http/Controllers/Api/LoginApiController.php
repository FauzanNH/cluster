<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataRumah;
use Illuminate\Support\Facades\Auth;

class LoginApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required'],
            'password' => ['required'],
        ]);
        $loginInput = $request->login;
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $credentials = [
                'email' => $loginInput,
                'password' => $request->password,
            ];
        } else {
            $user = \App\Models\User::where('no_hp', $loginInput)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No HP tidak ditemukan.'
                ], 401);
            }
            $credentials = [
                'email' => $user->email,
                'password' => $request->password,
            ];
        }

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email/No HP atau password salah.'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        if ($user->role === 'Satpam') {
            // Cek di tabel datasatpam
            $satpam = \DB::table('datasatpam')->where('users_id', $user->users_id)->first();
            if (!$satpam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Satpam belum di daftarkan.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'bearer',
                'role' => $user->role,
                'user' => [
                    'users_id' => $user->users_id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'no_hp' => $user->no_hp,
                    'role' => $user->role,
                    'no_kep' => $satpam->no_kep ?? null,
                ]
            ]);
        } else {
            // Default: cek di datarumah (untuk Warga)
            $rumah = \App\Models\DataRumah::where('users_id', $user->users_id)->first();
            if (!$rumah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Rumah belum di daftarkan.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'bearer',
                'role' => $user->role,
                'user' => [
                    'users_id' => $user->users_id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'no_hp' => $user->no_hp,
                    'role' => $user->role,
                    'rt_blok' => $rumah->blok_rt,
                    'rumah_id' => $rumah->rumah_id,
                ]
            ]);
        }
    }
    
    
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil logout.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal logout.'
            ], 500);
        }
    }
}
