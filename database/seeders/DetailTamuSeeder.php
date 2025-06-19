<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DetailTamu;

class DetailTamuSeeder extends Seeder
{
    public function run(): void
    {
        DetailTamu::insert([
            [
                'tamu_id' => 'T00001',
                'nik' => '3201010101010001',
                'nama' => 'Budi Santoso',
                'tempat_lahir' => 'Bandung',
                'tgl_lahir' => '1990-01-01',
                'kewarganegaraan' => 'Indonesia',
                'alamat' => 'Jl. Merdeka No.1',
                'rt' => '01',
                'rw' => '02',
                'kel_desa' => 'Cihampelas',
                'kecamatan' => 'Cimahi',
                'agama' => 'Islam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tamu_id' => 'T00002',
                'nik' => '3201010101010002',
                'nama' => 'Siti Aminah',
                'tempat_lahir' => 'Jakarta',
                'tgl_lahir' => '1992-02-02',
                'kewarganegaraan' => 'Indonesia',
                'alamat' => 'Jl. Sudirman No.2',
                'rt' => '03',
                'rw' => '04',
                'kel_desa' => 'Menteng',
                'kecamatan' => 'Setiabudi',
                'agama' => 'Kristen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 