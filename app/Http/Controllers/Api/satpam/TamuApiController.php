<?php

namespace App\Http\Controllers\Api\satpam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tamu;
use App\Models\DetailTamu;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class TamuApiController extends Controller
{
    // Generate unique Tamu ID (7 characters alphanumeric)
    private function generateTamuId()
    {
        $tamuId = strtoupper(Str::random(7));
        
        // Check if ID already exists, if yes, generate new one
        while (Tamu::where('tamu_id', $tamuId)->exists()) {
            $tamuId = strtoupper(Str::random(7));
        }
        
        return $tamuId;
    }
    
    // Store new tamu data
    public function store(Request $request)
    {
        // Validate tamu contact information
        $validator = Validator::make($request->all(), [
            'no_hp' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            // Detail tamu validation
            'nik' => 'required|string|max:20',
            'nama' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:50',
            'tgl_lahir' => 'required|date',
            'kewarganegaraan' => 'required|string|max:30',
            'alamat' => 'required|string|max:255',
            'rt' => 'required|string|max:5',
            'rw' => 'required|string|max:5',
            'kel_desa' => 'required|string|max:50',
            'kecamatan' => 'required|string|max:50',
            'kabupaten' => 'required|string|max:50',
            'agama' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate unique tamu_id
            $tamuId = $this->generateTamuId();
            
            // Create new tamu record
            $tamu = Tamu::create([
                'tamu_id' => $tamuId,
                'no_hp' => $request->no_hp,
                'email' => $request->email,
            ]);
            
            // Create detail tamu record
            $detailTamu = DetailTamu::create([
                'tamu_id' => $tamuId,
                'nik' => $request->nik,
                'nama' => $request->nama,
                'tempat_lahir' => $request->tempat_lahir,
                'tgl_lahir' => $request->tgl_lahir,
                'kewarganegaraan' => $request->kewarganegaraan,
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'kel_desa' => $request->kel_desa,
                'kecamatan' => $request->kecamatan,
                'kabupaten' => $request->kabupaten,
                'agama' => $request->agama,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Data tamu berhasil disimpan',
                'data' => [
                    'tamu_id' => $tamuId,
                    'tamu' => $tamu,
                    'detail_tamu' => $detailTamu
                ]
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data tamu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Get tamu by ID
    public function getTamuById($tamu_id)
    {
        try {
            $tamu = Tamu::with('detailTamu')->where('tamu_id', $tamu_id)->first();
            
            if (!$tamu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tamu tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $tamu
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tamu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // OCR processing endpoint
    public function processOcr(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Simulasi hasil OCR (dalam implementasi nyata, gunakan layanan OCR seperti Google Vision API)
            // Ini hanya contoh, pada implementasi sebenarnya akan menggunakan layanan OCR
            $extractedData = [
                'nik' => '1234567890123456',
                'nama' => 'NAMA CONTOH',
                'tempat_lahir' => 'JAKARTA',
                'tgl_lahir' => '1990-01-01',
                'kewarganegaraan' => 'WNI',
                'alamat' => 'JL. CONTOH NO. 123',
                'rt' => '001',
                'rw' => '002',
                'kel_desa' => 'KELURAHAN CONTOH',
                'kecamatan' => 'KECAMATAN CONTOH',
                'kabupaten' => 'KABUPATEN CONTOH',
                'agama' => 'ISLAM',
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'OCR berhasil',
                'data' => $extractedData
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 