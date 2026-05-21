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
        $this->call(SpecialistSeeder::class);

        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin SurgyPlan',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        $specialist = Specialist::firstOrCreate(['name' => 'Spesialis Bedah Umum']);

        $doctorUser = User::firstOrCreate(
            ['email' => 'dokter@gmail.com'],
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

        $okNurseUser = User::firstOrCreate(
            ['email' => 'perawat.ok@gmail.com'],
            [
                'name' => 'Perawat OK Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PERAWAT_OK,
            ]
        );

        Nurse::firstOrCreate(
            ['user_id' => $okNurseUser->id],
            [
                'nurse_type' => User::ROLE_PERAWAT_OK,
                'origin_unit' => null,
            ]
        );

        $regularNurseUser = User::firstOrCreate(
            ['email' => 'perawat@gmail.com'],
            [
                'name' => 'Perawat Demo',
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

        $this->call(PendingSurgeryRequestSeeder::class);
    }
}
