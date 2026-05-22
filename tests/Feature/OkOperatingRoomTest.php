<?php

use App\Models\OperatingRoom;
use App\Models\Specialist;
use App\Models\User;

it('lets ok nurses manage operating rooms', function () {
    $okNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_OK]);
    $specialist = Specialist::create(['name' => 'Bedah Umum']);

    $this->actingAs($okNurse)
        ->get(route('nurse-ok.rooms.index'))
        ->assertOk();

    $this->actingAs($okNurse)
        ->get(route('nurse-ok.rooms.create'))
        ->assertOk();

    $this->actingAs($okNurse)
        ->post(route('nurse-ok.rooms.store'), [
            'room_code' => 'OK-01',
            'room_name' => 'Kamar Operasi 01',
            'specialist_id' => $specialist->id,
            'status' => 'siap',
            'capacity' => 1,
        ])
        ->assertRedirect();

    $room = OperatingRoom::firstOrFail();

    $this->assertDatabaseHas('operating_rooms', [
        'id' => $room->id,
        'room_code' => 'OK-01',
        'room_name' => 'Kamar Operasi 01',
        'status' => 'siap',
    ]);

    $this->actingAs($okNurse)
        ->get(route('nurse-ok.rooms.show', $room))
        ->assertOk()
        ->assertSee('Kamar Operasi 01');

    $this->actingAs($okNurse)
        ->get(route('nurse-ok.rooms.edit', $room))
        ->assertOk();

    $this->actingAs($okNurse)
        ->put(route('nurse-ok.rooms.update', $room), [
            'room_code' => 'OK-01',
            'room_name' => 'Kamar Operasi Utama',
            'specialist_id' => $specialist->id,
            'status' => 'perawatan',
            'capacity' => 1,
        ])
        ->assertRedirect(route('nurse-ok.rooms.show', $room));

    $this->assertDatabaseHas('operating_rooms', [
        'id' => $room->id,
        'room_name' => 'Kamar Operasi Utama',
        'status' => 'perawatan',
    ]);

    $this->actingAs($okNurse)
        ->delete(route('nurse-ok.rooms.destroy', $room))
        ->assertRedirect(route('nurse-ok.rooms.index'));

    $this->assertDatabaseMissing('operating_rooms', [
        'id' => $room->id,
    ]);
});

it('blocks non ok nurses from managing operating rooms', function () {
    $regularNurse = User::factory()->create(['role' => User::ROLE_PERAWAT_BIASA]);
    $room = OperatingRoom::create([
        'room_code' => 'OK-02',
        'room_name' => 'Kamar Operasi 02',
        'status' => 'siap',
        'capacity' => 1,
    ]);

    $this->actingAs($regularNurse)
        ->get(route('nurse-ok.rooms.index'))
        ->assertForbidden();

    $this->actingAs($regularNurse)
        ->post(route('nurse-ok.rooms.store'), [
            'room_code' => 'OK-03',
            'room_name' => 'Kamar Operasi 03',
            'status' => 'siap',
        ])
        ->assertForbidden();

    $this->actingAs($regularNurse)
        ->delete(route('nurse-ok.rooms.destroy', $room))
        ->assertForbidden();
});
