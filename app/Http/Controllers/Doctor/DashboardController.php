<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\SurgerySchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $doctor = $this->doctorFrom($request->user());

        $sameSpecialistSchedules = SurgerySchedule::query()
            ->whereHas('doctor', function ($query) use ($doctor): void {
                $query->where('specialist_id', $doctor->specialist_id);
            });

        $stats = [
            [
                'label' => 'Jumlah Total Operasi',
                'value' => $this->formatStat((clone $sameSpecialistSchedules)->count()),
                'note' => $doctor->specialist?->name ?? 'Spesialis dokter',
            ],
            [
                'label' => 'Operasi Terjadwal Saya',
                'value' => $this->formatStat(
                    $doctor->surgerySchedules()
                        ->where('schedule_status', 'scheduled')
                        ->count()
                ),
                'note' => 'Untuk akun dokter ini',
            ],
        ];

        $schedules = (clone $sameSpecialistSchedules)
            ->with(['patient', 'doctor.user', 'surgeryRequest'])
            ->where('schedule_status', 'scheduled')
            ->orderBy('surgery_date')
            ->orderBy('start_time')
            ->limit(8)
            ->get();

        return view('doctor.dashboard', compact('stats', 'schedules'));
    }

    private function doctorFrom(?User $user)
    {
        abort_unless($user?->isDoctor(), 403);
        abort_unless($user->doctor, 403);

        return $user->doctor()->with('specialist')->firstOrFail();
    }

    private function formatStat(int $value): string
    {
        return str_pad((string) $value, 2, '0', STR_PAD_LEFT);
    }
}
