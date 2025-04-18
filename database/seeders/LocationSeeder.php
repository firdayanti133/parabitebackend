<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            'Gardu',
            'Laboratorium Game dan IoT',
            'Laboratorium Komputer',
            'Aula Gedung T.P. Rachmat',
            'Aula Gedung Nurcholish Madjid',
            'Ruang Tendik',
            'Ruang Program Studi Teknik Informatika',
            'Ruang Program Studi Desain Komunikasi dan Visual',
            'Ruang Program Studi Desain Produk',
            'Ruang Direktorat Kemahasiswaan dan Inkubator Bisnis',
            'Ruang Teknologi dan Sistem Informasi',
            'Ruang Unit Pengelola Program Studi',
            'Ruang Administrasi',
            'Ruang Humas dan Pemasaran',
            'Lobi Gedung Nurcholish Madjid',
        ];

        foreach ($locations as $location) {
            Location::factory()->create([
                'name' => $location,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
