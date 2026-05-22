<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Specialist;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDoctorSeeder extends Seeder
{
    /**
     * Seed demo doctor account and profile.
     */
    public function run(): void
    {
        $specialist = Specialist::firstOrCreate(['name' => 'Spesialis Bedah Umum']);

        $doctorUser = User::firstOrCreate(
            ['email' => 'hilmygreenlight@gmail.com'],
            [
                'name' => 'Dokter Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DOKTER,
            ],
        );

        Doctor::firstOrCreate(
            ['user_id' => $doctorUser->id],
            [
                'specialist_id' => $specialist->id,
                'title' => 'dr.',
                'str_number' => 'STR-0001',
                'sip_number' => 'SIP-0001',
            ],
        );
    }
}
