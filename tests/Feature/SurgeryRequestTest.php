<?php

use App\Mail\DoctorScheduleConflictMail;
use App\Models\Doctor;
use App\Models\OperatingRoom;
use App\Models\Specialist;
use App\Models\SurgeryRequest;
use App\Models\SurgerySchedule;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function surgeryRequestPayload(Doctor $doctor): array
{
    return [
        'medical_record_number' => 'RM-001',
        'patient_name' => 'Budi Santoso',
        'birth_date' => '1990-01-01',
        'age' => 36,
        'gender' => 'Laki-laki',
        'origin_room' => 'IGD',
        'diagnosis_text' => 'K35.8 - Appendicitis akut',
        'procedure_text' => '47.01 - Appendektomi',
        'patient_priority' => 'Urgent',
        'requested_date' => '2026-05-20',
        'requested_start_time' => '10:30',
        'requested_end_time' => '12:00',
        'requested_doctor_id' => $doctor->id,
        'surgical_consent' => '0',
        'anesthesia_consent' => '0',
        'lab_result_complete' => '0',
        'radiology_available' => '0',
        'anesthesia_consultation_done' => 'Sudah dikonsultasikan',
        'anesthesia_risk_estimation' => 'Risiko rendah',
        'vital_sign_stable' => 'Stabil',
        'vital_sign_note' => 'Stabil',
        'blood_pressure' => '120/80',
        'fasting_more_than_6_hours' => 'Lebih dari 6 jam',
        'blood_type' => 'O',
        'blood_available' => 'Tersedia',
        'infusion_installed' => 'Terpasang',
        'catheter_installed' => 'Tidak terpasang',
        'surgical_area_shaved' => 'Sudah dilakukan',
        'jewelry_removed' => 'Terlepas',
    ];
}

function surgeryRequestUpdatePayload(Doctor $doctor): array
{
    return [
        ...surgeryRequestPayload($doctor),
        'patient_priority' => 'urgent',
        'anesthesia_consultation_done' => '1',
        'anesthesia_risk_estimation' => 'Risiko rendah',
        'vital_sign_stable' => '1',
        'fasting_more_than_6_hours' => '1',
        'blood_available' => '1',
        'infusion_installed' => '1',
        'catheter_installed' => '0',
        'surgical_area_shaved' => '1',
        'jewelry_removed' => '1',
        'has_previous_surgery' => '0',
    ];
}

it('allows only regular nurses to create surgery requests', function () {
    $specialist = Specialist::create(['name' => 'Bedah Umum']);
    $doctorUser = User::factory()->create(['role' => User::ROLE_DOKTER]);
    $doctor = Doctor::create([
        'user_id' => $doctorUser->id,
        'specialist_id' => $specialist->id,
        'title' => 'dr.',
        'str_number' => 'STR-1',
        'sip_number' => 'SIP-1',
    ]);
    $regularNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $okNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_OK]);

    $this->actingAs($regularNurse)
        ->post(route('nurse-regular.surgery-requests.store'), surgeryRequestPayload($doctor))
        ->assertRedirect(route('nurse-regular.surgery-requests.index'));

    $this->assertDatabaseHas('surgery_requests', [
        'requested_by' => $regularNurse->id,
        'request_status' => 'menunggu',
        'patient_priority' => 'Urgent',
        'requested_doctor_id' => $doctor->id,
    ]);

    $this->actingAs($okNurse)
        ->post(route('nurse-regular.surgery-requests.store'), array_merge(surgeryRequestPayload($doctor), [
            'medical_record_number' => 'RM-002',
        ]))
        ->assertForbidden();

    $this->actingAs($doctorUser)
        ->get(route('nurse-regular.surgery-requests.create'))
        ->assertForbidden();
});

it('lets regular nurses manage their waiting surgery requests', function () {
    $specialist = Specialist::create(['name' => 'Bedah Umum']);
    $doctorUser = User::factory()->create(['role' => User::ROLE_DOKTER]);
    $doctor = Doctor::create([
        'user_id' => $doctorUser->id,
        'specialist_id' => $specialist->id,
        'title' => 'dr.',
        'str_number' => 'STR-2',
        'sip_number' => 'SIP-2',
    ]);
    $regularNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);

    $this->actingAs($regularNurse)
        ->post(route('nurse-regular.surgery-requests.store'), surgeryRequestPayload($doctor));

    $surgeryRequest = SurgeryRequest::firstOrFail();

    $this->actingAs($regularNurse)
        ->get(route('nurse-regular.surgery-requests.index'))
        ->assertOk();

    $this->actingAs($regularNurse)
        ->get(route('nurse-regular.surgery-requests.index', ['status' => 'menunggu']))
        ->assertOk()
        ->assertSee('Budi Santoso');

    $this->actingAs($regularNurse)
        ->get(route('nurse-regular.surgery-requests.show', $surgeryRequest))
        ->assertOk();

    $this->actingAs($regularNurse)
        ->get(route('nurse-regular.surgery-requests.edit', $surgeryRequest))
        ->assertOk();

    $this->actingAs($regularNurse)
        ->put(route('nurse-regular.surgery-requests.update', $surgeryRequest), array_merge(
            surgeryRequestUpdatePayload($doctor),
            [
                'medical_record_number' => 'RM-001',
                'patient_name' => 'Budi Santoso Revisi',
                'patient_priority' => 'cito',
            ],
        ))
        ->assertRedirect(route('nurse-regular.surgery-requests.show', $surgeryRequest));

    $this->assertDatabaseHas('patients', [
        'id' => $surgeryRequest->patient_id,
        'name' => 'Budi Santoso Revisi',
    ]);

    $this->assertDatabaseHas('surgery_requests', [
        'id' => $surgeryRequest->id,
        'patient_priority' => 'cito',
    ]);

    $this->actingAs($regularNurse)
        ->delete(route('nurse-regular.surgery-requests.destroy', $surgeryRequest))
        ->assertRedirect(route('nurse-regular.surgery-requests.index'));

    $this->assertDatabaseMissing('surgery_requests', [
        'id' => $surgeryRequest->id,
    ]);
});

it('emails doctors when a new surgery request conflicts with their schedule', function () {
    Mail::fake();

    $specialist = Specialist::create(['name' => 'Bedah Umum']);
    $doctorUser = User::factory()->create(['role' => User::ROLE_DOKTER]);
    $doctor = Doctor::create([
        'user_id' => $doctorUser->id,
        'specialist_id' => $specialist->id,
        'title' => 'dr.',
        'str_number' => 'STR-3',
        'sip_number' => 'SIP-3',
    ]);
    $regularNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $okNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_OK]);
    $room = OperatingRoom::create([
        'specialist_id' => $specialist->id,
        'room_code' => 'OK-01',
        'room_name' => 'Kamar Operasi 01',
        'status' => 'siap',
        'capacity' => 1,
    ]);

    $existingRequest = SurgeryRequest::create([
        'patient_id' => \App\Models\Patient::create([
            'medical_record_number' => 'RM-EXISTING',
            'name' => 'Pasien Terjadwal',
            'gender' => 'Laki-laki',
            'origin_room' => 'IGD',
            'created_by' => $regularNurse->id,
        ])->id,
        'diagnosis_text' => 'K35.8',
        'procedure_text' => '47.01',
        'requested_by' => $regularNurse->id,
        'requested_doctor_id' => $doctor->id,
        'requested_date' => '2026-05-20',
        'requested_start_time' => '09:30',
        'requested_end_time' => '11:00',
        'patient_priority' => 'Urgent',
        'request_status' => 'disetujui',
    ]);

    SurgerySchedule::create([
        'surgery_request_id' => $existingRequest->id,
        'patient_id' => $existingRequest->patient_id,
        'doctor_id' => $doctor->id,
        'operating_room_id' => $room->id,
        'approved_by' => $okNurse->id,
        'surgery_date' => '2026-05-20',
        'start_time' => '09:30',
        'end_time' => '11:00',
        'schedule_status' => 'scheduled',
    ]);

    $this->actingAs($regularNurse)
        ->post(route('nurse-regular.surgery-requests.store'), surgeryRequestPayload($doctor))
        ->assertRedirect(route('nurse-regular.surgery-requests.index'));

    Mail::assertSent(DoctorScheduleConflictMail::class, function (DoctorScheduleConflictMail $mail) use ($doctorUser) {
        return $mail->hasTo($doctorUser->email)
            && $mail->conflictingSchedules->count() === 1;
    });

    $this->assertDatabaseHas('notifications', [
        'user_id' => $doctorUser->id,
        'title' => 'Jadwal dokter bentrok',
        'is_read' => false,
    ]);
});
