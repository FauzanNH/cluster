<?php

namespace App\Http\Controllers\Api\warga\keamanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keamanan;
use Illuminate\Support\Facades\Hash;

class GuardApiController extends Controller
{
    public function setPin(Request $request)
    {
        $request->validate([
            'users_id' => 'required|string',
            'pin' => 'required|string|min:4|max:6',
        ]);

        $keamanan = Keamanan::updateOrCreate(
            ['users_id' => $request->users_id],
            [
                'pin' => Hash::make($request->pin),
                'pin_active' => 'aktif',
                'hint' => $request->hint ?? '',
                'login_pin_active' => 'aktif',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'PIN berhasil disimpan',
            'data' => $keamanan
        ]);
    }

    public function checkPinStatus(Request $request)
    {
        $request->validate([
            'users_id' => 'required|string',
        ]);

        $keamanan = \App\Models\Keamanan::where('users_id', $request->users_id)->first();

        if (!$keamanan || $keamanan->pin_active !== 'aktif') {
            return response()->json([
                'success' => false,
                'pin_active' => false,
                'message' => 'PIN belum diatur atau tidak aktif'
            ]);
        }

        return response()->json([
            'success' => true,
            'pin_active' => true,
            'message' => 'PIN aktif'
        ]);
    }

    public function deletePin(Request $request)
    {
        $request->validate([
            'users_id' => 'required|string',
        ]);

        $keamanan = \App\Models\Keamanan::where('users_id', $request->users_id)->first();

        if (!$keamanan) {
            return response()->json([
                'success' => false,
                'message' => 'Data keamanan tidak ditemukan'
            ], 404);
        }

        $keamanan->pin = null;
        $keamanan->pin_active = 'nonaktif';
        $keamanan->login_pin_active = 'nonaktif';
        $keamanan->save();

        return response()->json([
            'success' => true,
            'message' => 'PIN berhasil dihapus dan dinonaktifkan'
        ]);
    }

    public function verifyPin(Request $request)
    {
        $request->validate([
            'users_id' => 'required|string',
            'pin' => 'required|string|min:4|max:6',
        ]);

        $keamanan = Keamanan::where('users_id', $request->users_id)->first();

        if (!$keamanan) {
            return response()->json([
                'success' => false,
                'message' => 'Data keamanan tidak ditemukan'
            ], 404);
        }

        if ($keamanan->pin_active !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'PIN tidak aktif'
            ]);
        }

        if (!Hash::check($request->pin, $keamanan->pin)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN tidak valid'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'PIN valid'
        ]);
    }

    public function updatePin(Request $request)
    {
        $request->validate([
            'users_id' => 'required|string',
            'old_pin' => 'required|string|min:4|max:6',
            'new_pin' => 'required|string|min:4|max:6',
        ]);

        $keamanan = Keamanan::where('users_id', $request->users_id)->first();

        if (!$keamanan) {
            return response()->json([
                'success' => false,
                'message' => 'Data keamanan tidak ditemukan'
            ], 404);
        }

        if ($keamanan->pin_active !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'PIN tidak aktif'
            ]);
        }

        // Verify old PIN
        if (!Hash::check($request->old_pin, $keamanan->pin)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN lama tidak valid'
            ], 400);
        }

        // Update with new PIN
        $keamanan->pin = Hash::make($request->new_pin);
        $keamanan->save();

        return response()->json([
            'success' => true,
            'message' => 'PIN berhasil diperbarui'
        ]);
    }

    public function setPinSecurity(Request $request)
    {
        $request->validate([
            'users_id' => 'required|string',
            'enabled' => 'required|boolean',
        ]);

        $keamanan = \App\Models\Keamanan::where('users_id', $request->users_id)->first();

        if (!$keamanan) {
            return response()->json([
                'success' => false,
                'message' => 'Data keamanan tidak ditemukan'
            ], 404);
        }

        $keamanan->login_pin_active = $request->enabled ? 'aktif' : 'nonaktif';
        $keamanan->save();

        return response()->json([
            'success' => true,
            'message' => 'Status keamanan PIN saat akses aplikasi berhasil diubah',
            'login_pin_active' => $keamanan->login_pin_active
        ]);
    }

    public function getPinSecurityStatus(Request $request)
    {
        $request->validate([
            'users_id' => 'required|string',
        ]);

        $keamanan = \App\Models\Keamanan::where('users_id', $request->users_id)->first();

        if (!$keamanan) {
            return response()->json([
                'success' => false,
                'message' => 'Data keamanan tidak ditemukan',
                'pin_security_enabled' => false
            ], 404);
        }

        $enabled = $keamanan->login_pin_active === 'aktif';

        return response()->json([
            'success' => true,
            'pin_security_enabled' => $enabled,
            'login_pin_active' => $keamanan->login_pin_active
        ]);
    }

    public function verifyHint(Request $request)
    {
        $request->validate([
            'users_id' => 'required|string',
            'hint' => 'required|string|min:3',
        ]);

        $keamanan = Keamanan::where('users_id', $request->users_id)->first();

        if (!$keamanan) {
            return response()->json([
                'success' => false,
                'message' => 'Data keamanan tidak ditemukan'
            ], 404);
        }

        if (!$keamanan->hint) {
            return response()->json([
                'success' => false,
                'message' => 'Hint PIN belum diatur'
            ]);
        }

        // Verifikasi hint dengan case-insensitive
        if (strtolower($keamanan->hint) !== strtolower($request->hint)) {
            return response()->json([
                'success' => false,
                'message' => 'Hint tidak valid'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hint valid',
            'hint' => $keamanan->hint
        ]);
    }

    public function resetPin(Request $request)
    {
        $request->validate([
            'users_id' => 'required|string',
            'new_pin' => 'required|string|min:4|max:6',
            'hint' => 'required|string',
        ]);

        $keamanan = Keamanan::where('users_id', $request->users_id)->first();

        if (!$keamanan) {
            return response()->json([
                'success' => false,
                'message' => 'Data keamanan tidak ditemukan'
            ], 404);
        }

        // Update with new PIN
        $keamanan->pin = Hash::make($request->new_pin);
        $keamanan->pin_active = 'aktif';
        $keamanan->save();

        return response()->json([
            'success' => true,
            'message' => 'PIN berhasil direset'
        ]);
    }

    public function getHint(Request $request)
    {
        $request->validate([
            'users_id' => 'required|string',
        ]);

        $keamanan = Keamanan::where('users_id', $request->users_id)->first();

        if (!$keamanan) {
            return response()->json([
                'success' => false,
                'message' => 'Data keamanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'hint' => $keamanan->hint
        ]);
    }

    public function updateHint(Request $request)
    {
        $request->validate([
            'users_id' => 'required|string',
            'hint' => 'required|string|min:3',
        ]);

        $keamanan = Keamanan::where('users_id', $request->users_id)->first();

        if (!$keamanan) {
            return response()->json([
                'success' => false,
                'message' => 'Data keamanan tidak ditemukan'
            ], 404);
        }

        $keamanan->hint = $request->hint;
        $keamanan->save();

        return response()->json([
            'success' => true,
            'message' => 'Hint berhasil diperbarui',
            'hint' => $keamanan->hint
        ]);
    }
}
