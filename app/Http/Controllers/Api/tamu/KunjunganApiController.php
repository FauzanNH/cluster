<?php

namespace App\Http\Controllers\Api\tamu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kunjungan;
use App\Models\DataRumah;
use App\Models\Tamu;
use App\Models\Aktivitas;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class KunjunganApiController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'tamu_id' => 'required|string|exists:tamu,tamu_id',
            'rumah_id' => 'required|string|exists:datarumah,rumah_id',
            'tujuan_kunjungan' => 'required|string',
        ]);

        try {
            // Generate kunjungan_id dengan 8 digit kombinasi huruf dan angka
            $kunjungan_id = 'KJG-' . strtoupper(Str::random(8));
            
            // Cek jika ID sudah ada, generate ulang
            while (Kunjungan::where('kunjungan_id', $kunjungan_id)->exists()) {
                $kunjungan_id = 'KJG-' . strtoupper(Str::random(8));
            }

            // Simpan data kunjungan
            $kunjungan = Kunjungan::create([
                'kunjungan_id' => $kunjungan_id,
                'tamu_id' => $request->tamu_id,
                'rumah_id' => $request->rumah_id,
                'tujuan_kunjungan' => $request->tujuan_kunjungan,
                'status_kunjungan' => 'Menunggu Menuju Cluster',
                'waktu_masuk' => null,
                'waktu_keluar' => null,
            ]);

            // Simpan log aktivitas
            $this->simpanAktivitas($kunjungan);

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan berhasil ditambahkan',
                'data' => $kunjungan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKunjunganByTamu(Request $request)
    {
        $request->validate([
            'tamu_id' => 'required|string',
        ]);

        try {
            // Cek apakah tamu_id ada di database
            $tamu = Tamu::where('tamu_id', $request->tamu_id)->first();
            
            if (!$tamu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamu tidak ditemukan'
                ], 404);
            }

            // Ambil data kunjungan dengan relasi ke rumah
            $kunjungan = Kunjungan::where('tamu_id', $request->tamu_id)
                ->with(['rumah' => function($query) {
                    $query->select('rumah_id', 'blok_rt', 'alamat_cluster');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            // Format tanggal dan waktu dengan ISO string untuk diproses di client dengan zona waktu lokal
            $formattedKunjungan = $kunjungan->map(function($item) {
                $data = $item->toArray();
                
                // Konversi waktu_masuk ke format ISO 8601 jika ada
                if ($item->waktu_masuk) {
                    $data['waktu_masuk'] = Carbon::parse($item->waktu_masuk)->toIso8601String();
                }
                
                // Konversi waktu_keluar ke format ISO 8601 jika ada
                if ($item->waktu_keluar) {
                    $data['waktu_keluar'] = Carbon::parse($item->waktu_keluar)->toIso8601String();
                }
                
                // Konversi created_at dan updated_at ke format ISO 8601
                $data['created_at'] = Carbon::parse($item->created_at)->toIso8601String();
                $data['updated_at'] = Carbon::parse($item->updated_at)->toIso8601String();
                
                return $data;
            });

            return response()->json([
                'success' => true,
                'data' => $formattedKunjungan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKunjunganById($kunjungan_id)
    {
        try {
            $kunjungan = Kunjungan::where('kunjungan_id', $kunjungan_id)->first();

            if (!$kunjungan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kunjungan tidak ditemukan'
                ], 404);
            }

            // Ambil data tamu
            $tamu = Tamu::where('tamu_id', $kunjungan->tamu_id)->first();
            
            // Ambil detail tamu dari tabel detail_tamu
            $detailTamu = \App\Models\DetailTamu::where('tamu_id', $kunjungan->tamu_id)->first();
            
            // Ambil data rumah
            $rumah = DataRumah::where('rumah_id', $kunjungan->rumah_id)->first();
            
            // Ambil data pemilik rumah
            $pemilikRumah = null;
            if ($rumah && $rumah->users_id) {
                $pemilikRumah = User::where('users_id', $rumah->users_id)
                    ->select('users_id', 'nama', 'no_hp', 'email')
                    ->first();
            }

            // Gabungkan data
            $result = $kunjungan->toArray();
            
            // Format tanggal dan waktu dengan ISO string
            if ($kunjungan->waktu_masuk) {
                $result['waktu_masuk'] = Carbon::parse($kunjungan->waktu_masuk)->toIso8601String();
            }
            
            if ($kunjungan->waktu_keluar) {
                $result['waktu_keluar'] = Carbon::parse($kunjungan->waktu_keluar)->toIso8601String();
            }
            
            $result['created_at'] = Carbon::parse($kunjungan->created_at)->toIso8601String();
            $result['updated_at'] = Carbon::parse($kunjungan->updated_at)->toIso8601String();
            
            // Set data tamu
            if ($tamu) {
                $tamuData = $tamu->toArray();
                // Tambahkan data detail tamu jika ada
                if ($detailTamu) {
                    $tamuData['nama'] = $detailTamu->nama;
                    $tamuData['nik'] = $detailTamu->nik;
                }
                $result['tamu'] = $tamuData;
            }
            
            // Set data rumah
            if ($rumah) {
                $rumahData = $rumah->toArray();
                // Tambahkan data pemilik rumah jika ada
                if ($pemilikRumah) {
                    $rumahData['pemilik'] = $pemilikRumah;
                }
                $result['rumah'] = $rumahData;
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchRumah(Request $request)
    {
        $search = $request->input('search');
        if (!$search) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter pencarian diperlukan'
            ], 400);
        }

        try {
            // Cari rumah berdasarkan rumah_id atau nama pemilik
            $rumah = DataRumah::query()
                ->where('rumah_id', 'like', "%{$search}%")
                ->orWhereHas('kepalaKeluarga', function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%");
                })
                ->with(['kepalaKeluarga' => function($query) {
                    $query->select('users_id', 'nama', 'no_hp');
                }])
                ->limit(10)
                ->get(['rumah_id', 'users_id', 'alamat_cluster', 'blok_rt']);

            // Format response untuk memudahkan akses di frontend
            $formattedData = $rumah->map(function($item) {
                return [
                    'rumah_id' => $item->rumah_id,
                    'users_id' => $item->users_id,
                    'nama' => $item->kepalaKeluarga ? $item->kepalaKeluarga->nama : 'Tidak diketahui',
                    'alamat' => $item->alamat_cluster,
                    'blok_rt' => $item->blok_rt,
                    'no_hp' => $item->kepalaKeluarga ? $item->kepalaKeluarga->no_hp : '-'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari data rumah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan log aktivitas dari kunjungan
     *
     * @param Kunjungan $kunjungan
     * @return void
     */
    private function simpanAktivitas(Kunjungan $kunjungan)
    {
        try {
            // Generate aktivitas_id
            $aktivitas_id = 'AKT-' . strtoupper(Str::random(8));
            
            // Cek jika ID sudah ada, generate ulang
            while (Aktivitas::where('aktivitas_id', $aktivitas_id)->exists()) {
                $aktivitas_id = 'AKT-' . strtoupper(Str::random(8));
            }
            
            // Ambil data tamu untuk informasi tambahan
            $tamu = Tamu::where('tamu_id', $kunjungan->tamu_id)->first();
            $detailTamu = \App\Models\DetailTamu::where('tamu_id', $kunjungan->tamu_id)->first();
            $namaTamu = $detailTamu ? $detailTamu->nama : 'Tamu';
            
            // Ambil data rumah
            $rumah = DataRumah::where('rumah_id', $kunjungan->rumah_id)->first();
            $alamatRumah = $rumah ? $rumah->alamat_cluster . ' ' . $rumah->blok_rt : 'Alamat tidak diketahui';
            
            // Karena aktivitas dibuat oleh tamu, simpan hanya tamu_id
            $tamu_id = $kunjungan->tamu_id;
            
            // Buat judul dan sub judul
            $judul = 'Kunjungan Baru';
            $subJudul = "Tamu $namaTamu mengajukan kunjungan ke $alamatRumah dengan tujuan: " . $kunjungan->tujuan_kunjungan;
            
            // Simpan aktivitas dengan tamu_id saja, tanpa users_id
            Aktivitas::create([
                'aktivitas_id' => $aktivitas_id,
                'tamu_id' => $tamu_id,
                'users_id' => null, // Tidak menyimpan users_id
                'judul' => $judul,
                'sub_judul' => $subJudul,
            ]);
            
        } catch (\Exception $e) {
            // Log error tapi tidak menghentikan proses
            \Log::error('Gagal menyimpan aktivitas kunjungan: ' . $e->getMessage());
        }
    }
}
