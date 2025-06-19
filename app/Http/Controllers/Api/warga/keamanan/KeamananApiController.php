<?php

namespace App\Http\Controllers\api\warga\keamanan;

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
                ], 422);
            }
        }

        try {
            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Generate token untuk menghindari logout
            $newToken = auth('api')->login($user);
            $expiresIn = auth('api')->factory()->getTTL() * 60;

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diperbarui',
                'token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => $expiresIn
            ]);
        } catch (\Exception $e) {
            \Log::error('Update password error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui password.'
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
}
