<?php

use App\Models\Patient;
use App\Models\User;

it('blocks doctors from patient data routes', function () {
    $regularNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $patient = Patient::create([
        'medical_record_number' => 'RM-PAT-001',
        'name' => 'Pasien Read Only',
        'gender' => 'Laki-laki',
        'origin_room' => 'IGD',
        'created_by' => $regularNurse->id,
    ]);
    $doctor = User::factory()->create(['role' => User::ROLE_DOKTER]);

    $this->actingAs($doctor)
        ->get(route('patients.index'))
        ->assertForbidden();

    $this->actingAs($doctor)
        ->get(route('patients.show', $patient))
        ->assertForbidden();
});

it('lets ok nurses view patients from their own patient routes', function () {
    $regularNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $okNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_OK]);
    $patient = Patient::create([
        'medical_record_number' => 'RM-PAT-OK-001',
        'name' => 'Pasien OK Read Only',
        'gender' => 'Laki-laki',
        'origin_room' => 'IGD',
        'created_by' => $regularNurse->id,
    ]);

    $this->actingAs($okNurse)
        ->get(route('nurse-ok.patients.index'))
        ->assertOk()
        ->assertSee('Pasien OK Read Only');

    $this->actingAs($okNurse)
        ->get(route('nurse-ok.patients.show', $patient))
        ->assertOk();
});

it('lets regular nurses manage patients', function () {
    $regularNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $patient = Patient::create([
        'medical_record_number' => 'RM-PAT-002',
        'name' => 'Pasien Kelola',
        'gender' => 'Laki-laki',
        'origin_room' => 'Bangsal',
        'created_by' => $regularNurse->id,
    ]);

    $this->actingAs($regularNurse)
        ->get(route('patients.create'))
        ->assertOk();

    $this->actingAs($regularNurse)
        ->post(route('patients.store'), [
            'medical_record_number' => 'RM-PAT-003',
            'name' => 'Pasien Baru',
            'gender' => 'Laki-laki',
            'origin_room' => 'IGD',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('patients', [
        'medical_record_number' => 'RM-PAT-003',
        'name' => 'Pasien Baru',
        'created_by' => $regularNurse->id,
    ]);

    $this->actingAs($regularNurse)
        ->get(route('patients.edit', $patient))
        ->assertOk();

    $this->actingAs($regularNurse)
        ->put(route('patients.update', $patient), [
            'medical_record_number' => 'RM-PAT-002',
            'name' => 'Pasien Kelola Revisi',
            'gender' => 'Perempuan',
            'origin_room' => 'Poli',
        ])
        ->assertRedirect(route('nurse-regular.patients.show', $patient));

    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'name' => 'Pasien Kelola Revisi',
        'gender' => 'Perempuan',
        'origin_room' => 'Poli',
    ]);

    $this->actingAs($regularNurse)
        ->delete(route('patients.destroy', $patient))
        ->assertRedirect(route('nurse-regular.patients.index'));

    $this->assertDatabaseMissing('patients', [
        'id' => $patient->id,
    ]);
});
