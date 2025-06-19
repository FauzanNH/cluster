<?php

namespace App\Http\Controllers\Api\satpam\keamanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class KeamananApiController extends Controller
{
    /**
     * Update email user berdasarkan users_id
     */
    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|string|exists:users,users_id',
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('users_id', $request->users_id)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        $user->email = $request->email;
        $user->save();

        // Catat aktivitas perubahan email
        $aktId = 'AK' . date('ymd') . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        \App\Models\Aktivitas::create([
            'aktivitas_id' => $aktId,
            'users_id' => $request->users_id,
            'judul' => 'Ubah Email',
            'sub_judul' => 'Berhasil memperbarui email akun'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diupdate',
            'user' => [
                'users_id' => $user->users_id,
                'email' => $user->email
            ]
        ]);
    }

    /**
     * Ambil email user berdasarkan users_id
     */
    public function getCurrentEmail(Request $request)
    {
        $request->validate([
            'users_id' => 'required|string|exists:users,users_id',
        ]);
        $user = User::where('users_id', $request->users_id)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'email' => $user->email
        ]);
    }

    /**
     * Update password user berdasarkan users_id
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|string|exists:users,users_id',
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('users_id', $request->users_id)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan.'
                ], 404);
            }

            // Verifikasi password saat ini, kecuali untuk reset password (lupa password)
            if ($request->current_password !== '-') {
                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Password saat ini tidak sesuai.'
                    ], 422);  // Gunakan 422 daripada 401 untuk menghindari interceptor logout
                }
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Catat aktivitas perubahan password
            $aktId = 'AK' . date('ymd') . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            \App\Models\Aktivitas::create([
                'aktivitas_id' => $aktId,
                'users_id' => $request->users_id,
                'judul' => 'Ubah Password',
                'sub_judul' => 'Berhasil memperbarui password akun'
            ]);

            // Generate token untuk menghindari logout
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diperbarui',
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => null // Sanctum tidak memiliki TTL default seperti JWT
            ]);
        } catch (\Exception $e) {
            \Log::error('Update password error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update nomor HP user berdasarkan users_id
     */
    public function updatePhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|string|exists:users,users_id',
            'no_hp' => 'required|string|min:10|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('users_id', $request->users_id)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        $user->no_hp = $request->no_hp;
        $user->save();

        // Catat aktivitas perubahan nomor HP
        $aktId = 'AK' . date('ymd') . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        \App\Models\Aktivitas::create([
            'aktivitas_id' => $aktId,
            'users_id' => $request->users_id,
            'judul' => 'Ubah Nomor HP',
            'sub_judul' => 'Berhasil memperbarui nomor HP'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nomor HP berhasil diperbarui',
            'user' => [
                'users_id' => $user->users_id,
                'no_hp' => $user->no_hp
            ]
        ]);
    }

    /**
     * Ambil nomor HP user berdasarkan users_id
     */
    public function getCurrentPhone(Request $request)
    {
        $request->validate([
            'users_id' => 'required|string|exists:users,users_id',
        ]);
        $user = User::where('users_id', $request->users_id)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'no_hp' => $user->no_hp
        ]);
    }
    
    /**
     * Ambil status PIN keamanan berdasarkan users_id
     */
    public function getPinSecurity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|string|exists:users,users_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $keamanan = \App\Models\Keamanan::where('users_id', $request->users_id)->first();
        
        if (!$keamanan) {
            return response()->json([
                'success' => true,
                'pin_security_enabled' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'pin_security_enabled' => (bool)$keamanan->pin_active
        ]);
    }
} 