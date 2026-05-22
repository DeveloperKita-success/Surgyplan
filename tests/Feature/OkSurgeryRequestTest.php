<?php

use App\Mail\DoctorScheduleCreatedMail;
use App\Mail\SurgeryRequestApprovedMail;
use App\Models\Doctor;
use App\Models\OkVerificationChecklist;
use App\Models\OperatingRoom;
use App\Models\Patient;
use App\Models\Specialist;
use App\Models\SurgeryRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function createOkRequestFixture(): array
{
    $specialist = Specialist::create(['name' => 'Bedah Umum']);
    $doctorUser = User::factory()->create(['role' => User::ROLE_DOKTER]);
    $doctor = Doctor::create([
        'user_id' => $doctorUser->id,
        'specialist_id' => $specialist->id,
        'title' => 'dr.',
        'str_number' => 'STR-OK-1',
        'sip_number' => 'SIP-OK-1',
    ]);
    $regularNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $okNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_OK]);
    $patient = Patient::create([
        'medical_record_number' => 'RM-OK-1',
        'name' => 'Pasien OK',
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
        'capacity' => 1,
    ]);

    return compact('okNurse', 'doctorUser', 'doctor', 'request', 'room');
}

it('lets ok nurses review and approve surgery requests', function () {
    Mail::fake();

    [
        'okNurse' => $okNurse,
        'doctorUser' => $doctorUser,
        'doctor' => $doctor,
        'request' => $request,
        'room' => $room,
    ] = createOkRequestFixture();

    $this->actingAs($okNurse)
        ->get(route('nurse-ok.requests.index'))
        ->assertOk()
        ->assertSee('Pasien OK');

    $this->actingAs($okNurse)
        ->get(route('nurse-ok.requests.show', $request))
        ->assertOk();

    $this->withSession(['_token' => 'test-token'])
        ->actingAs($okNurse)
        ->post(route('nurse-ok.requests.decide', $request), [
            '_token' => 'test-token',
            'patient_wristband_installed' => '1',
            'doctor_present' => '1',
            'oxygen_saturation' => '95%-100% (Normal)',
            'anesthesiologist_name' => 'dr. Anestesi',
            'anesthesia_type' => 'General Anesthesia / Anestesi Umum',
            'asa_status' => 'ASA II',
            'decision' => 'disetujui',
            'requested_doctor_id' => $doctor->id,
            'doctor_id' => $doctor->id,
            'operating_room_id' => $room->id,
            'end_time' => '12:00',
        ])
        ->assertRedirect(route('nurse-ok.requests.show', $request));

    $this->assertDatabaseHas('surgery_requests', [
        'id' => $request->id,
        'request_status' => 'disetujui',
        'requested_doctor_id' => $doctor->id,
    ]);

    Mail::assertSent(SurgeryRequestApprovedMail::class, function (SurgeryRequestApprovedMail $mail) use ($doctorUser) {
        return $mail->hasTo($doctorUser->email)
            && $mail->surgeryRequest->request_status === 'disetujui';
    });
});

it('blocks non ok nurses from reviewing surgery requests', function () {
    ['doctorUser' => $doctorUser, 'request' => $request] = createOkRequestFixture();

    $this->actingAs($doctorUser)
        ->get(route('nurse-ok.requests.show', $request))
        ->assertForbidden();
});

it('requires ok nurses to choose a doctor when reviewing surgery requests', function () {
    ['okNurse' => $okNurse, 'request' => $request] = createOkRequestFixture();

    $this->withSession(['_token' => 'test-token'])
        ->actingAs($okNurse)
        ->from(route('nurse-ok.requests.show', $request))
        ->post(route('nurse-ok.requests.decide', $request), [
            '_token' => 'test-token',
            'patient_wristband_installed' => '1',
            'doctor_present' => '1',
            'oxygen_saturation' => '95%-100% (Normal)',
            'anesthesia_type' => 'General Anesthesia / Anestesi Umum',
            'asa_status' => 'ASA II',
            'decision' => 'ditunda',
        ])
        ->assertRedirect(route('nurse-ok.requests.show', $request))
        ->assertSessionHasErrors(['requested_doctor_id']);
});

it('emails doctors when ok nurses create surgery schedules', function () {
    Mail::fake();

    [
        'okNurse' => $okNurse,
        'doctorUser' => $doctorUser,
        'request' => $request,
        'room' => $room,
    ] = createOkRequestFixture();

    $request->update([
        'request_status' => 'disetujui',
    ]);

    OkVerificationChecklist::create([
        'surgery_request_id' => $request->id,
        'verified_by' => $okNurse->id,
        'patient_wristband_installed' => true,
        'doctor_present' => true,
        'oxygen_saturation' => '95%-100% (Normal)',
        'operating_room_ready' => true,
        'anesthesia_type' => 'General Anesthesia / Anestesi Umum',
        'asa_status' => 'ASA II',
        'anesthesia_approved' => true,
        'doctor_anesthesia_approved' => true,
    ]);

    $this->withSession(['_token' => 'test-token'])
        ->actingAs($okNurse)
        ->post(route('nurse-ok.rooms.patient-scheduling.store'), [
            '_token' => 'test-token',
            'surgery_request_id' => $request->id,
            'operating_room_id' => $room->id,
            'end_time' => '12:00',
        ])
        ->assertRedirect(route('nurse-ok.schedules.index'));

    Mail::assertSent(DoctorScheduleCreatedMail::class, function (DoctorScheduleCreatedMail $mail) use ($doctorUser) {
        return $mail->hasTo($doctorUser->email)
            && $mail->surgerySchedule->surgery_request_id === $mail->surgerySchedule->surgeryRequest->id;
    });
});
