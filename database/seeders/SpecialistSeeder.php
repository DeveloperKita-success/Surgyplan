<?php

namespace Database\Seeders;

use App\Models\Specialist;
use Illuminate\Database\Seeder;

class SpecialistSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $specialists = [
            'Spesialis Penyakit Dalam',
            'Spesialis Bedah Umum',
            'Spesialis Anak',
            'Spesialis Kandungan dan Kebidanan (Obgyn)',
            'Spesialis Jantung dan Pembuluh Darah',
            'Spesialis Saraf',
            'Spesialis Mata',
            'Spesialis THT (Telinga Hidung Tenggorokan)',
            'Spesialis Kulit dan Kelamin',
            'Spesialis Paru',
            'Spesialis Ortopedi dan Traumatologi',
            'Spesialis Urologi',
            'Spesialis Anestesi',
            'Spesialis Radiologi',
            'Spesialis Gigi dan Mulut',
            'Spesialis Plastik',
        ];

        foreach ($specialists as $name) {
            Specialist::firstOrCreate(['name' => $name]);
        }
    }
}
