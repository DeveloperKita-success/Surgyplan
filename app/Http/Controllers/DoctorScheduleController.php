<?php

namespace App\Http\Controllers;

use App\Models\SurgerySchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $doctor = $this->doctorFrom($request->user());
        $search = $request->string('q')->trim()->toString();
        $selectedDate = $request->date('date')?->format('Y-m-d');

        $sameSpecialistSchedules = SurgerySchedule::query()
            ->whereHas('doctor', function ($query) use ($doctor): void {
                $query->where('specialist_id', $doctor->specialist_id);
            });

        $schedules = (clone $sameSpecialistSchedules)
            ->with(['patient', 'doctor.user', 'operatingRoom', 'surgeryRequest'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereHas('doctor.user', function ($doctorQuery) use ($search): void {
                    $doctorQuery->where('name', 'like', "%{$search}%");
                });
            })
            ->when($selectedDate, function ($query) use ($selectedDate): void {
                $query->whereDate('surgery_date', $selectedDate);
            })
            ->orderBy('surgery_date')
            ->orderBy('start_time')
            ->paginate(10)
            ->withQueryString();

        return view('doctor.schedules.index', [
            'schedules' => $schedules,
            'search' => $search,
            'selectedDate' => $selectedDate,
        ]);
    }

    public function show(Request $request, SurgerySchedule $surgerySchedule): View
    {
        $doctor = $this->doctorFrom($request->user());
        $surgerySchedule->loadMissing('doctor');

        abort_unless($surgerySchedule->doctor?->specialist_id === $doctor->specialist_id, 403);

        return view('doctor.schedules.show', [
            'schedule' => $surgerySchedule->load([
                'patient',
                'doctor.user',
                'operatingRoom',
                'surgeryRequest',
                'surgeryRequest.preoperativeChecklist',
            ]),
        ]);
    }

    private function doctorFrom(?User $user)
    {
        abort_unless($user?->isDoctor(), 403);
        abort_unless($user->doctor, 403);

        return $user->doctor;
    }
}
