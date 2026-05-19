<?php

namespace App\Http\Controllers;

use App\Models\OperatingRoom;
use App\Models\SurgeryHistory;
use App\Models\SurgeryRequest;
use App\Models\SurgerySchedule;
use App\Models\UkVerificationChecklist;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UkSurgeryRequestController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureUkNurse($request->user());
        $status = $request->string('status')->toString();
        $allowedStatuses = ['menunggu', 'disetujui', 'ditolak', 'ditunda'];

        $requests = SurgeryRequest::query()
            ->with(['patient', 'procedure', 'requestedDoctor.user'])
            ->when(in_array($status, $allowedStatuses, true), function ($query) use ($status): void {
                $query->where('request_status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('uk.requests.index', [
            'requests' => $requests,
            'activeStatus' => in_array($status, $allowedStatuses, true) ? $status : null,
            'statusCounts' => [
                'all' => SurgeryRequest::count(),
                'menunggu' => SurgeryRequest::where('request_status', 'menunggu')->count(),
                'disetujui' => SurgeryRequest::where('request_status', 'disetujui')->count(),
                'ditolak' => SurgeryRequest::where('request_status', 'ditolak')->count(),
                'ditunda' => SurgeryRequest::where('request_status', 'ditunda')->count(),
            ],
        ]);
    }

    public function show(Request $request, SurgeryRequest $surgeryRequest): View
    {
        $this->ensureUkNurse($request->user());

        return view('uk.requests.show', [
            'surgeryRequest' => $surgeryRequest->load([
                'patient',
                'diagnosis',
                'procedure',
                'requestedDoctor.user',
                'preoperativeChecklist',
                'ukVerificationChecklist',
                'surgerySchedules.operatingRoom',
            ]),
            'rooms' => OperatingRoom::query()
                ->with('specialist')
                ->orderBy('room_name')
                ->get(),
        ]);
    }

    public function decide(Request $request, SurgeryRequest $surgeryRequest): RedirectResponse
    {
        $this->ensureUkNurse($request->user());
        abort_unless(in_array($surgeryRequest->request_status, ['menunggu', 'ditunda'], true), 403);

        $validated = $request->validate([
            'patient_wristband_installed' => ['required', 'boolean'],
            'doctor_present' => ['required', 'boolean'],
            'oxygen_saturation' => ['nullable', 'string', 'max:255'],
            'operating_room_ready' => ['required', 'boolean'],
            'anesthesiologist_name' => ['nullable', 'string', 'max:255'],
            'anesthesia_type' => ['nullable', 'string', 'max:255'],
            'asa_status' => ['nullable', 'string', 'max:255'],
            'anesthesia_approved' => ['required', 'boolean'],
            'anesthesia_note' => ['nullable', 'string'],
            'verification_note' => ['nullable', 'string'],
            'decision' => ['required', 'in:disetujui,ditolak,ditunda'],
            'operating_room_id' => ['nullable', 'required_if:decision,disetujui', 'exists:operating_rooms,id'],
            'end_time' => ['nullable', 'required_if:decision,disetujui', 'date_format:H:i'],
            'reason' => ['nullable', 'required_if:decision,ditolak,ditunda', 'string'],
        ]);

        DB::transaction(function () use ($validated, $request, $surgeryRequest): void {
            UkVerificationChecklist::updateOrCreate(
                ['surgery_request_id' => $surgeryRequest->id],
                [
                    'verified_by' => $request->user()->id,
                    'patient_wristband_installed' => (bool) $validated['patient_wristband_installed'],
                    'doctor_present' => (bool) $validated['doctor_present'],
                    'oxygen_saturation' => $validated['oxygen_saturation'] ?? null,
                    'operating_room_ready' => (bool) $validated['operating_room_ready'],
                    'anesthesiologist_name' => $validated['anesthesiologist_name'] ?? null,
                    'anesthesia_type' => $validated['anesthesia_type'] ?? null,
                    'asa_status' => $validated['asa_status'] ?? null,
                    'anesthesia_approved' => (bool) $validated['anesthesia_approved'],
                    'anesthesia_note' => $validated['anesthesia_note'] ?? null,
                    'verification_note' => $validated['verification_note'] ?? null,
                ],
            );

            $oldStatus = $surgeryRequest->request_status;
            $surgeryRequest->update([
                'request_status' => $validated['decision'],
            ]);

            if ($validated['decision'] === 'disetujui') {
                SurgerySchedule::updateOrCreate(
                    ['surgery_request_id' => $surgeryRequest->id],
                    [
                        'patient_id' => $surgeryRequest->patient_id,
                        'doctor_id' => $surgeryRequest->requested_doctor_id,
                        'operating_room_id' => $validated['operating_room_id'],
                        'approved_by' => $request->user()->id,
                        'surgery_date' => $surgeryRequest->requested_date,
                        'start_time' => $surgeryRequest->requested_start_time,
                        'end_time' => $validated['end_time'],
                        'schedule_status' => 'scheduled',
                    ],
                );
            }

            SurgeryHistory::create([
                'surgery_request_id' => $surgeryRequest->id,
                'changed_by' => $request->user()->id,
                'old_status' => $oldStatus,
                'new_status' => $validated['decision'],
                'note' => $validated['reason'] ?? 'Verifikasi Perawat UK selesai.',
            ]);
        });

        return redirect()
            ->route('uk.requests.show', $surgeryRequest)
            ->with('status', 'Keputusan pengajuan berhasil disimpan.');
    }

    private function ensureUkNurse(?User $user): void
    {
        abort_unless($user?->isUkNurse(), 403);
    }
}
