<?php

namespace App\Http\Controllers\Api\satpam\beranda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kunjungan;
use App\Models\Tamu;
use Carbon\Carbon;

class KunjunganTamuApiController extends Controller
{
    /**
     * Mendapatkan daftar kunjungan untuk satpam
     */
    public function getKunjunganList(Request $request)
    {
        try {
            // Ambil semua data kunjungan dengan relasi ke tamu dan rumah
            $kunjungan = Kunjungan::with(['tamu' => function($query) {
                    $query->select('tamu_id', 'no_hp');
                }, 'tamu.detailTamu' => function($query) {
                    $query->select('tamu_id', 'nama', 'nik');
                }, 'rumah' => function($query) {
                    $query->select('rumah_id', 'blok_rt', 'alamat_cluster');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            // Format data untuk response
            $formattedData = $kunjungan->map(function($item) {
                // Format tanggal dari ISO string ke format DD/MM/YYYY
                $createdDate = Carbon::parse($item->created_at);
                $tanggal = $createdDate->format('d/m/Y');
                
                // Kirim waktu dalam format ISO untuk dikonversi ke waktu lokal di client
                $waktuMasuk = $item->waktu_masuk ? Carbon::parse($item->waktu_masuk)->toISOString() : '-';
                $waktuKeluar = $item->waktu_keluar ? Carbon::parse($item->waktu_keluar)->toISOString() : null;
                
                // Ambil blok dari data rumah jika ada
                $blok = $item->rumah ? $item->rumah->blok_rt : $item->rumah_id;
                
                // Ambil nama tamu dari detail tamu jika ada
                $namaTamu = '';
                if ($item->tamu && $item->tamu->detailTamu) {
                    $namaTamu = $item->tamu->detailTamu->nama;
                }
                
                return [
                    'id_kunjungan' => $item->kunjungan_id,
                    'nama_tamu' => $namaTamu,
                    'tanggal' => $tanggal,
                    'tujuan' => $item->tujuan_kunjungan,
                    'status' => $item->status_kunjungan,
                    'blok' => $blok,
                    'waktu_masuk' => $waktuMasuk,
                    'waktu_keluar' => $waktuKeluar,
                    'tamu_id' => $item->tamu_id,
                    'rumah_id' => $item->rumah_id,
                    'created_at' => $item->created_at->toISOString(),
                    'updated_at' => $item->updated_at->toISOString()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan detail kunjungan berdasarkan ID
     */
    public function getKunjunganById($kunjungan_id)
    {
        try {
            $kunjungan = Kunjungan::where('kunjungan_id', $kunjungan_id)
                ->with(['tamu.detailTamu', 'rumah'])
                ->first();

            if (!$kunjungan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kunjungan tidak ditemukan'
                ], 404);
            }

            // Konversi waktu ke ISO string untuk diproses di client
            if ($kunjungan->waktu_masuk) {
                $kunjungan->waktu_masuk = Carbon::parse($kunjungan->waktu_masuk)->toISOString();
            }
            
            if ($kunjungan->waktu_keluar) {
                $kunjungan->waktu_keluar = Carbon::parse($kunjungan->waktu_keluar)->toISOString();
            }
            
            $kunjungan->created_at = Carbon::parse($kunjungan->created_at)->toISOString();
            $kunjungan->updated_at = Carbon::parse($kunjungan->updated_at)->toISOString();

            return response()->json([
                'success' => true,
                'data' => $kunjungan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update status kunjungan (tamu masuk)
     */
    public function tamuMasuk(Request $request)
    {
        $request->validate([
            'kunjungan_id' => 'required|string|exists:kunjungan,kunjungan_id'
        ]);

        try {
            $kunjungan = Kunjungan::where('kunjungan_id', $request->kunjungan_id)->first();
            
            if (!$kunjungan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data kunjungan tidak ditemukan'
                ], 404);
            }
            
            // Update status kunjungan dan waktu masuk
            $kunjungan->status_kunjungan = 'Sedang Berlangsung';
            $kunjungan->waktu_masuk = Carbon::now();
            $kunjungan->save();
            
            // Konversi waktu ke ISO string untuk diproses di client
            $kunjungan->waktu_masuk = Carbon::parse($kunjungan->waktu_masuk)->toISOString();
            if ($kunjungan->waktu_keluar) {
                $kunjungan->waktu_keluar = Carbon::parse($kunjungan->waktu_keluar)->toISOString();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Status kunjungan berhasil diperbarui',
                'data' => $kunjungan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update status kunjungan (tamu keluar)
     */
    public function tamuKeluar(Request $request)
    {
        $request->validate([
            'kunjungan_id' => 'required|string|exists:kunjungan,kunjungan_id'
        ]);

        try {
            $kunjungan = Kunjungan::where('kunjungan_id', $request->kunjungan_id)->first();
            
            if (!$kunjungan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data kunjungan tidak ditemukan'
                ], 404);
            }
            
            // Update status kunjungan dan waktu keluar
            $kunjungan->status_kunjungan = 'Meninggalkan Cluster';
            $kunjungan->waktu_keluar = Carbon::now();
            $kunjungan->save();
            
            // Konversi waktu ke ISO string untuk diproses di client
            if ($kunjungan->waktu_masuk) {
                $kunjungan->waktu_masuk = Carbon::parse($kunjungan->waktu_masuk)->toISOString();
            }
            $kunjungan->waktu_keluar = Carbon::parse($kunjungan->waktu_keluar)->toISOString();
            
            return response()->json([
                'success' => true,
                'message' => 'Status kunjungan berhasil diperbarui',
                'data' => $kunjungan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }
}
