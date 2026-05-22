<?php

namespace Database\Seeders;

use App\Models\Nurse;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoNurseSeeder extends Seeder
{
    /**
     * Seed demo OK and regular nurse accounts.
     */
    public function run(): void
    {
        $okNurseUser = User::firstOrCreate(
            ['email' => 'hilmyofficial03@gmail.com'],
            [
                'name' => 'Perawat OK Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PERAWAT_OK,
            ],
        );

        Nurse::firstOrCreate(
            ['user_id' => $okNurseUser->id],
            [
                'nurse_type' => User::ROLE_PERAWAT_OK,
                'origin_unit' => null,
            ],
        );

        $regularNurseUser = User::firstOrCreate(
            ['email' => 'perawat@gmail.com'],
            [
                'name' => 'Perawat Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PERAWAT_BIASA,
            ],
        );

        Nurse::firstOrCreate(
            ['user_id' => $regularNurseUser->id],
            [
                'nurse_type' => User::ROLE_PERAWAT_BIASA,
                'origin_unit' => 'IGD',
            ],
        );
    }
}
