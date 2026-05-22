<?php

use App\Models\Doctor;
use App\Models\OperatingRoom;
use App\Models\Patient;
use App\Models\Specialist;
use App\Models\SurgeryRequest;
use App\Models\SurgerySchedule;
use App\Models\User;

it('shows regular nurse dashboard stats for their own surgery requests', function () {
    $regularNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $otherRegularNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $okNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_OK]);
    $doctorUser = User::factory()->create(['role' => User::ROLE_DOKTER]);

    $specialist = Specialist::create(['name' => 'Bedah Umum']);
    $doctor = Doctor::create([
        'user_id' => $doctorUser->id,
        'specialist_id' => $specialist->id,
        'title' => 'dr.',
        'str_number' => 'STR-DASH-1',
        'sip_number' => 'SIP-DASH-1',
    ]);
    $room = OperatingRoom::create([
        'room_code' => 'DASH-1',
        'room_name' => 'Kamar Operasi Dashboard',
        'status' => 'siap',
        'capacity' => 1,
    ]);

    $createRequest = function (User $requester, string $medicalRecordNumber, string $createdAt) use ($doctor): SurgeryRequest {
        $patient = Patient::create([
            'medical_record_number' => $medicalRecordNumber,
            'name' => "Pasien {$medicalRecordNumber}",
            'gender' => 'Laki-laki',
            'origin_room' => 'IGD',
            'created_by' => $requester->id,
        ]);

        return SurgeryRequest::create([
            'patient_id' => $patient->id,
            'diagnosis_text' => 'Diagnosis dashboard',
            'procedure_text' => 'Tindakan dashboard',
            'requested_by' => $requester->id,
            'requested_doctor_id' => $doctor->id,
            'requested_date' => today(),
            'requested_start_time' => '08:00',
            'patient_priority' => 'urgent',
            'request_status' => 'disetujui',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    };

    $todayRequest = $createRequest($regularNurse, 'RM-DASH-1', now()->toDateTimeString());
    $createRequest($regularNurse, 'RM-DASH-2', now()->subDay()->toDateTimeString());
    $otherRequest = $createRequest($otherRegularNurse, 'RM-DASH-3', now()->toDateTimeString());

    SurgerySchedule::create([
        'surgery_request_id' => $todayRequest->id,
        'patient_id' => $todayRequest->patient_id,
        'doctor_id' => $doctor->id,
        'operating_room_id' => $room->id,
        'approved_by' => $okNurse->id,
        'surgery_date' => today(),
        'start_time' => '08:00',
        'end_time' => '10:00',
        'schedule_status' => 'scheduled',
    ]);
    SurgerySchedule::create([
        'surgery_request_id' => $otherRequest->id,
        'patient_id' => $otherRequest->patient_id,
        'doctor_id' => $doctor->id,
        'operating_room_id' => $room->id,
        'approved_by' => $okNurse->id,
        'surgery_date' => today(),
        'start_time' => '10:00',
        'end_time' => '12:00',
        'schedule_status' => 'scheduled',
    ]);

    $this->actingAs($regularNurse)
        ->get(route('nurse-regular.dashboard'))
        ->assertOk()
        ->assertSee('Total Pengajuan Hari Ini')
        ->assertSee('Total Pengajuan')
        ->assertSee('Total Penjadwalan Operasi')
        ->assertSee('01')
        ->assertSee('02');

    $this->actingAs($regularNurse)
        ->get(route('dashboard.nurse.regular'))
        ->assertOk()
        ->assertSee('Total Pengajuan Hari Ini');
});
