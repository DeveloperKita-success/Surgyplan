<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Nurse;
use App\Models\Specialist;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $specialist = Specialist::firstOrCreate(
            ['name' => 'Bedah Umum'],
            ['description' => 'Spesialis bedah umum']
        );

        $doctorUser = User::firstOrCreate(
            ['email' => 'dokter@example.com'],
            [
                'name' => 'Dokter Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DOKTER,
            ]
        );

        Doctor::firstOrCreate(
            ['user_id' => $doctorUser->id],
            [
                'specialist_id' => $specialist->id,
                'title' => 'dr.',
                'str_number' => 'STR-0001',
                'sip_number' => 'SIP-0001',
            ]
        );

        $ukNurseUser = User::firstOrCreate(
            ['email' => 'perawat.uk@example.com'],
            [
                'name' => 'Perawat UK Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PERAWAT_UK,
            ]
        );

        Nurse::firstOrCreate(
            ['user_id' => $ukNurseUser->id],
            [
                'nurse_type' => User::ROLE_PERAWAT_UK,
                'origin_unit' => null,
            ]
        );

        $regularNurseUser = User::firstOrCreate(
            ['email' => 'perawat.biasa@example.com'],
            [
                'name' => 'Perawat Biasa Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PERAWAT_BIASA,
            ]
        );

        Nurse::firstOrCreate(
            ['user_id' => $regularNurseUser->id],
            [
                'nurse_type' => User::ROLE_PERAWAT_BIASA,
                'origin_unit' => 'IGD',
            ]
        );
    }
}
