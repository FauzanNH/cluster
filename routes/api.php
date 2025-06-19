<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API Controllers
use App\Http\Controllers\Api\PingApiController;
use App\Http\Controllers\Api\LoginApiController;
use App\Http\Controllers\Api\AktivitasApiController;
use App\Http\Controllers\Api\warga\BerandaApiController;
use App\Http\Controllers\Api\warga\SuratPengajuanApiController;
use App\Http\Controllers\Api\warga\ProfileApiWargaController;
use App\Http\Controllers\Api\warga\LainnyaApiController;
use App\Http\Controllers\Api\warga\lainnya\keluargaApiController;
use App\Http\Controllers\Api\warga\keamanan\KeamananApiController;
use App\Http\Controllers\Api\warga\OTPController;
use App\Http\Controllers\Api\warga\OTPEmailController;
use App\Http\Controllers\Api\warga\beranda\PengaduanApiController;
use App\Http\Controllers\Api\warga\keamanan\GuardApiController;
use App\Http\Controllers\Api\satpam\JadwalKerjaSatpamApiController;
use App\Http\Controllers\Api\satpam\BerandaApiController as SatpamBerandaApiController;
use App\Http\Controllers\Api\satpam\TamuApiController;
use App\Http\Controllers\Api\satpam\OcrApiController;
use App\Http\Controllers\Api\warga\ChatWargaController;
use App\Http\Controllers\Api\satpam\ChatSatpamController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// =========================
// Auth API
// =========================
Route::post('/login', [LoginApiController::class, 'login']);
Route::post('/refresh', [LoginApiController::class, 'refresh'])->middleware('auth:sanctum');
Route::post('/logout', [LoginApiController::class, 'logout'])->middleware('auth:sanctum');

// =========================
// Ping API
// =========================
Route::get('/ping', [PingApiController::class, 'ping']);
Route::post('/cek-akun', [PingApiController::class, 'cekAkun']);

// =========================
// API Warga - Rumah & Anggota
// =========================
Route::get('/warga/{users_id}/rumah', [BerandaApiController::class, 'showUserRumah']);
Route::get('/rumah/{rumah_id}/anggota', [BerandaApiController::class, 'getTotalAnggotaRumah']);
Route::get('/rumah/{rumah_id}/info', [keluargaApiController::class, 'getRumahInfo']);

// =========================
// API Warga - Surat Pengajuan
// =========================
Route::post('/warga/suratpengajuan', [SuratPengajuanApiController::class, 'store']);
Route::get('/warga/daftarwarga', [SuratPengajuanApiController::class, 'getWarga']);
Route::get('/warga/anggota-rumah', [SuratPengajuanApiController::class, 'getAnggotaRumah']);
Route::get('/warga/surat-by-rumah', [SuratPengajuanApiController::class, 'getSuratByRumahId']);
Route::get('/warga/viewsurat/{surat_id}', [SuratPengajuanApiController::class, 'viewsurat']);
Route::get('/warga/surat-stats-by-rumah', [SuratPengajuanApiController::class, 'getSuratStatsByRumahId']);
Route::delete('/warga/suratpengajuan/{surat_id}', [SuratPengajuanApiController::class, 'destroy']);

// =========================
// API Warga - Profile
// =========================
Route::post('/warga/profile', [ProfileApiWargaController::class, 'getProfile']);

// =========================
// API Warga - Lainnya (User Info)
// =========================
Route::post('/warga/userinfo', [LainnyaApiController::class, 'userinfo']);

// =========================
// API Warga - Keamanan
// =========================
Route::post('/warga/keamanan/update-email', [KeamananApiController::class, 'updateEmail']);
Route::post('/warga/keamanan/get-current-email', [KeamananApiController::class, 'getCurrentEmail']);
Route::post('/warga/keamanan/update-password', [KeamananApiController::class, 'updatePassword']);
Route::post('/warga/keamanan/update-phone', [KeamananApiController::class, 'updatePhone']);
Route::post('/warga/keamanan/get-current-phone', [KeamananApiController::class, 'getCurrentPhone']);
Route::post('/warga/keamanan/check-pin', [GuardApiController::class, 'checkPinStatus']);

// =========================
// API Warga - OTP
// =========================
Route::post('/warga/otp/request', [OTPController::class, 'requestOTP']);
Route::post('/warga/otp/verify', [OTPController::class, 'verifyOTP']);

// =========================
// API Warga - OTP Email
// =========================
Route::post('/warga/otp-email/request', [OTPEmailController::class, 'requestOTPEmail']);
Route::post('/warga/otp-email/verify', [OTPEmailController::class, 'verifyOTPEmail']);
// OTP untuk reset password
Route::post('/warga/otp-email/request-reset-password', [OTPEmailController::class, 'requestResetPasswordOTP']);
Route::post('/warga/otp-email/verify-reset-password', [OTPEmailController::class, 'verifyResetPasswordOTP']);

// =========================
// API Warga - Pengaduan
// =========================
Route::post('/warga/pengaduan/store', [PengaduanApiController::class, 'store']);
Route::get('/warga/pengaduan/list', [PengaduanApiController::class, 'list']);
Route::get('/warga/pengaduan/detail', [PengaduanApiController::class, 'detail']);
Route::delete('/warga/pengaduan/{pengaduan_id}', [PengaduanApiController::class, 'destroy']);

// =========================
// API Satpam - Jadwal Kerja
// =========================
Route::get('/satpam/jadwalkerja', [JadwalKerjaSatpamApiController::class, 'getJadwalBySatpam']);
Route::get('/satpam/jadwalkerja/bulan/{bulan}/{tahun}', [JadwalKerjaSatpamApiController::class, 'getJadwalByBulan']);
Route::get('/satpam/jadwalkerja/hari/{tanggal}', [JadwalKerjaSatpamApiController::class, 'getJadwalByTanggal']);
Route::get('/satpam/jadwalkerja/tim/{tim_id}', [JadwalKerjaSatpamApiController::class, 'getAnggotaTim']);

// =========================
// API Satpam - Beranda
// =========================
Route::post('/satpam/beranda/info', [SatpamBerandaApiController::class, 'getSatpamInfo']);

// =========================
// API Satpam - Tamu
// =========================
Route::post('/satpam/tamu/store', [TamuApiController::class, 'store']);
Route::get('/satpam/tamu/{tamu_id}', [TamuApiController::class, 'getTamuById']);

// =========================
// API Satpam - OCR
// =========================
Route::post('/satpam/ocr/process-ktp', [OcrApiController::class, 'processKtp']);

// =========================
// API Aktivitas
// =========================
Route::get('/aktivitas/{users_id}', [AktivitasApiController::class, 'getByUserId']);
Route::get('/aktivitas/tamu/{tamu_id}', [AktivitasApiController::class, 'getByTamuId']);
Route::post('/aktivitas/dashboard', [AktivitasApiController::class, 'getActivityDashboard']);

// =========================
// API Warga - Keamanan PIN
// =========================
Route::post('/warga/keamanan/set-pin', [GuardApiController::class, 'setPin']);
Route::post('/warga/keamanan/delete-pin', [GuardApiController::class, 'deletePin']);
Route::post('/warga/keamanan/verify-pin', [GuardApiController::class, 'verifyPin']);
Route::post('/warga/keamanan/update-pin', [GuardApiController::class, 'updatePin']);

// =========================
// API Warga - Keamanan Security
// =========================
Route::post('/warga/keamanan/set-pin-security', [GuardApiController::class, 'setPinSecurity']);
Route::post('/warga/keamanan/get-pin-security', [GuardApiController::class, 'getPinSecurityStatus']);

// Verifikasi hint dan reset PIN
Route::post('/warga/keamanan/verify-hint', [GuardApiController::class, 'verifyHint']);
Route::post('/warga/keamanan/reset-pin', [GuardApiController::class, 'resetPin']);
Route::post('/warga/keamanan/get-hint', [GuardApiController::class, 'getHint']);
Route::post('/warga/keamanan/update-hint', [GuardApiController::class, 'updateHint']);

// =========================
// API User (Sanctum Protected)
// =========================
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API OCR KTP dan Tamu dengan middleware cors
Route::middleware(['cors'])->group(function () {
    // OCR KTP
    Route::post('/satpam/ocr/process-ktp', [OcrApiController::class, 'processKtp']);
    
    // Tamu
    Route::post('/satpam/tamu/store', [TamuApiController::class, 'store']);
    Route::get('/satpam/tamu/{tamu_id}', [TamuApiController::class, 'getTamuById']);
});

// Chat API untuk Warga
Route::middleware('auth:sanctum')->prefix('warga/chat')->group(function () {
    Route::post('/list', [ChatWargaController::class, 'getChatList']);
    Route::post('/detail', [ChatWargaController::class, 'getChatDetail']);
    Route::post('/send', [ChatWargaController::class, 'sendMessage']);
    Route::post('/new-messages', [ChatWargaController::class, 'getNewMessages']);
    Route::post('/create', [ChatWargaController::class, 'createChat']);
    Route::post('/contacts', [ChatWargaController::class, 'getContacts']);
    Route::post('/mark-read', [ChatWargaController::class, 'markAsRead']);
    Route::post('/delete-message', [ChatWargaController::class, 'deleteMessage']);
    Route::post('/delete-chat', [ChatWargaController::class, 'deleteChat']);
    Route::post('/clear-chat', [ChatWargaController::class, 'clearChat']);
    Route::get('/download-document/{id}', [ChatWargaController::class, 'downloadDocument'])->name('api.warga.chat.download-document');
});

// Chat API untuk Satpam
Route::middleware('auth:sanctum')->prefix('satpam/chat')->group(function () {
    Route::post('/list', [ChatSatpamController::class, 'getChatList']);
    Route::post('/detail', [ChatSatpamController::class, 'getChatDetail']);
    Route::post('/send', [ChatSatpamController::class, 'sendMessage']);
    Route::post('/new-messages', [ChatSatpamController::class, 'getNewMessages']);
    Route::post('/create', [ChatSatpamController::class, 'createChat']);
    Route::post('/contacts', [ChatSatpamController::class, 'getContacts']);
    Route::post('/mark-read', [ChatSatpamController::class, 'markAsRead']);
    Route::post('/delete-message', [ChatSatpamController::class, 'deleteMessage']);
    Route::post('/delete-chat', [ChatSatpamController::class, 'deleteChat']);
    Route::post('/clear-chat', [ChatSatpamController::class, 'clearChat']);
    Route::get('/download-document/{id}', [ChatSatpamController::class, 'downloadDocument'])->name('api.satpam.chat.download-document');
}); 