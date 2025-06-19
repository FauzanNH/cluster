<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controller imports
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RTController;
use App\Http\Controllers\AkunWargaController;
use App\Http\Controllers\DataSatpamController;
use App\Http\Controllers\DataPendudukController;
use App\Http\Controllers\DataRumahController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\SuratPengantarController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\JadwalKerjaSatpamController;
use App\Http\Controllers\ChatRtController;
use App\Http\Controllers\JumlahTamuController;

// API Controllers
use App\Http\Controllers\Api\PingApiController;
use App\Http\Controllers\Api\LoginApiController;
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
use App\Http\Controllers\Api\satpam\OcrApiController;
use App\Http\Controllers\Api\satpam\TamuApiController;
use App\Http\Controllers\Api\tamu\LoginApiControler;
use App\Http\Controllers\Api\TotalTamuHariIni;
use App\Http\Controllers\Api\tamu\ProfileApiController;
use App\Http\Controllers\Api\satpam\profile\ProfileApiController as SatpamProfileApiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================
// Landing Page
// =========================
Route::get('/', [AuthController::class, 'showWelcome'])->name('welcome');

/*
|--------------------------------------------------------------------------
| Auth Routes (Web)
|--------------------------------------------------------------------------
*/
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.tambahakun');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| RT Routes (Web)
|--------------------------------------------------------------------------
*/
// Dashboard & Data
Route::get('/rt/dashboard', [RTController::class, 'index'])->name('rt.dashboard');
Route::get('/rt/datapenduduk', [RTController::class, 'datapenduduk'])->name('rt.DataWarga.datapenduduk');
Route::get('/rt/datasatpam', [RTController::class, 'datasatpam'])->name('rt.DataSatpam.index');
Route::get('/rt/datarumah', [DataRumahController::class, 'index'])->name('rt.DataWarga.datarumah');
Route::get('/rt/suratpengantar', [SuratPengantarController::class, 'suratpengantar'])->name('rt.DataWarga.suratpengantar');
Route::get('/rt/akunwarga', [RTController::class, 'akunwarga'])->name('rt.DataWarga.akunwarga');
Route::get('/rt/datasatpam/{users_id}/detail', [DataSatpamController::class, 'showDetail'])->name('rt.DataSatpam.detail');
Route::get('/rt/datapenduduk/{warga_id}/edit', [DataPendudukController::class, 'edit'])->name('rt.DataWarga.datapenduduk.edit');
Route::get('/rt/akunwarga/{users_id}', [AkunWargaController::class, 'show'])->name('rt.DataWarga.akunwarga.show');
Route::get('/rt/keluhan/{id}', [PengaduanController::class, 'showDetail'])->name('rt.keluhan.detail');

// Jadwal Kerja Satpam Routes
Route::get('/rt/jadwalkerja', [JadwalKerjaSatpamController::class, 'index'])->name('rt.jadwalkerja.index');
Route::get('/rt/jadwalkerja/create', [JadwalKerjaSatpamController::class, 'create'])->name('rt.jadwalkerja.create');
Route::post('/rt/jadwalkerja/store', [JadwalKerjaSatpamController::class, 'store'])->name('rt.jadwalkerja.store');
Route::get('/rt/jadwalkerja/{id}/edit', [JadwalKerjaSatpamController::class, 'edit'])->name('rt.jadwalkerja.edit');
Route::put('/rt/jadwalkerja/{id}/update', [JadwalKerjaSatpamController::class, 'update'])->name('rt.jadwalkerja.update');
Route::delete('/rt/jadwalkerja/{id}/delete', [JadwalKerjaSatpamController::class, 'destroy'])->name('rt.jadwalkerja.delete');
Route::post('/rt/jadwalkerja/generate', [JadwalKerjaSatpamController::class, 'generateSchedule'])->name('rt.jadwalkerja.generate');
Route::post('/rt/jadwalkerja/reset', [JadwalKerjaSatpamController::class, 'resetSchedule'])->name('rt.jadwalkerja.reset');

// Tim Satpam Routes
Route::get('/rt/timsatpam', [JadwalKerjaSatpamController::class, 'indexTim'])->name('rt.timsatpam.index');
Route::get('/rt/timsatpam/create', [JadwalKerjaSatpamController::class, 'createTim'])->name('rt.timsatpam.create');
Route::post('/rt/timsatpam/store', [JadwalKerjaSatpamController::class, 'storeTim'])->name('rt.timsatpam.store');
Route::get('/rt/timsatpam/{id}/edit', [JadwalKerjaSatpamController::class, 'editTim'])->name('rt.timsatpam.edit');
Route::put('/rt/timsatpam/{id}/update', [JadwalKerjaSatpamController::class, 'updateTim'])->name('rt.timsatpam.update');
Route::delete('/rt/timsatpam/{id}/delete', [JadwalKerjaSatpamController::class, 'destroyTim'])->name('rt.timsatpam.delete');

// Store & Update
Route::post('/rt/datapenduduk', [DataPendudukController::class, 'store'])->name('rt.DataWarga.datapenduduk.store');
Route::post('/rt/datarumah', [DataRumahController::class, 'store'])->name('rt.DataWarga.datarumah.store');
Route::post('/rt/datasatpam/{users_id}/simpan', [DataSatpamController::class, 'storeOrUpdate'])->name('rt.DataSatpam.simpan');
Route::post('/rt/datasatpam/register', [AuthController::class, 'registerSatpam'])->name('rt.DataSatpam.register');
Route::post('/rt/pengaturan/update', [PengaturanController::class, 'update'])->name('rt.pengaturan.update');
Route::post('/rt/pengaturan/update-password', [PengaturanController::class, 'updatePassword'])->name('rt.pengaturan.updatePassword');
Route::post('/rt/suratpengantar/update-status', [SuratPengantarController::class, 'updateStatus'])->name('rt.suratpengantar.updateStatus');
Route::post('/rt/datapenduduk/{warga_id}/update', [DataPendudukController::class, 'update'])->name('rt.DataWarga.datapenduduk.update');
Route::post('/rt/akunwarga', [AkunWargaController::class, 'store'])->name('rt.DataWarga.akunwarga.store');

// Patch
Route::patch('/rt/akunwarga/{users_id}', [AkunWargaController::class, 'update'])->name('rt.DataWarga.akunwarga.update');
Route::patch('/rt/akunwarga/{users_id}/password', [AkunWargaController::class, 'updatePassword'])->name('rt.DataWarga.akunwarga.updatePassword');
Route::patch('/rt/datasatpam/{users_id}/password', [DataSatpamController::class, 'updatePasswordSatpam'])->name('rt.DataSatpam.password');
Route::patch('/rt/datarumah/{rumah_id}', [DataRumahController::class, 'update'])->name('rt.DataWarga.datarumah.update');

// Delete
Route::delete('/rt/akunwarga/{users_id}', [AkunWargaController::class, 'destroy'])->name('rt.DataWarga.akunwarga.destroy');
Route::delete('/rt/datasatpam/{users_id}/hapus', [DataSatpamController::class, 'destroySatpam'])->name('rt.DataSatpam.hapus');
Route::delete('/rt/datapenduduk/{warga_id}', [DataPendudukController::class, 'destroy'])->name('rt.DataWarga.datapenduduk.destroy');
Route::delete('/rt/datarumah/{rumah_id}', [DataRumahController::class, 'destroy'])->name('rt.DataWarga.datarumah.destroy');

// Pengaturan
Route::get('/rt/pengaturan', [RTController::class, 'pengaturan'])->name('rt.pengaturan');
Route::get('/rt/laporan/keluhan', [RTController::class, 'keluhan'])->name('rt.laporan.keluhan');
Route::get('/rt/laporan/aspirasi', [RTController::class, 'aspirasi'])->name('rt.laporan.aspirasi');
Route::get('/rt/laporan/tamu', [JumlahTamuController::class, 'index'])->name('rt.laporan.tamu');
Route::post('/rt/laporan/tamu/filter', [JumlahTamuController::class, 'filterByDate'])->name('rt.laporan.tamu.filter');

// Chat RT Routes
Route::middleware(['auth'])->prefix('rt/chat')->name('rt.chat.')->group(function () {
    Route::get('/', [ChatRtController::class, 'index'])->name('index');
    Route::get('/viewchat/{id}', [ChatRtController::class, 'viewChat'])->name('viewchat');
    Route::post('/{id}/send', [ChatRtController::class, 'sendMessage'])->name('send');
    Route::get('/{id}/new-messages', [ChatRtController::class, 'getNewMessages'])->name('new-messages');
    Route::post('/{id}/mark-read', [ChatRtController::class, 'markAsRead'])->name('mark-read');
    Route::delete('/{id}', [ChatRtController::class, 'deleteChat'])->name('delete');
    Route::post('/{id}/clear', [ChatRtController::class, 'clearChat'])->name('clear');
    Route::delete('/message/{id}', [ChatRtController::class, 'deleteMessage'])->name('delete-message');
    Route::get('/list', [ChatRtController::class, 'getChatList'])->name('list');
    Route::post('/create', [ChatRtController::class, 'createChat'])->name('create');
    Route::get('/available-users', [ChatRtController::class, 'getAvailableUsers'])->name('available-users');
    Route::get('/document/{id}', [ChatRtController::class, 'downloadDocument'])->name('download-document');
});

// User Status Routes
Route::middleware(['auth'])->prefix('rt/user')->name('rt.user.')->group(function () {
    Route::post('/status/online', [RTController::class, 'setUserOnline'])->name('status.online');
    Route::post('/status/offline', [RTController::class, 'setUserOffline'])->name('status.offline');
    Route::post('/status/away', [RTController::class, 'setUserAway'])->name('status.away');
    Route::get('/status/{userId}', [RTController::class, 'getUserStatus'])->name('status.get');
});

/*
|--------------------------------------------------------------------------
| API Routes (Mobile App)
|--------------------------------------------------------------------------
*/

// =========================
// Auth API
// =========================
Route::post('/api/login', [LoginApiController::class, 'login']);
Route::post('/api/refresh', [LoginApiController::class, 'refresh'])->middleware('auth:sanctum');
Route::post('/api/logout', [LoginApiController::class, 'logout'])->middleware('auth:sanctum');

// =========================
// Ping API
// =========================
Route::middleware('api')->prefix('api')->group(function () {
    Route::get('/ping', [PingApiController::class, 'ping']);
    Route::post('/cek-akun', [PingApiController::class, 'cekAkun']);
});

// =========================
// API Warga - Rumah & Anggota
// =========================
Route::get('/api/warga/{users_id}/rumah', [BerandaApiController::class, 'showUserRumah']);
Route::get('/api/rumah/{rumah_id}/anggota', [BerandaApiController::class, 'getTotalAnggotaRumah']);
Route::get('/api/rumah/{rumah_id}/info', [keluargaApiController::class, 'getRumahInfo']);

// =========================
// API Warga - Surat Pengajuan
// =========================
Route::post('/api/warga/suratpengajuan', [SuratPengajuanApiController::class, 'store']);
Route::get('/api/warga/daftarwarga', [SuratPengajuanApiController::class, 'getWarga']);
Route::get('/api/warga/anggota-rumah', [SuratPengajuanApiController::class, 'getAnggotaRumah']);
Route::get('/api/warga/surat-by-rumah', [SuratPengajuanApiController::class, 'getSuratByRumahId']);
Route::get('/api/warga/viewsurat/{surat_id}', [SuratPengajuanApiController::class, 'viewsurat']);
Route::get('/api/warga/surat-stats-by-rumah', [SuratPengajuanApiController::class, 'getSuratStatsByRumahId']);
Route::delete('/api/warga/suratpengajuan/{surat_id}', [SuratPengajuanApiController::class, 'destroy']);

// =========================
// API Warga - Profile
// =========================
Route::post('/api/warga/profile', [ProfileApiWargaController::class, 'getProfile']);

// =========================
// API Warga - Lainnya (User Info)
// =========================
Route::post('/api/warga/userinfo', [LainnyaApiController::class, 'userinfo']);

// =========================
// API Warga - Keamanan
// =========================
Route::post('/api/warga/keamanan/update-email', [KeamananApiController::class, 'updateEmail']);
Route::post('/api/warga/keamanan/get-current-email', [KeamananApiController::class, 'getCurrentEmail']);
Route::post('/api/warga/keamanan/update-password', [KeamananApiController::class, 'updatePassword']);
Route::post('/api/warga/keamanan/update-phone', [KeamananApiController::class, 'updatePhone']);
Route::post('/api/warga/keamanan/get-current-phone', [KeamananApiController::class, 'getCurrentPhone']);
Route::post('/api/warga/keamanan/check-pin', [GuardApiController::class, 'checkPinStatus']);

// =========================
// API Warga - OTP
// =========================
Route::post('/api/warga/otp/request', [OTPController::class, 'requestOTP']);
Route::post('/api/warga/otp/verify', [OTPController::class, 'verifyOTP']);

// =========================
// API Warga - OTP Email
// =========================
Route::post('/api/warga/otp-email/request', [OTPEmailController::class, 'requestOTPEmail']);
Route::post('/api/warga/otp-email/verify', [OTPEmailController::class, 'verifyOTPEmail']);
// OTP untuk reset password
Route::post('/api/warga/otp-email/request-reset-password', [OTPEmailController::class, 'requestResetPasswordOTP']);
Route::post('/api/warga/otp-email/verify-reset-password', [OTPEmailController::class, 'verifyResetPasswordOTP']);

// =========================
// API Warga - Pengaduan
// =========================
Route::post('/api/warga/pengaduan/store', [PengaduanApiController::class, 'store']);
Route::get('/api/warga/pengaduan/list', [PengaduanApiController::class, 'list']);
Route::get('/api/warga/pengaduan/detail', [PengaduanApiController::class, 'detail']);
Route::delete('/api/warga/pengaduan/{pengaduan_id}', [PengaduanApiController::class, 'destroy']);

// =========================
// API Satpam - Jadwal Kerja
// =========================
Route::get('/api/satpam/jadwalkerja', [JadwalKerjaSatpamApiController::class, 'getJadwalBySatpam']);
Route::get('/api/satpam/jadwalkerja/bulan/{bulan}/{tahun}', [JadwalKerjaSatpamApiController::class, 'getJadwalByBulan']);
Route::get('/api/satpam/jadwalkerja/hari/{tanggal}', [JadwalKerjaSatpamApiController::class, 'getJadwalByTanggal']);
Route::get('/api/satpam/jadwalkerja/tim/{tim_id}', [JadwalKerjaSatpamApiController::class, 'getAnggotaTim']);

// =========================
// API User (Sanctum Protected)
// =========================
Route::middleware('auth:sanctum')->get('/api/user', function (Request $request) {
    return $request->user();
});

// =========================
// RT Routes (Web) - Mark keluhan as read
// =========================
Route::post('/rt/keluhan/{id}/mark-as-read', [PengaduanController::class, 'markAsRead'])->name('rt.keluhan.markAsRead');



// =========================
// API Warga - Keamanan PIN
// =========================
Route::post('/api/warga/keamanan/set-pin', [GuardApiController::class, 'setPin']);
Route::post('/api/warga/keamanan/delete-pin', [GuardApiController::class, 'deletePin']);
Route::post('/api/warga/keamanan/verify-pin', [GuardApiController::class, 'verifyPin']);
Route::post('/api/warga/keamanan/update-pin', [GuardApiController::class, 'updatePin']);

// =========================
// API Warga - Keamanan Security
// =========================
Route::post('/api/warga/keamanan/set-pin-security', [GuardApiController::class, 'setPinSecurity']);
Route::post('/api/warga/keamanan/get-pin-security', [GuardApiController::class, 'getPinSecurityStatus']);

// Verifikasi hint dan reset PIN
Route::post('/api/warga/keamanan/verify-hint', [GuardApiController::class, 'verifyHint']);
Route::post('/api/warga/keamanan/reset-pin', [GuardApiController::class, 'resetPin']);
Route::post('/api/warga/keamanan/get-hint', [GuardApiController::class, 'getHint']);
Route::post('/api/warga/keamanan/update-hint', [GuardApiController::class, 'updateHint']);

// =========================
// API Satpam - Beranda
// =========================
Route::post('/api/satpam/beranda/info', [\App\Http\Controllers\Api\satpam\BerandaApiController::class, 'getSatpamInfo']);
Route::post('/api/satpam/beranda/activities', [\App\Http\Controllers\Api\satpam\BerandaApiController::class, 'getRecentActivities']);

// =========================
// API Satpam - Kunjungan Tamu
// =========================
Route::get('/api/satpam/kunjungan/list', [\App\Http\Controllers\Api\satpam\beranda\KunjunganTamuApiController::class, 'getKunjunganList']);
Route::get('/api/satpam/kunjungan/{kunjungan_id}', [\App\Http\Controllers\Api\satpam\beranda\KunjunganTamuApiController::class, 'getKunjunganById']);
Route::post('/api/satpam/kunjungan/tamu-masuk', [\App\Http\Controllers\Api\satpam\beranda\KunjunganTamuApiController::class, 'tamuMasuk']);
Route::post('/api/satpam/kunjungan/tamu-keluar', [\App\Http\Controllers\Api\satpam\beranda\KunjunganTamuApiController::class, 'tamuKeluar']);

// =========================
// API Satpam - Laporan Tamu
// =========================
Route::get('/api/satpam/laporan/harian', [\App\Http\Controllers\Api\satpam\beranda\LaporanHarianApiController::class, 'getKunjunganHariIni']);
Route::get('/api/satpam/laporan/bulanan', [\App\Http\Controllers\Api\satpam\beranda\LaporanHarianApiController::class, 'getKunjunganBulanIni']);
Route::get('/api/satpam/laporan/by-tanggal', [\App\Http\Controllers\Api\satpam\beranda\LaporanHarianApiController::class, 'getKunjunganByTanggal']);
Route::get('/api/satpam/laporan/statistik', [\App\Http\Controllers\Api\satpam\beranda\LaporanHarianApiController::class, 'getStatistikKunjungan']);

// =========================
// API Satpam - Tamu
// =========================
Route::post('/api/satpam/tamu/store', [TamuApiController::class, 'store']);
Route::get('/api/satpam/tamu/{tamu_id}', [TamuApiController::class, 'getTamuById']);

// =========================
// API Satpam - OCR
// =========================
Route::post('/api/satpam/ocr/process-ktp', [OcrApiController::class, 'processKtp']);

// =========================
// API Tamu - Login
// =========================
Route::post('/api/tamu/login', [LoginApiControler::class, 'loginTamu']);

// =========================
// API Tamu - Kunjungan
// =========================
Route::post('/api/tamu/kunjungan/store', [\App\Http\Controllers\Api\tamu\KunjunganApiController::class, 'store']);
Route::post('/api/tamu/kunjungan/list', [\App\Http\Controllers\Api\tamu\KunjunganApiController::class, 'getKunjunganByTamu']);
Route::get('/api/tamu/kunjungan/{kunjungan_id}', [\App\Http\Controllers\Api\tamu\KunjunganApiController::class, 'getKunjunganById']);
Route::post('/api/tamu/kunjungan/search-rumah', [\App\Http\Controllers\Api\tamu\KunjunganApiController::class, 'searchRumah']);

// =========================
// API Tamu - Dashboard Info
// =========================
Route::post('/api/tamu/dashboard/info', [\App\Http\Controllers\Api\tamu\DashboardApi::class, 'getTamuInfo']);

// =========================
// API Tamu - Profile
// =========================
Route::post('/api/tamu/profile', [ProfileApiController::class, 'getProfile']);

// =========================
// API Aktivitas
// =========================
Route::get('/api/aktivitas/by-user/{users_id}', [\App\Http\Controllers\Api\AktivitasApiController::class, 'getByUserId']);
Route::get('/api/aktivitas/by-tamu/{tamu_id}', [\App\Http\Controllers\Api\AktivitasApiController::class, 'getByTamuId']);
Route::post('/api/aktivitas/dashboard', [\App\Http\Controllers\Api\AktivitasApiController::class, 'getActivityDashboard']);
Route::get('/api/aktivitas/{users_id}', [\App\Http\Controllers\Api\AktivitasApiController::class, 'getByUserId']);

// =========================
// API Total Tamu Hari Ini
// =========================
Route::get('/api/rumah/{rumah_id}/tamu/today', [TotalTamuHariIni::class, 'getTotalTamuHariIni']);
Route::get('/api/tamu/today', [TotalTamuHariIni::class, 'getTotalTamuHariIni']);
Route::get('/api/tamu/active', [TotalTamuHariIni::class, 'getTamuSedangBerkunjung']);
Route::get('/api/tamu/ongoing', [TotalTamuHariIni::class, 'getTamuOngoing']);

// =========================
// API Satpam - Profile
// =========================
Route::post('/api/satpam/profile', [SatpamProfileApiController::class, 'getProfile']);

// =========================
// API Satpam - Keamanan
// =========================
Route::post('/api/satpam/keamanan/update-email', [\App\Http\Controllers\Api\satpam\keamanan\KeamananApiController::class, 'updateEmail']);
Route::post('/api/satpam/keamanan/get-current-email', [\App\Http\Controllers\Api\satpam\keamanan\KeamananApiController::class, 'getCurrentEmail']);
Route::post('/api/satpam/keamanan/update-password', [\App\Http\Controllers\Api\satpam\keamanan\KeamananApiController::class, 'updatePassword']);
Route::post('/api/satpam/keamanan/update-phone', [\App\Http\Controllers\Api\satpam\keamanan\KeamananApiController::class, 'updatePhone']);
Route::post('/api/satpam/keamanan/get-current-phone', [\App\Http\Controllers\Api\satpam\keamanan\KeamananApiController::class, 'getCurrentPhone']);
Route::post('/api/satpam/keamanan/get-pin-security', [\App\Http\Controllers\Api\satpam\keamanan\KeamananApiController::class, 'getPinSecurity']);

// =========================
// API Satpam - OTP
// =========================
Route::post('/api/satpam/otp/request', [\App\Http\Controllers\Api\satpam\OTPController::class, 'requestOTP']);
Route::post('/api/satpam/otp/verify', [\App\Http\Controllers\Api\satpam\OTPController::class, 'verifyOTP']);

// =========================
// API Satpam - OTP Email
// =========================
Route::post('/api/satpam/otp-email/request', [\App\Http\Controllers\Api\satpam\OTPEmailController::class, 'requestOTPEmail']);
Route::post('/api/satpam/otp-email/verify', [\App\Http\Controllers\Api\satpam\OTPEmailController::class, 'verifyOTPEmail']);
Route::post('/api/satpam/otp-email/reset-password/request', [\App\Http\Controllers\Api\satpam\OTPEmailController::class, 'requestResetPasswordOTP']);
Route::post('/api/satpam/otp-email/reset-password/verify', [\App\Http\Controllers\Api\satpam\OTPEmailController::class, 'verifyResetPasswordOTP']);


