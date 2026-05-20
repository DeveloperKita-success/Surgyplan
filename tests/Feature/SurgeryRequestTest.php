<?php

use App\Models\Doctor;
use App\Models\Specialist;
use App\Models\SurgeryRequest;
use App\Models\User;

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
        'patient_priority' => 'urgent',
        'requested_date' => '2026-05-20',
        'requested_start_time' => '10:30',
        'requested_doctor_id' => $doctor->id,
        'surgical_consent' => '1',
        'anesthesia_consent' => '1',
        'lab_result_complete' => '1',
        'radiology_available' => '1',
        'anesthesia_consultation_done' => '1',
        'vital_sign_stable' => '1',
        'fasting_more_than_6_hours' => '1',
        'blood_available' => '1',
        'infusion_installed' => '1',
        'catheter_installed' => '1',
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
    $ukNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_UK]);

    $this->actingAs($regularNurse)
        ->post(route('nurse-regular.surgery-requests.store'), surgeryRequestPayload($doctor))
        ->assertRedirect(route('nurse-regular.surgery-requests.create'));

    $this->assertDatabaseHas('surgery_requests', [
        'requested_by' => $regularNurse->id,
        'request_status' => 'menunggu',
        'patient_priority' => 'urgent',
    ]);

    $this->actingAs($ukNurse)
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
            surgeryRequestPayload($doctor),
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
