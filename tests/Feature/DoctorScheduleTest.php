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
    $approver = User::factory()->create(['role' => User::ROLE_PERAWAT_OK]);
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
        'capacity' => 1,
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

it('lets doctors view schedules from the same specialist only', function () {
    ['doctorUser' => $doctorUser, 'doctor' => $doctor, 'schedule' => $schedule] = createDoctorScheduleFixture();

    $otherSpecialist = Specialist::create(['name' => 'Mata']);
    $otherDoctorUser = User::factory()->create(['role' => User::ROLE_DOKTER]);
    $otherDoctor = Doctor::create([
        'user_id' => $otherDoctorUser->id,
        'specialist_id' => $otherSpecialist->id,
        'title' => 'dr.',
        'str_number' => 'STR-DOC-2',
        'sip_number' => 'SIP-DOC-2',
    ]);
    $sameSpecialistDoctorUser = User::factory()->create([
        'name' => 'Dokter Rekan Spesialis',
        'role' => User::ROLE_DOKTER,
    ]);
    $sameSpecialistDoctor = Doctor::create([
        'user_id' => $sameSpecialistDoctorUser->id,
        'specialist_id' => $doctor->specialist_id,
        'title' => 'dr.',
        'str_number' => 'STR-DOC-3',
        'sip_number' => 'SIP-DOC-3',
    ]);
    $approver = User::factory()->create(['role' => User::ROLE_PERAWAT_OK]);
    $requester = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $room = OperatingRoom::create([
        'room_code' => '1B',
        'room_name' => 'Kamar Operasi 1B',
        'status' => 'siap',
        'capacity' => 1,
    ]);

    $sameSpecialistPatient = Patient::create([
        'medical_record_number' => 'RM-DOC-2',
        'name' => 'Pasien Rekan Spesialis',
        'gender' => 'Laki-laki',
        'origin_room' => 'Poli',
        'created_by' => $requester->id,
    ]);
    $sameSpecialistRequest = SurgeryRequest::create([
        'patient_id' => $sameSpecialistPatient->id,
        'diagnosis_text' => 'Diagnosis rekan',
        'procedure_text' => 'Tindakan rekan',
        'requested_by' => $requester->id,
        'requested_doctor_id' => $sameSpecialistDoctor->id,
        'requested_date' => '2026-05-21',
        'requested_start_time' => '08:00',
        'patient_priority' => 'urgent',
        'request_status' => 'disetujui',
    ]);
    $sameSpecialistSchedule = SurgerySchedule::create([
        'surgery_request_id' => $sameSpecialistRequest->id,
        'patient_id' => $sameSpecialistPatient->id,
        'doctor_id' => $sameSpecialistDoctor->id,
        'operating_room_id' => $room->id,
        'approved_by' => $approver->id,
        'surgery_date' => '2026-05-21',
        'start_time' => '08:00',
        'end_time' => '10:00',
        'schedule_status' => 'scheduled',
    ]);

    $otherPatient = Patient::create([
        'medical_record_number' => 'RM-DOC-3',
        'name' => 'Pasien Spesialis Mata',
        'gender' => 'Perempuan',
        'origin_room' => 'IGD',
        'created_by' => $requester->id,
    ]);
    $otherRequest = SurgeryRequest::create([
        'patient_id' => $otherPatient->id,
        'diagnosis_text' => 'Diagnosis lain',
        'procedure_text' => 'Tindakan lain',
        'requested_by' => $requester->id,
        'requested_doctor_id' => $otherDoctor->id,
        'requested_date' => '2026-05-21',
        'requested_start_time' => '09:00',
        'patient_priority' => 'urgent',
        'request_status' => 'disetujui',
    ]);
    $otherSchedule = SurgerySchedule::create([
        'surgery_request_id' => $otherRequest->id,
        'patient_id' => $otherPatient->id,
        'doctor_id' => $otherDoctor->id,
        'operating_room_id' => $room->id,
        'approved_by' => $approver->id,
        'surgery_date' => '2026-05-21',
        'start_time' => '09:00',
        'end_time' => '11:00',
        'schedule_status' => 'scheduled',
    ]);

    $this->actingAs($doctorUser)
        ->get(route('doctor.schedules.index'))
        ->assertOk()
        ->assertSee('Pasien Dokter')
        ->assertSee('Pasien Rekan Spesialis')
        ->assertDontSee('Pasien Spesialis Mata');

    $this->actingAs($doctorUser)
        ->get(route('doctor.schedules.index', ['q' => 'Dokter Rekan']))
        ->assertOk()
        ->assertSee('Pasien Rekan Spesialis')
        ->assertDontSee('Pasien Dokter')
        ->assertDontSee('Pasien Spesialis Mata');

    $this->actingAs($doctorUser)
        ->get(route('doctor.schedules.index', ['q' => 'Dokter Spesialis Tidak Ada']))
        ->assertOk()
        ->assertSee('Belum ada data pasien');

    $this->actingAs($doctorUser)
        ->get(route('doctor.schedules.index', ['date' => '2026-05-21']))
        ->assertOk()
        ->assertSee('Pasien Rekan Spesialis')
        ->assertDontSee('Pasien Dokter')
        ->assertDontSee('Pasien Spesialis Mata');

    $this->actingAs($doctorUser)
        ->get(route('doctor.schedules.index', ['date' => '2026-05-20']))
        ->assertOk()
        ->assertSee('Pasien Dokter')
        ->assertDontSee('Pasien Rekan Spesialis')
        ->assertDontSee('Pasien Spesialis Mata');

    $this->actingAs($doctorUser)
        ->get(route('doctor.schedules.show', $schedule))
        ->assertOk();

    $this->actingAs($doctorUser)
        ->get(route('doctor.schedules.show', $sameSpecialistSchedule))
        ->assertOk();

    $this->actingAs($otherDoctorUser)
        ->get(route('doctor.schedules.show', $schedule))
        ->assertForbidden();

    $this->actingAs($doctorUser)
        ->get(route('doctor.schedules.show', $otherSchedule))
        ->assertForbidden();
});

it('shows doctor dashboard stats and same specialist schedules only', function () {
    ['doctorUser' => $doctorUser, 'doctor' => $doctor] = createDoctorScheduleFixture();

    $sameSpecialistDoctorUser = User::factory()->create([
        'name' => 'Dokter Spesialis Sama',
        'role' => User::ROLE_DOKTER,
    ]);
    $sameSpecialistDoctor = Doctor::create([
        'user_id' => $sameSpecialistDoctorUser->id,
        'specialist_id' => $doctor->specialist_id,
        'title' => 'dr.',
        'str_number' => 'STR-DOC-SAME',
        'sip_number' => 'SIP-DOC-SAME',
    ]);

    $otherSpecialist = Specialist::create(['name' => 'Ortopedi']);
    $otherSpecialistDoctorUser = User::factory()->create([
        'name' => 'Dokter Spesialis Lain',
        'role' => User::ROLE_DOKTER,
    ]);
    $otherSpecialistDoctor = Doctor::create([
        'user_id' => $otherSpecialistDoctorUser->id,
        'specialist_id' => $otherSpecialist->id,
        'title' => 'dr.',
        'str_number' => 'STR-DOC-OTHER',
        'sip_number' => 'SIP-DOC-OTHER',
    ]);

    $approver = User::factory()->create(['role' => User::ROLE_PERAWAT_OK]);
    $requester = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);

    $createSchedule = function (Doctor $scheduleDoctor, string $patientName, string $procedure, string $medicalRecordNumber, string $startTime) use ($approver, $requester): void {
        $patient = Patient::create([
            'medical_record_number' => $medicalRecordNumber,
            'name' => $patientName,
            'gender' => 'Perempuan',
            'origin_room' => 'Bangsal',
            'created_by' => $requester->id,
        ]);
        $request = SurgeryRequest::create([
            'patient_id' => $patient->id,
            'diagnosis_text' => 'Diagnosis uji',
            'procedure_text' => $procedure,
            'requested_by' => $requester->id,
            'requested_doctor_id' => $scheduleDoctor->id,
            'requested_date' => '2026-05-22',
            'requested_start_time' => $startTime,
            'patient_priority' => 'urgent',
            'request_status' => 'disetujui',
        ]);
        $room = OperatingRoom::create([
            'room_code' => $medicalRecordNumber,
            'room_name' => "Kamar {$medicalRecordNumber}",
            'status' => 'siap',
            'capacity' => 1,
        ]);

        SurgerySchedule::create([
            'surgery_request_id' => $request->id,
            'patient_id' => $patient->id,
            'doctor_id' => $scheduleDoctor->id,
            'operating_room_id' => $room->id,
            'approved_by' => $approver->id,
            'surgery_date' => '2026-05-22',
            'start_time' => $startTime,
            'end_time' => '10:00',
            'schedule_status' => 'scheduled',
        ]);
    };

    $createSchedule($sameSpecialistDoctor, 'Pasien Spesialis Sama', 'Tindakan Sama', 'RM-SAME', '08:00');
    $createSchedule($otherSpecialistDoctor, 'Pasien Spesialis Lain', 'Tindakan Lain', 'RM-OTHER', '09:00');

    $this->actingAs($doctorUser)
        ->get(route('doctor.dashboard'))
        ->assertOk()
        ->assertSee('Jumlah Total Operasi')
        ->assertSee('Operasi Terjadwal Saya')
        ->assertSee('02')
        ->assertSee('01')
        ->assertSee('Pasien Spesialis Sama')
        ->assertSee('Dokter Spesialis Sama')
        ->assertSee('Tindakan Sama')
        ->assertSee('08:00')
        ->assertDontSee('Pasien Spesialis Lain')
        ->assertDontSee('Tindakan Lain');
});
