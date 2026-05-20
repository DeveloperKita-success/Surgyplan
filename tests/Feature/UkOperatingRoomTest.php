<?php

use App\Models\OperatingRoom;
use App\Models\Specialist;
use App\Models\User;

it('lets uk nurses manage operating rooms', function () {
    $ukNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_UK]);
    $specialist = Specialist::create(['name' => 'Bedah Umum']);

    $this->actingAs($ukNurse)
        ->get(route('nurse-uk.rooms.index'))
        ->assertOk();

    $this->actingAs($ukNurse)
        ->get(route('nurse-uk.rooms.create'))
        ->assertOk();

    $this->actingAs($ukNurse)
        ->post(route('nurse-uk.rooms.store'), [
            'room_code' => 'OK-01',
            'room_name' => 'Kamar Operasi 01',
            'specialist_id' => $specialist->id,
            'status' => 'siap',
        ])
        ->assertRedirect();

    $room = OperatingRoom::firstOrFail();

    $this->assertDatabaseHas('operating_rooms', [
        'id' => $room->id,
        'room_code' => 'OK-01',
        'room_name' => 'Kamar Operasi 01',
        'status' => 'siap',
    ]);

    $this->actingAs($ukNurse)
        ->get(route('nurse-uk.rooms.show', $room))
        ->assertOk()
        ->assertSee('Kamar Operasi 01');

    $this->actingAs($ukNurse)
        ->get(route('nurse-uk.rooms.edit', $room))
        ->assertOk();

    $this->actingAs($ukNurse)
        ->put(route('nurse-uk.rooms.update', $room), [
            'room_code' => 'OK-01',
            'room_name' => 'Kamar Operasi Utama',
            'specialist_id' => $specialist->id,
            'status' => 'perawatan',
        ])
        ->assertRedirect(route('nurse-uk.rooms.show', $room));

    $this->assertDatabaseHas('operating_rooms', [
        'id' => $room->id,
        'room_name' => 'Kamar Operasi Utama',
        'status' => 'perawatan',
    ]);

    $this->actingAs($ukNurse)
        ->delete(route('nurse-uk.rooms.destroy', $room))
        ->assertRedirect(route('nurse-uk.rooms.index'));

    $this->assertDatabaseMissing('operating_rooms', [
        'id' => $room->id,
    ]);
});

it('blocks non uk nurses from managing operating rooms', function () {
    $regularNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $room = OperatingRoom::create([
        'room_code' => 'OK-02',
        'room_name' => 'Kamar Operasi 02',
        'status' => 'siap',
    ]);

    $this->actingAs($regularNurse)
        ->get(route('nurse-uk.rooms.index'))
        ->assertForbidden();

    $this->actingAs($regularNurse)
        ->post(route('nurse-uk.rooms.store'), [
            'room_code' => 'OK-03',
            'room_name' => 'Kamar Operasi 03',
            'status' => 'siap',
        ])
        ->assertForbidden();

    $this->actingAs($regularNurse)
        ->delete(route('nurse-uk.rooms.destroy', $room))
        ->assertForbidden();
});
