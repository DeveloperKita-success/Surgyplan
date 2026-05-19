<?php

namespace App\Http\Controllers;

use App\Models\OperationReport;
use App\Models\SurgerySchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $doctor = $this->doctorFrom($request->user());
        $status = $request->string('status')->toString();
        $allowedStatuses = ['scheduled', 'completed'];

        $schedules = $doctor->surgerySchedules()
            ->with(['patient', 'operatingRoom', 'surgeryRequest.procedure'])
            ->when(in_array($status, $allowedStatuses, true), function ($query) use ($status): void {
                $query->where('schedule_status', $status);
            })
            ->orderByDesc('surgery_date')
            ->orderByDesc('start_time')
            ->paginate(10)
            ->withQueryString();

        return view('doctor.schedules.index', [
            'schedules' => $schedules,
            'activeStatus' => in_array($status, $allowedStatuses, true) ? $status : null,
            'statusCounts' => [
                'all' => $doctor->surgerySchedules()->count(),
                'scheduled' => $doctor->surgerySchedules()->where('schedule_status', 'scheduled')->count(),
                'completed' => $doctor->surgerySchedules()->where('schedule_status', 'completed')->count(),
            ],
        ]);
    }

    public function show(Request $request, SurgerySchedule $surgerySchedule): View
    {
        $doctor = $this->doctorFrom($request->user());
        abort_unless($surgerySchedule->doctor_id === $doctor->id, 403);

        return view('doctor.schedules.show', [
            'schedule' => $surgerySchedule->load([
                'patient',
                'operatingRoom',
                'surgeryRequest.diagnosis',
                'surgeryRequest.procedure',
                'surgeryRequest.preoperativeChecklist',
                'operationReports',
            ]),
        ]);
    }

    public function reports(Request $request): View
    {
        $doctor = $this->doctorFrom($request->user());

        return view('doctor.reports.index', [
            'reports' => OperationReport::query()
                ->where('doctor_id', $doctor->id)
                ->with(['surgerySchedule.patient', 'surgerySchedule.surgeryRequest.procedure'])
                ->latest()
                ->paginate(10),
        ]);
    }

    private function doctorFrom(?User $user)
    {
        abort_unless($user?->isDoctor(), 403);
        abort_unless($user->doctor, 403);

        return $user->doctor;
    }
}
