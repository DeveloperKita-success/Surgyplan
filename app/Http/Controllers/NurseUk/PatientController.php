<?php

namespace App\Http\Controllers\NurseUk;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\SurgeryRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PatientController extends Controller
{
    private function abortUnlessUkNurse(): void
    {
        $user = $this->currentUser();

        abort_unless($user && $user->role === User::ROLE_PERAWAT_UK, 403);
    }

    private function currentUser(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user;
    }

    public function index(): View
    {
        $this->abortUnlessUkNurse();

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

        return view('nurse-uk.patients.index', [
            'patients' => $patients,
            'query' => $query,
            'originRoom' => $originRoom,
            'gender' => $gender,
            'lastStatus' => $lastStatus,
            'statusOptions' => $statusOptions,
            'canManagePatients' => false,
        ]);
    }

    public function show(Patient $patient): View
    {
        $this->abortUnlessUkNurse();

        $patient->loadMissing([
            'createdBy',
            'latestSurgeryRequest.requestedBy',
            'latestSurgeryRequest.requestedDoctor.user',
            'surgeryRequests' => fn ($q) => $q->latest()->take(5),
        ]);

        return view('nurse-uk.patients.show', [
            'patient' => $patient,
            'canManagePatients' => false,
        ]);
    }

}
