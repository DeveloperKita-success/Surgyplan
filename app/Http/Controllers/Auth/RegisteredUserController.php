<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Nurse;
use App\Models\Specialist;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    private const ROLE_UI_PERAWAT = 'perawat';

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'specialists' => Specialist::query()->orderBy('name')->get(['id', 'name']),
        ]);
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
            'role' => ['required', 'string', Rule::in([User::ROLE_DOKTER, self::ROLE_UI_PERAWAT])],

            // Dokter
            'specialist_id' => ['required_if:role,'.User::ROLE_DOKTER, 'nullable', 'integer', 'exists:specialists,id'],
            'title' => ['required_if:role,'.User::ROLE_DOKTER, 'nullable', 'string', 'max:255'],
            'str_number' => ['required_if:role,'.User::ROLE_DOKTER, 'nullable', 'string', 'max:100'],
            'sip_number' => ['required_if:role,'.User::ROLE_DOKTER, 'nullable', 'string', 'max:100', 'unique:doctors,sip_number'],

            // Perawat
            'nurse_type' => ['required_if:role,'.self::ROLE_UI_PERAWAT, 'nullable', 'string', Rule::in([User::ROLE_PERAWAT_OK, User::ROLE_PERAWAT_BIASA])],
            'origin_unit' => ['required_if:nurse_type,'.User::ROLE_PERAWAT_BIASA, 'nullable', 'string', Rule::in(['IGD', 'Bangsal', 'Poli'])],
        ]);

        $finalRole = $request->role === User::ROLE_DOKTER
            ? User::ROLE_DOKTER
            : $request->nurse_type;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $finalRole,
        ]);

        if ($user->role === User::ROLE_DOKTER) {
            Doctor::create([
                'user_id' => $user->id,
                'specialist_id' => $request->specialist_id,
                'title' => $request->title,
                'str_number' => $request->str_number,
                'sip_number' => $request->sip_number,
            ]);
        }

        if (in_array($user->role, [User::ROLE_PERAWAT_OK, User::ROLE_PERAWAT_BIASA], true)) {
            Nurse::create([
                'user_id' => $user->id,
                'nurse_type' => $user->role,
                'origin_unit' => $user->role === User::ROLE_PERAWAT_BIASA ? $request->origin_unit : null,
            ]);
        }

        event(new Registered($user));

        return redirect()
            ->route('login')
            ->with('status', 'Registrasi berhasil. Silakan login menggunakan akun Anda.');
    }
}
