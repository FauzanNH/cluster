<?php

namespace App\Http\Controllers\api\warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OTP;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class OTPController extends Controller
{
    // 1. Request OTP
    public function requestOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,users_id',
            'no_hp' => 'required|string|min:10|max:20'
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        // Cek apakah nomor sudah terdaftar di users
        $exists = User::where('no_hp', $request->no_hp)->where('users_id', '!=', $request->users_id)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Nomor sudah terdaftar oleh akun lain.'], 409);
        }

        $otp_code = rand(100000, 999999);
        $expired_at = Carbon::now()->addMinutes(5);

        $otp = OTP::create([
            'users_id' => $request->users_id,
            'no_hp' => $request->no_hp,
            'otp_code' => $otp_code,
            'is_used' => false,
            'expired_at' => $expired_at
        ]);

        // Template pesan OTP yang bervariasi
        $messageTemplates = [
            "Halo! 👋 Kode OTP kamu: $otp_code (berlaku 5 menit). Jangan kasih tau siapa-siapa ya! 🤫",
            "Hai! Ini kode verifikasi kamu: $otp_code. Berlaku 5 menit. Keep it secret! 🔒",
            "Kode OTP: $otp_code. Berlaku 5 menit. Jangan dibagikan ke orang lain ya! ✨",
            "OTP kamu: $otp_code (berlaku 5 menit). Rahasiakan kode ini ya! 🙏",
            "Ini dia kode OTP-mu: $otp_code. Berlaku 5 menit. Jangan share ke siapapun! 🚫",
            "Kode verifikasi Bukit Asri: $otp_code (5 menit). Simpan untuk diri sendiri ya! 👍",
            "Hei! $otp_code adalah kode OTP kamu. Berlaku 5 menit. Jangan dibagikan! 😊",
            "Kode rahasia kamu: $otp_code. Berlaku 5 menit. Ingat, jangan dibagi-bagi! 🤐",
            "Kode OTP Bukit Asri: $otp_code (berlaku 5 menit). Kode ini privasi kamu! 🔐",
            "Verifikasi akun: $otp_code. Berlaku 5 menit. Jangan beritahu siapapun ya! ⚠️"
        ];

        // Pilih template secara random
        $randomIndex = array_rand($messageTemplates);
        $message = $messageTemplates[$randomIndex];

        // Kirim ke Fonnte
        $fonnteToken = env('FONNTE_TOKEN'); // simpan token di .env

        $response = Http::withHeaders([
            'Authorization' => $fonnteToken
        ])->post('https://api.fonnte.com/send', [
            'target' => $request->no_hp,
            'message' => $message,
            'countryCode' => '62', // opsional, default 62
        ]);

        // Cek response Fonnte
        if ($response->failed()) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim OTP ke WhatsApp.']);
        }

        return response()->json(['success' => true, 'message' => 'OTP berhasil dikirim ke WhatsApp.']);
    }

    // 2. Verifikasi OTP
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,users_id',
            'no_hp' => 'required|string|min:10|max:20',
            'otp_code' => 'required|string|size:6'
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $otp = OTP::where([
            ['users_id', $request->users_id],
            ['no_hp', $request->no_hp],
            ['otp_code', $request->otp_code],
            ['is_used', false]
        ])->where('expired_at', '>', Carbon::now())->first();

        if (!$otp) {
            return response()->json(['success' => false, 'message' => 'OTP tidak valid atau sudah kadaluarsa.'], 400);
        }

        // Tandai OTP sudah digunakan
        $otp->is_used = true;
        $otp->save();

        // Update nomor HP user
        $user = User::where('users_id', $request->users_id)->first();
        $user->no_hp = $request->no_hp;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Nomor HP berhasil diverifikasi dan diupdate.']);
    }
}
