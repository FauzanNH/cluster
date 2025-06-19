<?php

namespace App\Http\Controllers\Api\satpam;

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
        \Log::debug('OTP Email Request:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,users_id',
            'email' => 'required|email|max:255'
        ]);
        if ($validator->fails()) {
            \Log::debug('OTP Email Validation Failed:', $validator->errors()->toArray());
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        // Cek apakah email sudah terdaftar di users
        $exists = User::where('email', $request->email)->where('users_id', '!=', $request->users_id)->exists();
        if ($exists) {
            \Log::debug('OTP Email Request - Email already registered by another user');
            return response()->json(['success' => false, 'message' => 'Email sudah terdaftar oleh akun lain.'], 409);
        }

        // Generate OTP 6 digit dan pastikan sebagai string
        $otpNumber = rand(100000, 999999);
        $otp_code = sprintf("%06d", $otpNumber);
        
        \Log::debug('Generated OTP Email:', ['otp' => $otp_code]);
        
        $expired_at = Carbon::now()->addMinutes(5);

        // Hapus OTP lama yang belum digunakan untuk email dan user yang sama
        OTPemail::where('users_id', $request->users_id)
            ->where('email', $request->email)
            ->where('is_used', false)
            ->delete();
            
        $otp = OTPemail::create([
            'users_id' => $request->users_id,
            'email' => $request->email,
            'otp_code' => $otp_code,
            'is_used' => false,
            'expired_at' => $expired_at
        ]);

        \Log::debug('OTP Email Created:', [
            'otp_id' => $otp->id,
            'users_id' => $otp->users_id,
            'email' => $otp->email,
            'otp_code' => $otp->otp_code
        ]);

        // Kirim email OTP pakai blade template
        $userData = User::where('users_id', $request->users_id)->first();
        $nama = $userData ? $userData->nama : '';
        $subject = 'Kode OTP Verifikasi Email';
        $expiredFormat = $expired_at->format('H:i, d M Y');
        
        try {

            Mail::send('emails.otp_email', [
                'otp_code' => $otp_code,
                'expired_at' => $expiredFormat,
                'nama' => $nama
            ], function ($message) use ($request, $subject) {
                $message->to($request->email)
                        ->subject($subject);
            });
            
            \Log::debug('OTP Email sent successfully');
            return response()->json([
                'success' => true, 
                'message' => 'OTP berhasil dikirim ke email.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error sending OTP Email:', ['error' => $e->getMessage()]);
            
            // Jika gagal kirim email, kirim response berhasil tapi tambahkan OTP untuk testing
            // CATATAN: Ini hanya untuk development, jangan gunakan di production
            return response()->json([
                'success' => true, 
                'message' => 'OTP berhasil dicatat, tapi gagal dikirim ke email (akan diperbaiki segera).',
                'otp_for_testing' => $otp_code // Untuk development saja
            ]);
        }
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
        \Log::debug('Reset Password OTP Request:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|exists:users,email'
        ]);
        if ($validator->fails()) {
            \Log::debug('Reset Password OTP Validation Failed:', $validator->errors()->toArray());
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            \Log::debug('Reset Password OTP - Email not found');
            return response()->json(['success' => false, 'message' => 'Email tidak terdaftar di sistem kami.'], 404);
        }
        $users_id = $user->users_id;
        $nama = $user->nama;

        // Generate OTP 6 digit dan pastikan sebagai string
        $otpNumber = rand(100000, 999999);
        $otp_code = sprintf("%06d", $otpNumber);
        
        \Log::debug('Generated Reset Password OTP:', ['otp' => $otp_code]);
        
        $expired_at = now()->addMinutes(5);

        // Hapus OTP lama yang belum digunakan untuk email dan user yang sama
        \App\Models\OTPemail::where('users_id', $users_id)
            ->where('email', $request->email)
            ->where('is_used', false)
            ->delete();
            
        $otp = \App\Models\OTPemail::create([
            'users_id' => $users_id,
            'email' => $request->email,
            'otp_code' => $otp_code,
            'is_used' => false,
            'expired_at' => $expired_at
        ]);
        
        \Log::debug('Reset Password OTP Created:', [
            'otp_id' => $otp->id,
            'users_id' => $otp->users_id,
            'email' => $otp->email, 
            'otp_code' => $otp->otp_code
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
        
        try {
            // Menggunakan konfigurasi email dari .env melalui config()
            \Mail::send('emails.otp_reset_password', [
                'otp_code' => $otp_code,
                'expired_at' => $expiredFormat,
                'nama' => $nama
            ], function ($message) use ($request, $subject) {
                $message->to($request->email)
                        ->subject($subject);
            });
            
            \Log::debug('Reset Password OTP Email sent successfully');
            return response()->json([
                'success' => true, 
                'message' => 'OTP reset password berhasil dikirim ke email.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error sending Reset Password OTP Email:', ['error' => $e->getMessage()]);
            
            // Jika gagal kirim email, kirim response berhasil tapi tambahkan OTP untuk testing
            // CATATAN: Ini hanya untuk development, jangan gunakan di production
            return response()->json([
                'success' => true, 
                'message' => 'OTP reset password berhasil dicatat, tapi gagal dikirim ke email (akan diperbaiki segera).',
                'otp_for_testing' => $otp_code // Untuk development saja
            ]);
        }
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