<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controller imports
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RTController;
use App\Http\Controllers\AkunWargaController;
use App\Http\Controllers\Api\PingApiController;
use App\Http\Controllers\Api\LoginApiController;
use App\Http\Controllers\DataSatpamController;
use App\Http\Controllers\DataPendudukController;
use App\Http\Controllers\DataRumahController;
use App\Http\Controllers\Api\warga\BerandaApiController;
use App\Http\Controllers\PengaturanController;

// =========================
// Web Routes
// =========================

// Home
Route::get('/', function () {
    return view('welcome');
});

// -------------------------
// Auth Routes
// -------------------------
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.tambahakun');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// -------------------------
// RT Routes
// -------------------------
Route::get('/rt/dashboard', [RTController::class, 'index'])->name('rt.dashboard');
Route::get('/rt/datapenduduk', [RTController::class, 'datapenduduk'])->name('rt.DataWarga.datapenduduk');
Route::post('/rt/datapenduduk', [DataPendudukController::class, 'store'])->name('rt.DataWarga.datapenduduk.store');
Route::get('/rt/datasatpam', [RTController::class, 'datasatpam'])->name('rt.DataSatpam.index');
Route::get('/rt/datarumah', [DataRumahController::class, 'index'])->name('rt.DataWarga.datarumah');
Route::post('/rt/datarumah', [DataRumahController::class, 'store'])->name('rt.DataWarga.datarumah.store');
Route::get('/rt/suratpengantar', [RTController::class, 'suratpengantar'])->name('rt.DataWarga.suratpengantar');
Route::get('/rt/akunwarga', [RTController::class, 'akunwarga'])->name('rt.DataWarga.akunwarga');
Route::patch('/rt/akunwarga/{users_id}', [AkunWargaController::class, 'update'])->name('rt.DataWarga.akunwarga.update');
Route::get('/rt/akunwarga/{users_id}', [AkunWargaController::class, 'show'])->name('rt.DataWarga.akunwarga.show');
Route::patch('/rt/akunwarga/{users_id}/password', [AkunWargaController::class, 'updatePassword'])->name('rt.DataWarga.akunwarga.updatePassword');
Route::delete('/rt/akunwarga/{users_id}', [AkunWargaController::class, 'destroy'])->name('rt.DataWarga.akunwarga.destroy');
Route::get('/rt/datasatpam/{users_id}/detail', [DataSatpamController::class, 'showDetail'])->name('rt.DataSatpam.detail');
Route::post('/rt/datasatpam/{users_id}/simpan', [DataSatpamController::class, 'storeOrUpdate'])->name('rt.DataSatpam.simpan');
Route::delete('/rt/datasatpam/{users_id}/hapus', [DataSatpamController::class, 'destroySatpam'])->name('rt.DataSatpam.hapus');
Route::patch('/rt/datasatpam/{users_id}/password', [DataSatpamController::class, 'updatePasswordSatpam'])->name('rt.DataSatpam.password');
Route::post('/rt/datasatpam/register', [AuthController::class, 'registerSatpam'])->name('rt.DataSatpam.register');
Route::get('/rt/datapenduduk/{warga_id}/edit', [DataPendudukController::class, 'edit'])->name('rt.DataWarga.datapenduduk.edit');
Route::post('/rt/datapenduduk/{warga_id}/update', [DataPendudukController::class, 'update'])->name('rt.DataWarga.datapenduduk.update');
Route::delete('/rt/datapenduduk/{warga_id}', [DataPendudukController::class, 'destroy'])->name('rt.DataWarga.datapenduduk.destroy');
Route::patch('/rt/datarumah/{rumah_id}', [DataRumahController::class, 'update'])->name('rt.DataWarga.datarumah.update');
Route::delete('/rt/datarumah/{rumah_id}', [DataRumahController::class, 'destroy'])->name('rt.DataWarga.datarumah.destroy');
Route::get('/rt/pengaturan', [RTController::class, 'pengaturan'])->name('rt.pengaturan');
Route::post('/rt/pengaturan/update', [PengaturanController::class, 'update'])->name('rt.pengaturan.update');
Route::post('/rt/pengaturan/update-password', [PengaturanController::class, 'updatePassword'])->name('rt.pengaturan.updatePassword');

// =========================
// API Routes
// =========================

// Auth API
Route::post('/api/login', [LoginApiController::class, 'login']);

// Ping API
Route::middleware('api')->prefix('api')->group(function () {
    Route::get('/ping', [PingApiController::class, 'ping']);
    Route::post('/cek-akun', [PingApiController::class, 'cekAkun']);
});

Route::post('/api/warga/beranda/user-rumah', [BerandaApiController::class, 'getUserAndRumah']);

Route::middleware('auth:sanctum')->get('/api/user', function (Request $request) {
    return $request->user();
});
