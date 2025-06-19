<?php

namespace App\Http\Controllers\Api\satpam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\Image;
use Exception;

class OcrApiController extends Controller
{
    public function processKtp(Request $request)
    {
        Log::info('Memulai proses OCR KTP');
        
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            Log::error('Validasi gagal: ' . json_encode($validator->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cek apakah ada file yang diupload
            if (!$request->hasFile('image')) {
                Log::error('Tidak ada file gambar yang diupload');
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada file gambar yang diupload',
                ], 422);
            }

            $image = $request->file('image');
            
            // Verifikasi file adalah gambar yang valid
            if (!in_array($image->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
                Log::error('File bukan gambar yang valid: ' . $image->getMimeType());
                return response()->json([
                    'success' => false,
                    'message' => 'File bukan gambar yang valid. Gunakan format JPG atau PNG.',
                ], 422);
            }
            
            // Dapatkan informasi gambar
            $imageInfo = getimagesize($image->getPathname());
            if (!$imageInfo) {
                Log::error('Gagal mendapatkan informasi gambar');
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses gambar. Format tidak didukung.',
                ], 422);
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $fileSize = $image->getSize();
            
            Log::info('Info gambar: ' . json_encode([
                'nama_file' => $image->getClientOriginalName(),
                'ukuran' => $fileSize,
                'dimensi' => $width . 'x' . $height,
                'tipe' => $image->getMimeType(),
            ]));
            
            // Cek apakah ukuran gambar cukup besar untuk OCR KTP
            if ($width < 800 || $height < 500) {
                Log::error("Gambar terlalu kecil untuk diproses OCR: ${width}x${height}");
                return response()->json([
                    'success' => false,
                    'message' => 'Resolusi gambar terlalu rendah. Gunakan gambar dengan kualitas yang lebih baik.',
                ], 422);
            }
            
            // Simpan gambar yang diupload
            $imagePath = $image->store('ktp_images', 'public');
            Log::info('Gambar KTP berhasil disimpan: ' . $imagePath);
            
            // Dapatkan URL publik gambar
            $imageUrl = Storage::url($imagePath);
            
            // Implementasi Google Cloud Vision API
            try {
                // Buat client Google Cloud Vision
                // Untuk production, gunakan kredensial yang disimpan di environment variable
                // atau file JSON yang disimpan secara aman
                $imageAnnotator = new ImageAnnotatorClient([
                    'credentials' => json_decode(file_get_contents(storage_path('app/' . config('services.google.cloud.key_file'))), true)
                ]);
                
                // Baca file gambar
                $imageContent = file_get_contents($image->getPathname());
                
                // Buat objek Image
                $visionImage = new Image();
                $visionImage->setContent($imageContent);
                
                // Tentukan fitur yang ingin digunakan (TEXT_DETECTION untuk OCR)
                $feature = new \Google\Cloud\Vision\V1\Feature();
                $feature->setType(Type::TEXT_DETECTION);
                $feature->setMaxResults(50);
                
                // Buat request
                $request = new \Google\Cloud\Vision\V1\AnnotateImageRequest();
                $request->setImage($visionImage);
                $request->setFeatures([$feature]);
                
                // Kirim request
                $response = $imageAnnotator->annotateImage($request);
                
                // Ambil hasil OCR
                $texts = $response->getTextAnnotations();
                
                if (count($texts) === 0) {
                    Log::error('Tidak ada teks yang terdeteksi pada gambar');
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada teks yang terdeteksi pada KTP. Pastikan gambar KTP jelas dan tidak terpotong.',
                        'image_info' => [
                            'path' => $imageUrl,
                            'width' => $width,
                            'height' => $height,
                            'filesize' => $fileSize,
                            'filetype' => $image->getMimeType(),
                        ]
                    ], 422);
                }
                
                // Text annotation pertama berisi seluruh teks yang terdeteksi
                $fullText = $texts[0]->getDescription();
                Log::info('Teks yang terdeteksi: ' . $fullText);
                
                // Parsing data KTP dari teks yang terdeteksi
                $extractedData = $this->parseKtpData($fullText);
                
                // Tutup client
                $imageAnnotator->close();
                
                return response()->json([
                    'success' => true,
                    'message' => 'OCR KTP berhasil',
                    'data' => $extractedData,
                    'full_text' => $fullText,
                    'image_info' => [
                        'path' => $imageUrl,
                        'width' => $width,
                        'height' => $height,
                        'filesize' => $fileSize,
                        'filetype' => $image->getMimeType(),
                    ]
                ], 200);
                
            } catch (Exception $e) {
                Log::error('Error pada Google Cloud Vision: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses OCR KTP menggunakan Google Cloud Vision. ' . $e->getMessage(),
                    'image_info' => [
                        'path' => $imageUrl,
                        'width' => $width,
                        'height' => $height,
                        'filesize' => $fileSize,
                        'filetype' => $image->getMimeType(),
                    ]
                ], 422);
            }
            
        } catch (Exception $e) {
            Log::error('Error pada proses OCR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR KTP. Terjadi kesalahan sistem.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Parse data KTP dari teks OCR
     * 
     * @param string $text
     * @return array
     */
    private function parseKtpData($text)
    {
        // Inisialisasi data yang akan diekstrak
        $data = [
            'nik' => '',
            'nama' => '',
            'tempat_lahir' => '',
            'tgl_lahir' => '',
            'kewarganegaraan' => 'WNI', // Default
            'alamat' => '',
            'rt' => '',
            'rw' => '',
            'kel_desa' => '',
            'kecamatan' => '',
            'agama' => '',
        ];
        
        // Normalisasi teks (hapus spasi berlebih, ubah ke huruf besar)
        $text = trim(preg_replace('/\s+/', ' ', $text));
        
        // Ekstrak NIK (16 digit angka)
        if (preg_match('/NIK\s*:\s*(\d{16})/', $text, $matches) || preg_match('/(\d{16})/', $text, $matches)) {
            $data['nik'] = $matches[1];
        }
        
        // Ekstrak nama
        if (preg_match('/Nama\s*:\s*([^\n:]+)/i', $text, $matches)) {
            $data['nama'] = trim($matches[1]);
        }
        
        // Ekstrak tempat lahir dan tanggal lahir
        if (preg_match('/(?:Tempat|Tempat\/Tgl)[\s\.]*(?:Lahir|Tgl\.Lahir)\s*:\s*([^,\n:]+),?\s*(\d{2})-(\d{2})-(\d{4})/i', $text, $matches)) {
            $data['tempat_lahir'] = trim($matches[1]);
            $data['tgl_lahir'] = $matches[4] . '-' . $matches[3] . '-' . $matches[2]; // Format YYYY-MM-DD
        } elseif (preg_match('/(?:Tempat|Tempat\/Tgl)[\s\.]*(?:Lahir|Tgl\.Lahir)\s*:\s*([^,\n:]+)/i', $text, $matches)) {
            $data['tempat_lahir'] = trim($matches[1]);
        }
        
        // Ekstrak alamat
        if (preg_match('/Alamat\s*:\s*([^\n:]+)/i', $text, $matches)) {
            $data['alamat'] = trim($matches[1]);
        }
        
        // Ekstrak RT/RW
        if (preg_match('/RT\/RW\s*:\s*(\d+)\/(\d+)/i', $text, $matches)) {
            $data['rt'] = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
            $data['rw'] = str_pad($matches[2], 3, '0', STR_PAD_LEFT);
        } elseif (preg_match('/RT\s*:\s*(\d+)\s*RW\s*:\s*(\d+)/i', $text, $matches)) {
            $data['rt'] = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
            $data['rw'] = str_pad($matches[2], 3, '0', STR_PAD_LEFT);
        }
        
        // Ekstrak Kel/Desa
        if (preg_match('/(?:Kel|Desa|Kelurahan|Desa)\/(?:Kel|Desa|Kelurahan|Desa)\s*:\s*([^\n:]+)/i', $text, $matches)) {
            $data['kel_desa'] = trim($matches[1]);
        } elseif (preg_match('/(?:Kel|Desa|Kelurahan|Desa)\s*:\s*([^\n:]+)/i', $text, $matches)) {
            $data['kel_desa'] = trim($matches[1]);
        }
        
        // Ekstrak Kecamatan
        if (preg_match('/Kecamatan\s*:\s*([^\n:]+)/i', $text, $matches)) {
            $data['kecamatan'] = trim($matches[1]);
        }
        
        // Ekstrak Agama
        if (preg_match('/Agama\s*:\s*([^\n:]+)/i', $text, $matches)) {
            $data['agama'] = trim($matches[1]);
        }
        
        // Jika ada field yang masih kosong, coba ekstrak dengan cara lain
        if (empty($data['nama']) && preg_match('/([A-Z\s]+)(?=\s*Tempat|NIK|Lahir)/i', $text, $matches)) {
            $data['nama'] = trim($matches[1]);
        }
        
        // Bersihkan data dari karakter yang tidak diinginkan
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim(preg_replace('/[^\p{L}\p{N}\s\-\/\.,]/u', '', $value));
            }
        }
        
        return $data;
    }
} 