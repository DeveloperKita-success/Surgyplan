<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Models\SurgeryRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PatientController extends Controller
{
    private function abortUnlessAllowedRole(): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        abort_unless($user && in_array($user->role, [User::ROLE_DOKTER, User::ROLE_PERAWAT_BIASA, User::ROLE_PERAWAT_UK], true), 403);
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

        return view('data_pasients.index', [
            'patients' => $patients,
            'query' => $query,
            'originRoom' => $originRoom,
            'gender' => $gender,
            'lastStatus' => $lastStatus,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function show(Patient $patient): View
    {
        $this->abortUnlessAllowedRole();

        $patient->loadMissing([
            'createdBy',
            'latestSurgeryRequest.diagnosis',
            'latestSurgeryRequest.procedure',
            'latestSurgeryRequest.requestedBy',
            'latestSurgeryRequest.requestedDoctor.user',
            'surgeryRequests' => fn ($q) => $q->latest()->take(5),
        ]);

        return view('data_pasients.show', [
            'patient' => $patient,
        ]);
    }

    public function edit(Patient $patient): View
    {
        $this->abortUnlessAllowedRole();

        return view('data_pasients.edit', [
            'patient' => $patient,
        ]);
    }

    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        $this->abortUnlessAllowedRole();

        $data = $request->validated();

        unset($data['created_by']);

        $patient->update($data);

        return redirect()
            ->route('patients.show', $patient)
            ->with('status', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $this->abortUnlessAllowedRole();

        if ($patient->surgeryRequests()->exists()) {
            return redirect()
                ->back()
                ->with('status', 'Pasien tidak bisa dihapus karena sudah memiliki riwayat pengajuan.');
        }

        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('status', 'Data pasien berhasil dihapus.');
    }
}
