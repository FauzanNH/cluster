<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tamu;

class TamuSeeder extends Seeder
{
    public function run(): void
    {
        Tamu::insert([
            [
                'tamu_id' => 'T00001',
                'no_hp' => '081234567890',
                'email' => 'tamu1@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tamu_id' => 'T00002',
                'no_hp' => '081298765432',
                'email' => 'tamu2@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 