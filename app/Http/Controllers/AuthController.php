<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        $recaptcha_site_key = config('services.recaptcha.site_key');
        return view('auth.register', compact('recaptcha_site_key'));
    }

    public function register(Request $request)
    {
        \Log::debug('Register request data', $request->all());
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'no_hp' => 'required|string|max:20',
                'password' => 'required|string|min:6|confirmed',
                'gender' => 'required|in:laki-laki,perempuan',
                'alamat' => 'required|string',
                'rt_blok' => 'nullable|string',
                'agreement' => 'required',
                'g-recaptcha-response' => ['required'],
            ]);
            \Log::debug('Register validated data', $validated);

            // Verifikasi reCAPTCHA ke Google
            $response = \Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]);

            $result = $response->json();
            \Log::debug('reCAPTCHA response', $result);
            if (!($result['success'] ?? false)) {
                \Log::error('reCAPTCHA gagal', $result);
                return back()->withErrors(['captcha' => 'Verifikasi reCAPTCHA gagal.'])->withInput();
            }

            // Cek no_hp sudah terdaftar
            if (User::where('no_hp', $validated['no_hp'])->exists()) {
                \Log::error('Nomor HP sudah terdaftar', ['no_hp' => $validated['no_hp']]);
                return back()->withErrors(['no_hp' => 'Nomor HP sudah terdaftar.'])->withInput();
            }

            $validated['password'] = bcrypt($validated['password']);
            // Jika request tidak mengirim field role, set default ke 'RT'.
            $validated['role'] = $request->has('role') ? $request->input('role') : 'RT';

            // Generate 6 digit random unique users_id
            do {
                $randomId = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            } while (User::where('users_id', $randomId)->exists());
            $validated['users_id'] = $randomId;

            $user = User::create($validated);
            \Log::debug('User created', $user->toArray());

            if ($validated['role'] === 'Warga') {
                return redirect()->route('rt.DataWarga.akunwarga')->with('success', 'Akun warga berhasil ditambahkan.');
            } else {
                return redirect()->route('login')->with('success', 'Registrasi berhasil, silakan login.');
            }
        } catch (\Exception $e) {
            \Log::error('Register error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['register' => 'Terjadi kesalahan saat mendaftar: ' . $e->getMessage()])->withInput();
        }
    }

    public function showWelcome()
    {
        $token = bin2hex(random_bytes(16));
        session(['login_token' => $token]);
        return view('welcome', compact('token'));
    }

    public function showLoginForm(Request $request)
    {
        $token = $request->query('response_type');
        $sessionToken = session('login_token');

        if (!$token || $token !== $sessionToken) {
            return redirect()->route('welcome');
        }

        // Hapus token dari session agar tidak bisa dipakai ulang
        session()->forget('login_token');

        // Cek apakah cookie user_session masih valid
        $userSession = Cookie::get('user_session');
        if ($userSession) {
            $user = User::where('email', $userSession)->first();
            
            // Pastikan user ditemukan, role-nya RT, dan cookie belum kedaluwarsa
            if ($user && $user->role === 'RT' && $request->hasCookie('user_session')) {
                // Cek apakah user terakhir logout dengan benar
                if ($user->is_online) {
                    Auth::login($user);
                    return redirect()->route('rt.dashboard');
                }
            }
        }
        
        $recaptcha_site_key = config('services.recaptcha.site_key');
        return view('auth.login', compact('recaptcha_site_key'));
    }

    public function login(Request $request)
    {
        // Validasi input dan reCAPTCHA
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'g-recaptcha-response' => ['required'],
        ]);

        // Verifikasi reCAPTCHA ke Google
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        $result = $response->json();
        if (!($result['success'] ?? false)) {
            return back()->withErrors(['captcha' => 'Verifikasi reCAPTCHA gagal.'])->withInput();
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        // Cek apakah user ingin diingat
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Set user sebagai online
            $user = Auth::user();
            $user->setOnline();
            
            if ($user->role === 'RT') {
                // Set cookie user_session selama 5 hari
                return redirect()->route('rt.dashboard')
                    ->withCookie(cookie('user_session', $user->email, 7200));
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'Role Asing']);
            }
        }

        // Generate token baru untuk response_type agar bisa kembali ke halaman login
        $token = bin2hex(random_bytes(16));
        session(['login_token' => $token]);
        
        return redirect()->route('login', ['response_type' => $token])
            ->withErrors(['email' => 'Email atau password salah.'])
            ->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        // Set user sebagai offline sebelum logout
        $user = Auth::user();
        if ($user) {
            $user->setOffline();
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Hapus cookie user_session
        $cookie = Cookie::forget('user_session');
        
        return redirect()->route('welcome')->withCookie($cookie);
    }

    /**
     * Register a new satpam account.
     */
    public function registerSatpam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'no_hp' => 'required|string|max:15|unique:users',
            'gender' => 'required|in:laki-laki,perempuan',
            'alamat' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Check if maximum number of security guards (8) has been reached
        $satpamCount = User::where('role', 'Satpam')->count();
        if ($satpamCount >= 8) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah maksimal satpam (8 orang) telah tercapai. Hapus data satpam yang tidak aktif terlebih dahulu.'
            ], 422);
        }

        // Generate user ID
        $latestUser = User::orderBy('users_id', 'desc')->first();
        $newId = $latestUser ? (int)$latestUser->users_id + 1 : 1;
        $userId = str_pad($newId, 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'users_id' => $userId,
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'gender' => $request->gender,
            'alamat' => $request->alamat,
            'role' => 'Satpam',
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akun satpam berhasil dibuat',
            'data' => $user
        ]);
    }
}
