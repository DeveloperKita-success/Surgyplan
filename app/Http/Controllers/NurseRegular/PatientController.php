<?php

namespace App\Http\Controllers\NurseRegular;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\SurgeryRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PatientController extends Controller
{
    private function abortUnlessAllowedRole(): void
    {
        $user = $this->currentUser();

        abort_unless($user && in_array($user->role, [User::ROLE_DOKTER, User::ROLE_PERAWAT_BIASA, User::ROLE_PERAWAT_OK], true), 403);
    }

    private function abortUnlessRegularNurse(): void
    {
        abort_unless($this->currentUser()?->isRegularNurse(), 403);
    }

    private function currentUser(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user;
    }

    public function index(): View
    {
        $this->abortUnlessAllowedRole();

        $query = request()->string('q')->trim()->toString();

        $originRoom = request()->string('origin_room')->trim()->toString();
        $gender = request()->string('gender')->trim()->toString();
        $lastStatus = request()->string('last_status')->trim()->toString();

        $statusOptions = SurgeryRequest::query()
            ->select('request_status')
            ->distinct()
            ->orderBy('request_status')
            ->pluck('request_status')
            ->values();

        $patients = Patient::query()
            ->with('latestSurgeryRequest')
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('medical_record_number', 'like', "%{$query}%");
                });
            })
            ->when($originRoom !== '', fn ($builder) => $builder->where('origin_room', $originRoom))
            ->when($gender !== '', fn ($builder) => $builder->where('gender', $gender))
            ->when($lastStatus !== '', function ($builder) use ($lastStatus) {
                $builder->whereHas('latestSurgeryRequest', fn ($q) => $q->where('request_status', $lastStatus));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('nurse-regular.patients.index', [
            'patients' => $patients,
            'query' => $query,
            'originRoom' => $originRoom,
            'gender' => $gender,
            'lastStatus' => $lastStatus,
            'statusOptions' => $statusOptions,
            'canManagePatients' => $this->currentUser()?->isRegularNurse() ?? false,
        ]);
    }

    public function show(Patient $patient): View
    {
        $this->abortUnlessAllowedRole();

        $patient->loadMissing([
            'createdBy',
            'latestSurgeryRequest.requestedBy',
            'latestSurgeryRequest.requestedDoctor.user',
            'surgeryRequests' => fn ($q) => $q->latest()->take(5),
        ]);

        return view('nurse-regular.patients.show', [
            'patient' => $patient,
            'canManagePatients' => $this->currentUser()?->isRegularNurse() ?? false,
        ]);
    }

    public function create(): View
    {
        $this->abortUnlessRegularNurse();

        return view('nurse-regular.patients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->abortUnlessRegularNurse();

        $data = $request->validate([
            'medical_record_number' => ['required', 'string', 'max:50', Rule::unique('patients', 'medical_record_number')],
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'age' => ['nullable', 'integer', 'min:0', 'max:130'],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'origin_room' => ['required', Rule::in(['IGD', 'Bangsal', 'Poli'])],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $patient = Patient::create([
            ...$data,
            'created_by' => $this->currentUser()?->id,
        ]);

        return redirect()
            ->route('nurse-regular.patients.show', $patient)
            ->with('status', 'Data patient berhasil dibuat.');
    }

    public function edit(Patient $patient): View
    {
        $this->abortUnlessRegularNurse();

        return view('nurse-regular.patients.edit', [
            'patient' => $patient,
        ]);
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $this->abortUnlessRegularNurse();

        $data = $request->validate([
            'medical_record_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('patients', 'medical_record_number')->ignore($patient),
            ],
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'age' => ['nullable', 'integer', 'min:0', 'max:130'],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'origin_room' => ['required', Rule::in(['IGD', 'Bangsal', 'Poli'])],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        unset($data['created_by']);

        $patient->update($data);

        return redirect()
            ->route('nurse-regular.patients.show', $patient)
            ->with('status', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $this->abortUnlessRegularNurse();

        if ($patient->surgeryRequests()->exists()) {
            return redirect()
                ->back()
                ->with('status', 'Pasien tidak bisa dihapus karena sudah memiliki riwayat pengajuan.');
        }

        $patient->delete();

        return redirect()
            ->route('nurse-regular.patients.index')
            ->with('status', 'Data pasien berhasil dihapus.');
    }
}
