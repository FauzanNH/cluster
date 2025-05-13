<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'required|in:laki-laki,perempuan',
            'alamat' => 'required|string',
            'rt_blok' => 'nullable|string',
            'agreement' => 'required',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = $request->has('role') ? $request->input('role') : 'RT';

        // Generate 6 digit random unique users_id
        do {
            $randomId = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (User::where('users_id', $randomId)->exists());
        $validated['users_id'] = $randomId;

        User::create($validated);

        if ($validated['role'] === 'Warga') {
            return redirect()->route('rt.DataWarga.akunwarga')->with('success', 'Akun warga berhasil ditambahkan.');
        } else {
            return redirect()->route('login')->with('success', 'Registrasi berhasil, silakan login.');
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (\Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = \Auth::user();
            if ($user->role === 'RT') {
                return redirect()->route('rt.dashboard');
            } else {
                \Auth::logout();
                return back()->withErrors(['email' => 'Role Asing']);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        \Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function registerSatpam(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'required|in:laki-laki,perempuan',
            'alamat' => 'required|string',
        ]);
        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = 'Satpam';
        // Generate 6 digit random unique users_id
        do {
            $randomId = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (\App\Models\User::where('users_id', $randomId)->exists());
        $validated['users_id'] = $randomId;
        $user = \App\Models\User::create($validated);
        return response()->json(['success' => true, 'user' => $user]);
    }
}
