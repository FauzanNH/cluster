<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DataPenduduk;

class RtController extends Controller
{
    public function index()
    {
        return view('rt.dashboard');
    }
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (\Auth::user()->role != 'RT') {
                return redirect()->route('login');
            }
            return $next($request);
        });
    }
    public function datapenduduk()
    {
        $datawarga = DataPenduduk::all();
        $blok_rt_list = \App\Models\User::whereNotNull('rt_blok')->where('rt_blok', '!=', '')->distinct()->pluck('rt_blok');
        return view('rt.DataWarga.datapenduduk', compact('datawarga', 'blok_rt_list'));
    }
    public function datasatpam()
    {
        $satpams = \App\Models\User::where('role', 'Satpam')->get();
        return view('rt.DataSatpam.index', compact('satpams'));
    }
    public function datarumah()
    {
        return view('rt.DataWarga.datarumah');
    }
    public function suratpengantar()
    {
        return view('rt.DataWarga.suratpengantar');
    }
    public function akunwarga()
    {
        $warga = User::where('role', 'Warga')->get();
        return view('rt.DataWarga.akunwarga', compact('warga'));
    }
    public function pengaturan()
    {
        return view('rt.Pengaturan.index');
    }
}
