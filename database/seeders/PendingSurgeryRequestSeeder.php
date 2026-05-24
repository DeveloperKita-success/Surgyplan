<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Nurse;
use App\Models\Patient;
use App\Models\PatientPreoperativeChecklist;
use App\Models\Specialist;
use App\Models\SurgeryHistory;
use App\Models\SurgeryRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class PendingSurgeryRequestSeeder extends Seeder
{
    public function run(): void
    {
        $specialist = Specialist::firstOrCreate(['name' => 'Spesialis Bedah Umum']);

        $doctorUser = User::firstOrCreate(
            ['email' => 'yunaaapril@gmail.com'],
            [
                'name' => 'Dokter Pending Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DOKTER,
            ],
        );

        $doctor = Doctor::firstOrCreate(
            ['user_id' => $doctorUser->id],
            [
                'specialist_id' => $specialist->id,
                'title' => 'dr.',
                'str_number' => 'STR-PENDING-001',
                'sip_number' => 'SIP-PENDING-001',
            ],
        );

        $regularNurseUser = User::firstOrCreate(
            ['email' => 'perawat.pending@example.com'],
            [
                'name' => 'Perawat Pending Demo',
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

        $rows = [
            [
                'medical_record_number' => 'RM-PENDING-001',
                'name' => 'Ahmad Pratama',
                'birth_date' => '1988-02-14',
                'gender' => 'Laki-laki',
                'origin_room' => 'IGD',
                'diagnosis_text' => 'K35.8 - Appendicitis akut',
                'procedure_text' => '47.01 - Appendektomi',
                'requested_date' => now()->addDay()->toDateString(),
                'requested_start_time' => '08:00',
                'requested_end_time' => '09:30',
                'patient_priority' => 'urgent',
            ],
            [
                'medical_record_number' => 'RM-PENDING-002',
                'name' => 'Siti Rahma',
                'birth_date' => '1993-07-20',
                'gender' => 'Perempuan',
                'origin_room' => 'Bangsal',
                'diagnosis_text' => 'K80.2 - Batu kandung empedu',
                'procedure_text' => '51.23 - Kolesistektomi laparoskopik',
                'requested_date' => now()->addDays(2)->toDateString(),
                'requested_start_time' => '10:00',
                'requested_end_time' => '11:30',
                'patient_priority' => 'expedited',
            ],
            [
                'medical_record_number' => 'RM-PENDING-003',
                'name' => 'Bambang Wijaya',
                'birth_date' => '1979-11-05',
                'gender' => 'Laki-laki',
                'origin_room' => 'Poli',
                'diagnosis_text' => 'K40.9 - Hernia inguinalis',
                'procedure_text' => '53.05 - Repair hernia inguinalis',
                'requested_date' => now()->addDays(3)->toDateString(),
                'requested_start_time' => '13:00',
                'requested_end_time' => '14:30',
                'patient_priority' => 'elektif',
            ],
        ];

        foreach ($rows as $row) {
            $patient = Patient::updateOrCreate(
                ['medical_record_number' => $row['medical_record_number']],
                [
                    'name' => $row['name'],
                    'birth_date' => $row['birth_date'],
                    'age' => Carbon::parse($row['birth_date'])->age,
                    'gender' => $row['gender'],
                    'origin_room' => $row['origin_room'],
                    'address' => 'Alamat demo '.$row['name'],
                    'phone' => '08'.fake()->numerify('##########'),
                    'created_by' => $regularNurseUser->id,
                ],
            );

            $surgeryRequest = SurgeryRequest::updateOrCreate(
                [
                    'patient_id' => $patient->id,
                    'request_status' => 'menunggu',
                ],
                [
                    'diagnosis_text' => $row['diagnosis_text'],
                    'procedure_text' => $row['procedure_text'],
                    'requested_by' => $regularNurseUser->id,
                    'requested_doctor_id' => $doctor->id,
                    'requested_date' => $row['requested_date'],
                    'requested_start_time' => $row['requested_start_time'],
                    'requested_end_time' => $row['requested_end_time'],
                    'patient_priority' => $row['patient_priority'],
                    'notes' => 'Seeder: pengajuan belum divalidasi Perawat OK.',
                ],
            );

            PatientPreoperativeChecklist::updateOrCreate(
                ['surgery_request_id' => $surgeryRequest->id],
                [
                    'surgical_consent' => true,
                    'anesthesia_consent' => true,
                    'lab_result_complete' => true,
                    'radiology_available' => true,
                    'anesthesia_consultation_done' => false,
                    'anesthesia_risk_estimation' => 'Belum dikaji dokter anestesi.',
                    'vital_sign_stable' => true,
                    'vital_sign_note' => 'Stabil saat pengajuan.',
                    'blood_pressure' => '120/80',
                    'allergy' => null,
                    'fasting_more_than_6_hours' => true,
                    'blood_type' => 'O',
                    'blood_available' => false,
                    'infusion_installed' => true,
                    'catheter_installed' => false,
                    'surgical_area_shaved' => false,
                    'jewelry_removed' => true,
                    'disease_history' => 'Tidak ada riwayat mayor pada data demo.',
                    'current_medications' => null,
                    'has_previous_surgery' => false,
                    'previous_surgery_note' => null,
                    'previous_surgery_date' => null,
                    'final_note' => 'Menunggu review dan validasi Perawat OK.',
                ],
            );

            SurgeryHistory::firstOrCreate(
                [
                    'surgery_request_id' => $surgeryRequest->id,
                    'new_status' => 'menunggu',
                ],
                [
                    'changed_by' => $regularNurseUser->id,
                    'old_status' => null,
                    'note' => 'Pengajuan dibuat dari seeder dan belum direview Perawat OK.',
                ],
            );
        }
    }
}
