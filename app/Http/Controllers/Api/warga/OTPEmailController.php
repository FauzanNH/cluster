<?php

namespace App\Http\Controllers\Api\warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OTPemail;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class OTPEmailController extends Controller
{
    // 1. Request OTP Email
    public function requestOTPEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,users_id',
            'email' => 'required|email|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        // Cek apakah email sudah terdaftar di users
        $exists = User::where('email', $request->email)->where('users_id', '!=', $request->users_id)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Email sudah terdaftar oleh akun lain.'], 409);
        }

        $otp_code = rand(100000, 999999);
        $expired_at = Carbon::now()->addMinutes(5);

        OTPemail::create([
            'users_id' => $request->users_id,
            'email' => $request->email,
            'otp_code' => $otp_code,
            'is_used' => false,
            'expired_at' => $expired_at
        ]);

        // Kirim email OTP pakai blade template
        $userData = User::where('users_id', $request->users_id)->first();
        $nama = $userData ? $userData->nama : '';
        $subject = 'Kode OTP Verifikasi Email';
        $expiredFormat = $expired_at->format('H:i, d M Y');
        Mail::send('emails.otp_email', [
            'otp_code' => $otp_code,
            'expired_at' => $expiredFormat,
            'nama' => $nama
        ], function ($message) use ($request, $subject) {
            $message->to($request->email)
                    ->subject($subject);
        });

        return response()->json(['success' => true, 'message' => 'OTP berhasil dikirim ke email.']);
    }

    // 2. Verifikasi OTP Email
    public function verifyOTPEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,users_id',
            'email' => 'required|email|max:255',
            'otp_code' => 'required|string|size:6'
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $otp = OTPemail::where([
            ['users_id', $request->users_id],
            ['email', $request->email],
            ['otp_code', $request->otp_code],
            ['is_used', false]
        ])->where('expired_at', '>', Carbon::now())->first();

        if (!$otp) {
            return response()->json(['success' => false, 'message' => 'OTP tidak valid atau sudah kadaluarsa.'], 400);
        }

        // Tandai OTP sudah digunakan
        $otp->is_used = true;
        $otp->save();

        // Update email user
        $user = User::where('users_id', $request->users_id)->first();
        $user->email = $request->email;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Email berhasil diverifikasi dan diupdate.']);
    }

    // Request OTP untuk reset password
    public function requestResetPasswordOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|exists:users,email'
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email tidak terdaftar di sistem kami.'], 404);
        }
        $users_id = $user->users_id;
        $nama = $user->nama;

        $otp_code = rand(100000, 999999);
        $expired_at = now()->addMinutes(5);

        \App\Models\OTPemail::create([
            'users_id' => $users_id,
            'email' => $request->email,
            'otp_code' => $otp_code,
            'is_used' => false,
            'expired_at' => $expired_at
        ]);

        // Variasi subjek email
        $subjectTemplates = [
            'Kode OTP Reset Password',
            'Reset Password - Bukit Asri Cluster',
            'Permintaan Reset Password',
            'Kode Verifikasi Reset Password',
            'OTP Reset Password Akun Anda',
            'Reset Password - Kode OTP',
        ];
        $subject = $subjectTemplates[array_rand($subjectTemplates)];

        $expiredFormat = $expired_at->format('H:i, d M Y');
        // Kirim email pakai template khusus reset password
        \Mail::send('emails.otp_reset_password', [
            'otp_code' => $otp_code,
            'expired_at' => $expiredFormat,
            'nama' => $nama
        ], function ($message) use ($request, $subject) {
            $message->to($request->email)
                    ->subject($subject);
        });

        return response()->json(['success' => true, 'message' => 'OTP reset password berhasil dikirim ke email.']);
    }

    // Verifikasi OTP untuk reset password (tanpa update email)
    public function verifyResetPasswordOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|exists:users,email',
            'otp_code' => 'required|string|size:6'
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email tidak ditemukan.'], 404);
        }
        $users_id = $user->users_id;

        $otp = \App\Models\OTPemail::where([
            ['users_id', $users_id],
            ['email', $request->email],
            ['otp_code', $request->otp_code],
            ['is_used', false]
        ])->where('expired_at', '>', now())->first();

        if (!$otp) {
            return response()->json(['success' => false, 'message' => 'OTP tidak valid atau sudah kadaluarsa.'], 400);
        }

        // Tandai OTP sudah digunakan
        $otp->is_used = true;
        $otp->save();

        // Tidak update email, hanya return sukses
        return response()->json(['success' => true, 'message' => 'OTP valid, silakan lanjutkan reset password.', 'users_id' => $users_id]);
    }
}
