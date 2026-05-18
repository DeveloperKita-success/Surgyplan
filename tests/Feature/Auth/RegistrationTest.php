<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'perawat',
        'nurse_type' => 'perawat_uk',

        // Simulate browser behavior: hidden inputs submit empty strings,
        // then Laravel converts them to null via ConvertEmptyStringsToNull.
        'specialist_id' => '',
        'title' => '',
        'str_number' => '',
        'sip_number' => '',
        'origin_unit' => '',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('login', absolute: false));
});
