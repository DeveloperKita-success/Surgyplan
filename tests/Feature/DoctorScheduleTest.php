<?php

use App\Models\Doctor;
use App\Models\OperatingRoom;
use App\Models\Patient;
use App\Models\Specialist;
use App\Models\SurgeryRequest;
use App\Models\SurgerySchedule;
use App\Models\User;

function createDoctorScheduleFixture(): array
{
    $specialist = Specialist::create(['name' => 'Bedah Umum']);
    $doctorUser = User::factory()->create(['role' => User::ROLE_DOKTER]);
    $doctor = Doctor::create([
        'user_id' => $doctorUser->id,
        'specialist_id' => $specialist->id,
        'title' => 'dr.',
        'str_number' => 'STR-DOC-1',
        'sip_number' => 'SIP-DOC-1',
    ]);
    $approver = User::factory()->create(['role' => User::ROLE_PERAWAT_UK]);
    $requester = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $patient = Patient::create([
        'medical_record_number' => 'RM-DOC-1',
        'name' => 'Pasien Dokter',
        'gender' => 'Laki-laki',
        'origin_room' => 'IGD',
        'created_by' => $requester->id,
    ]);
    $request = SurgeryRequest::create([
        'patient_id' => $patient->id,
        'diagnosis_text' => 'K35.8 - Appendicitis akut',
        'procedure_text' => '47.01 - Appendektomi',
        'requested_by' => $requester->id,
        'requested_doctor_id' => $doctor->id,
        'requested_date' => '2026-05-20',
        'requested_start_time' => '10:30',
        'patient_priority' => 'urgent',
        'request_status' => 'disetujui',
    ]);
    $room = OperatingRoom::create([
        'specialist_id' => $specialist->id,
        'room_code' => '1A',
        'room_name' => 'Kamar Operasi 1A',
        'status' => 'siap',
    ]);
    $schedule = SurgerySchedule::create([
        'surgery_request_id' => $request->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'operating_room_id' => $room->id,
        'approved_by' => $approver->id,
        'surgery_date' => '2026-05-20',
        'start_time' => '10:30',
        'end_time' => '12:00',
        'schedule_status' => 'scheduled',
    ]);

    return compact('doctorUser', 'doctor', 'schedule');
}

it('lets doctors view only their own schedules', function () {
    ['doctorUser' => $doctorUser, 'schedule' => $schedule] = createDoctorScheduleFixture();

    $otherSpecialist = Specialist::create(['name' => 'Mata']);
    $otherDoctorUser = User::factory()->create(['role' => User::ROLE_DOKTER]);
    Doctor::create([
        'user_id' => $otherDoctorUser->id,
        'specialist_id' => $otherSpecialist->id,
        'title' => 'dr.',
        'str_number' => 'STR-DOC-2',
        'sip_number' => 'SIP-DOC-2',
    ]);

    $this->actingAs($doctorUser)
        ->get(route('doctor.schedules.index'))
        ->assertOk()
        ->assertSee('Pasien Dokter');

    $this->actingAs($doctorUser)
        ->get(route('doctor.schedules.show', $schedule))
        ->assertOk();

    $this->actingAs($otherDoctorUser)
        ->get(route('doctor.schedules.show', $schedule))
        ->assertForbidden();
});
