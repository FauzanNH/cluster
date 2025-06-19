<?php

namespace App\Http\Controllers\Api\satpam;

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
        \Log::debug('OTP Request:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,users_id',
            'no_hp' => 'required|string|min:10|max:20'
        ]);
        if ($validator->fails()) {
            \Log::debug('OTP Request Validation Failed:', $validator->errors()->toArray());
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        // Cek apakah nomor sudah terdaftar di users
        $exists = User::where('no_hp', $request->no_hp)->where('users_id', '!=', $request->users_id)->exists();
        if ($exists) {
            \Log::debug('OTP Request - Phone number already registered by another user');
            return response()->json(['success' => false, 'message' => 'Nomor sudah terdaftar oleh akun lain.'], 409);
        }

        // Generate OTP 6 digit dan pastikan sebagai string
        $otpNumber = rand(100000, 999999);
        $otp_code = sprintf("%06d", $otpNumber);
        
        \Log::debug('Generated OTP:', ['otp' => $otp_code, 'type' => gettype($otp_code), 'length' => strlen($otp_code)]);
        
        $expired_at = Carbon::now()->addMinutes(5);

        // Hapus OTP lama yang belum digunakan untuk nomor dan user yang sama
        OTP::where('users_id', $request->users_id)
            ->where('no_hp', $request->no_hp)
            ->where('is_used', false)
            ->delete();
            
        $otp = OTP::create([
            'users_id' => $request->users_id,
            'no_hp' => $request->no_hp,
            'otp_code' => $otp_code,
            'is_used' => false,
            'expired_at' => $expired_at
        ]);

        \Log::debug('OTP Created:', [
            'otp_id' => $otp->id,
            'users_id' => $otp->users_id,
            'no_hp' => $otp->no_hp,
            'otp_code' => $otp->otp_code,
            'otp_code_type' => gettype($otp->otp_code),
            'otp_code_length' => strlen($otp->otp_code),
            'expired_at' => $otp->expired_at->format('Y-m-d H:i:s'),
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
        
        try {
            $response = Http::withHeaders([
                'Authorization' => $fonnteToken
            ])->post('https://api.fonnte.com/send', [
                'target' => $request->no_hp,
                'message' => $message,
                'countryCode' => '62', // opsional, default 62
            ]);

            // Cek response Fonnte
            if ($response->failed()) {
                \Log::error('Fonnte API Error:', ['response' => $response->json()]);
                return response()->json(['success' => false, 'message' => 'Gagal mengirim OTP ke WhatsApp.']);
            }
            
            \Log::debug('OTP sent successfully');
            return response()->json(['success' => true, 'message' => 'OTP berhasil dikirim ke WhatsApp.']);
        } catch (\Exception $e) {
            \Log::error('Error sending OTP:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal mengirim OTP: ' . $e->getMessage()]);
        }
    }

    // 2. Verifikasi OTP
    public function verifyOTP(Request $request)
    {
        \Log::debug('OTP Verify Request:', $request->all());
        
        // Sanitasi input dan pastikan selalu 6 digit string
        $otpCode = trim((string) $request->otp_code);
        
        // Jika OTP code sebagai angka, pastikan format 6 digit penuh
        if (is_numeric($request->otp_code)) {
            $otpCode = sprintf("%06d", (int) $request->otp_code);
        }
        
        $request->merge([
            'otp_code' => $otpCode
        ]);
        
        \Log::debug('OTP after sanitization:', ['raw' => $request->otp_code, 'sanitized' => $otpCode]);
        
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,users_id',
            'no_hp' => 'required|string|min:10|max:20',
            'otp_code' => 'required|string|size:6'
        ]);
        
        if ($validator->fails()) {
            \Log::debug('OTP Verify Validation Failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false, 
                'message' => 'Validasi gagal', 
                'errors' => $validator->errors()
            ], 422);
        }

        // Debugging query OTP
        $otpQuery = OTP::where('users_id', $request->users_id)
                ->where('no_hp', $request->no_hp)
                ->where('otp_code', $request->otp_code)
                ->where('is_used', false)
                ->where('expired_at', '>', Carbon::now());
                
        \Log::debug('OTP query:', [
            'sql' => $otpQuery->toSql(),
            'bindings' => $otpQuery->getBindings()
        ]);
                
        $otp = $otpQuery->first();

        if (!$otp) {
            // Cek OTP terbaru untuk debugging
            $latestOtp = OTP::where('users_id', $request->users_id)
                           ->where('no_hp', $request->no_hp)
                           ->latest()
                           ->first();
                           
            if ($latestOtp) {
                \Log::debug('Latest OTP found:', [
                    'otp_code' => $latestOtp->otp_code,
                    'requested_code' => $request->otp_code,
                    'is_equal' => $latestOtp->otp_code === $request->otp_code ? 'Yes' : 'No',
                    'is_used' => $latestOtp->is_used ? 'Yes' : 'No',
                    'expired_at' => $latestOtp->expired_at,
                    'now' => Carbon::now(),
                    'is_expired' => $latestOtp->expired_at < Carbon::now() ? 'Yes' : 'No',
                ]);
            } else {
                \Log::debug('No OTP records found for this user and phone number');
            }

            \Log::debug('OTP Verify - OTP not found or expired');
            return response()->json(['success' => false, 'message' => 'OTP tidak valid atau sudah kadaluarsa.'], 400);
        }

        // Tandai OTP sudah digunakan
        $otp->is_used = true;
        $otp->save();

        // Update nomor HP user
        $user = User::where('users_id', $request->users_id)->first();
        $user->no_hp = $request->no_hp;
        $user->save();

        \Log::debug('OTP Verify Success - Phone number updated for user: ' . $request->users_id);
        return response()->json(['success' => true, 'message' => 'Nomor HP berhasil diverifikasi dan diupdate.']);
    }
} 