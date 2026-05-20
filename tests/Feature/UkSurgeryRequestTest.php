<?php

use App\Models\Doctor;
use App\Models\OperatingRoom;
use App\Models\Patient;
use App\Models\Specialist;
use App\Models\SurgeryRequest;
use App\Models\User;

function createUkRequestFixture(): array
{
    $specialist = Specialist::create(['name' => 'Bedah Umum']);
    $doctorUser = User::factory()->create(['role' => User::ROLE_DOKTER]);
    $doctor = Doctor::create([
        'user_id' => $doctorUser->id,
        'specialist_id' => $specialist->id,
        'title' => 'dr.',
        'str_number' => 'STR-UK-1',
        'sip_number' => 'SIP-UK-1',
    ]);
    $regularNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $ukNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_UK]);
    $patient = Patient::create([
        'medical_record_number' => 'RM-UK-1',
        'name' => 'Pasien UK',
        'gender' => 'Perempuan',
        'origin_room' => 'IGD',
        'created_by' => $regularNurse->id,
    ]);
    $request = SurgeryRequest::create([
        'patient_id' => $patient->id,
        'diagnosis_text' => 'K35.8 - Appendicitis akut',
        'procedure_text' => '47.01 - Appendektomi',
        'requested_by' => $regularNurse->id,
        'requested_doctor_id' => $doctor->id,
        'requested_date' => '2026-05-20',
        'requested_start_time' => '10:30',
        'patient_priority' => 'urgent',
        'request_status' => 'menunggu',
    ]);
    $room = OperatingRoom::create([
        'specialist_id' => $specialist->id,
        'room_code' => '1A',
        'room_name' => 'Kamar Operasi 1A',
        'status' => 'siap',
    ]);

    return compact('ukNurse', 'doctorUser', 'request', 'room');
}

it('lets uk nurses review and approve surgery requests', function () {
    ['ukNurse' => $ukNurse, 'request' => $request, 'room' => $room] = createUkRequestFixture();

    $this->actingAs($ukNurse)
        ->get(route('nurse-uk.requests.index'))
        ->assertOk()
        ->assertSee('Pasien UK');

    $this->actingAs($ukNurse)
        ->get(route('nurse-uk.requests.show', $request))
        ->assertOk();

    $this->actingAs($ukNurse)
        ->post(route('nurse-uk.requests.decide', $request), [
            'patient_wristband_installed' => '1',
            'doctor_present' => '1',
            'oxygen_saturation' => '99%',
            'operating_room_ready' => '1',
            'anesthesiologist_name' => 'dr. Anestesi',
            'anesthesia_type' => 'General',
            'asa_status' => 'II',
            'anesthesia_approved' => '1',
            'decision' => 'disetujui',
            'operating_room_id' => $room->id,
            'end_time' => '12:00',
        ])
        ->assertRedirect(route('nurse-uk.requests.show', $request));

    $this->assertDatabaseHas('surgery_requests', [
        'id' => $request->id,
        'request_status' => 'disetujui',
    ]);

    $this->assertDatabaseHas('surgery_schedules', [
        'surgery_request_id' => $request->id,
        'operating_room_id' => $room->id,
        'schedule_status' => 'scheduled',
    ]);
});

it('blocks non uk nurses from reviewing surgery requests', function () {
    ['doctorUser' => $doctorUser, 'request' => $request] = createUkRequestFixture();

    $this->actingAs($doctorUser)
        ->get(route('nurse-uk.requests.show', $request))
        ->assertForbidden();
});
