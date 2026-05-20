<?php

use App\Models\Patient;
use App\Models\User;

it('lets doctors and uk nurses view patients without managing them', function () {
    $regularNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $patient = Patient::create([
        'medical_record_number' => 'RM-PAT-001',
        'name' => 'Pasien Read Only',
        'gender' => 'Laki-laki',
        'origin_room' => 'IGD',
        'created_by' => $regularNurse->id,
    ]);

    foreach ([User::ROLE_DOKTER, User::ROLE_PERAWAT_UK] as $role) {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)
            ->get(route('patients.index'))
            ->assertOk()
            ->assertSee('Pasien Read Only');

        $this->actingAs($user)
            ->get(route('patients.show', $patient))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('patients.create'))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('patients.store'), [
                'medical_record_number' => 'RM-PAT-FORBIDDEN',
                'name' => 'Tidak Boleh Dibuat',
                'gender' => 'Laki-laki',
                'origin_room' => 'IGD',
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('patients.edit', $patient))
            ->assertForbidden();

        $this->actingAs($user)
            ->put(route('patients.update', $patient), [
                'medical_record_number' => 'RM-PAT-001',
                'name' => 'Nama Tidak Boleh Berubah',
                'gender' => 'Perempuan',
                'origin_room' => 'Poli',
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('patients.destroy', $patient))
            ->assertForbidden();
    }
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
        ->assertRedirect(route('patients.show', $patient));

    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'name' => 'Pasien Kelola Revisi',
        'gender' => 'Perempuan',
        'origin_room' => 'Poli',
    ]);

    $this->actingAs($regularNurse)
        ->delete(route('patients.destroy', $patient))
        ->assertRedirect(route('patients.index'));

    $this->assertDatabaseMissing('patients', [
        'id' => $patient->id,
    ]);
});
