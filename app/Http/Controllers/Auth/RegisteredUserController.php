<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Nurse;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in([User::ROLE_DOKTER, User::ROLE_PERAWAT])],

            // Dokter
            'specialist' => ['required_if:role,'.User::ROLE_DOKTER, 'nullable', 'string', 'max:255'],
            'degree' => ['required_if:role,'.User::ROLE_DOKTER, 'nullable', 'string', 'max:255'],
            'sip_number' => ['required_if:role,'.User::ROLE_DOKTER, 'nullable', 'string', 'max:100', 'unique:doctors,sip_number'],
            'address' => ['required_if:role,'.User::ROLE_DOKTER, 'nullable', 'string', 'max:2000'],
            'education_history' => ['required_if:role,'.User::ROLE_DOKTER, 'nullable', 'string', 'max:5000'],

            // Perawat
            'nurse_type' => ['required_if:role,'.User::ROLE_PERAWAT, 'nullable', 'string', Rule::in(['ok', 'biasa'])],
            'unit_asal' => ['required_if:nurse_type,biasa', 'nullable', 'string', Rule::in(['IGD', 'Bangsal', 'Poli'])],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($user->role === User::ROLE_DOKTER) {
            Doctor::create([
                'user_id' => $user->id,
                'specialist' => $request->specialist,
                'degree' => $request->degree,
                'sip_number' => $request->sip_number,
                'address' => $request->address,
                'education_history' => $request->education_history,
            ]);
        }

        if ($user->role === User::ROLE_PERAWAT) {
            Nurse::create([
                'user_id' => $user->id,
                'type' => $request->nurse_type,
                'unit_asal' => $request->nurse_type === 'biasa' ? $request->unit_asal : null,
            ]);
        }

        event(new Registered($user));

        return redirect()
            ->route('login')
            ->with('status', 'Registrasi berhasil. Silakan login menggunakan akun Anda.');
    }
}
