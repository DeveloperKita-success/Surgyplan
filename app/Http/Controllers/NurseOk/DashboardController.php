<?php

namespace App\Http\Controllers\NurseOk;

use App\Http\Controllers\Controller;
use App\Models\OkVerificationChecklist;
use App\Models\OperatingRoom;
use App\Models\SurgeryRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        abort_unless($user->isOkNurse(), 403);

        $stats = [
            [
                'label' => 'Checklist Aktif',
                'value' => $this->formatStat(
                    SurgeryRequest::query()
                        ->whereIn('request_status', ['menunggu', 'ditunda'])
                        ->count()
                ),
            ],
            [
                'label' => 'Kamar Siap',
                'value' => $this->formatStat(
                    OperatingRoom::query()
                        ->where('status', 'siap')
                        ->count()
                ),
            ],
            [
                'label' => 'Verifikasi Hari Ini',
                'value' => $this->formatStat(
                    OkVerificationChecklist::query()
                        ->whereDate('created_at', today())
                        ->count()
                ),
            ],
            [
                'label' => 'Kasus Prioritas',
                'value' => $this->formatStat(
                    SurgeryRequest::query()
                        ->whereIn('request_status', ['menunggu', 'ditunda', 'disetujui'])
                        ->whereIn('patient_priority', ['Imminent', 'Cito', 'Urgent', 'imminent', 'cito', 'urgent'])
                        ->count()
                ),
            ],
        ];

        $checklistPatients = SurgeryRequest::query()
            ->with(['patient', 'preoperativeChecklist', 'preoperativeChecklistItems'])
            ->whereIn('request_status', ['menunggu', 'ditunda', 'disetujui'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (SurgeryRequest $surgeryRequest): array => $this->checklistPatientSummary($surgeryRequest));

        $operatingRooms = OperatingRoom::query()
            ->orderBy('room_name')
            ->limit(8)
            ->get()
            ->map(fn (OperatingRoom $room): array => [
                'room' => $room->room_code ?: $room->room_name,
                'name' => $room->room_name,
                'status' => $this->roomStatusLabel($room->status),
                'tone' => $this->roomStatusTone($room->status),
            ]);

        return view('nurse-ok.dashboard', compact('stats', 'checklistPatients', 'operatingRooms'));
    }

    private function formatStat(int $value): string
    {
        return str_pad((string) $value, 2, '0', STR_PAD_LEFT);
    }

    private function checklistPatientSummary(SurgeryRequest $surgeryRequest): array
    {
        $items = $surgeryRequest->preoperativeChecklistItems;

        if ($items->isNotEmpty()) {
            $completed = $items->where('value', true)->count();
            $total = $items->count();

            return [
                'patient' => $surgeryRequest->patient?->name ?? '-',
                'progress' => $completed === $total ? 'Lengkap' : "{$completed}/{$total} item",
                'tone' => $completed === $total ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700',
            ];
        }

        $checklist = $surgeryRequest->preoperativeChecklist;

        if (! $checklist) {
            return [
                'patient' => $surgeryRequest->patient?->name ?? '-',
                'progress' => 'Perlu review',
                'tone' => 'bg-rose-100 text-rose-700',
            ];
        }

        $requiredItems = [
            $checklist->surgical_consent,
            $checklist->anesthesia_consent,
            $checklist->lab_result_complete,
            $checklist->radiology_available,
            $checklist->anesthesia_consultation_done,
            $checklist->blood_available,
            $checklist->infusion_installed,
            $checklist->catheter_installed,
        ];

        $completed = collect($requiredItems)->filter()->count();
        $total = count($requiredItems);

        return [
            'patient' => $surgeryRequest->patient?->name ?? '-',
            'progress' => $completed === $total ? 'Lengkap' : "{$completed}/{$total} item",
            'tone' => $completed === $total ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700',
        ];
    }

    private function roomStatusLabel(string $status): string
    {
        return match ($status) {
            'siap' => 'Siap',
            'dipakai' => 'Dipakai',
            'perawatan' => 'Perawatan',
            'nonaktif' => 'Nonaktif',
            default => ucfirst($status),
        };
    }

    private function roomStatusTone(string $status): string
    {
        return match ($status) {
            'siap' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            'dipakai' => 'border-amber-200 bg-amber-50 text-amber-700',
            'perawatan' => 'border-rose-200 bg-rose-50 text-rose-700',
            'nonaktif' => 'border-slate-200 bg-slate-50 text-slate-600',
            default => 'border-slate-200 bg-white text-slate-600',
        };
    }
}
