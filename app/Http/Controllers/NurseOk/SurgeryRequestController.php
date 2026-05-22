<?php

namespace App\Http\Controllers\NurseOk;

use App\Http\Controllers\Controller;
use App\Mail\SurgeryRequestApprovedMail;
use App\Models\Doctor;
use App\Models\OkVerificationChecklist;
use App\Models\SurgeryHistory;
use App\Models\SurgeryRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SurgeryRequestController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureOkNurse($request->user());
        $status = $request->string('status')->toString();
        $allowedStatuses = ['menunggu', 'disetujui', 'ditolak', 'ditunda'];

        $requests = SurgeryRequest::query()
            ->with(['patient', 'requestedDoctor.user'])
            ->when(in_array($status, $allowedStatuses, true), function ($query) use ($status): void {
                $query->where('request_status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('nurse-ok.requests.index', [
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
        $this->ensureOkNurse($request->user());

        return view('nurse-ok.requests.show', [
            'surgeryRequest' => $surgeryRequest->load([
                'patient',
                'requestedDoctor.user',
                'preoperativeChecklist',
                'okVerificationChecklist',
                'surgerySchedules.operatingRoom',
            ]),
            'doctors' => Doctor::query()
                ->with(['user', 'specialist'])
                ->whereHas('user')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function decide(Request $request, SurgeryRequest $surgeryRequest): RedirectResponse
    {
        $this->ensureOkNurse($request->user());
        abort_unless(in_array($surgeryRequest->request_status, ['menunggu', 'ditunda'], true), 403);

        $validated = $request->validate(
            [
                'patient_wristband_installed' => ['required', 'boolean'],
                'doctor_present' => ['required', 'boolean'],
                'anesthesia_consent_signed' => ['nullable', 'boolean'],
                'oxygen_saturation' => ['required', 'in:95%-100% (Normal),90%-94% (Hipoksia ringan / batas bawah),< 90% (Hipoksia / gawat)'],
                'anesthesiologist_name' => ['nullable', 'string', 'max:255'],
                'anesthesia_type' => ['required', 'in:General Anesthesia / Anestesi Umum,Regional Anesthesia / Anestesi Regional,Spinal Anesthesia / Anestesi Spinal,Epidural Anesthesia / Anestesi Epidural,Local Anesthesia / Anestesi Lokal,Sedation / Sedasi,Lainnya'],
                'anesthesia_type_other' => ['nullable', 'required_if:anesthesia_type,Lainnya', 'string', 'max:255'],
                'asa_status' => ['required', 'in:ASA I,ASA II,ASA III,ASA IV,ASA V,ASA VI,Emergency'],
                'anesthesia_approved' => ['nullable', 'boolean'],
                'doctor_anesthesia_approved' => ['nullable', 'boolean'],
                'anesthesia_note' => ['nullable', 'string'],
                'verification_note' => ['nullable', 'string'],
                'decision' => ['required', 'in:disetujui,ditunda'],
                'requested_doctor_id' => ['required', 'exists:doctors,id'],
            ],
            [
                'requested_doctor_id.required' => 'Pilih dokter penanggung jawab sebelum menyimpan verifikasi.',
                'requested_doctor_id.exists' => 'Dokter yang dipilih tidak valid atau belum terdaftar.',
                'asa_status.required' => 'Pilih status ASA pasien.',
                'asa_status.in' => 'Status ASA yang dipilih tidak valid.',
                'anesthesia_type.required' => 'Pilih jenis anestesi.',
                'anesthesia_type.in' => 'Jenis anestesi yang dipilih tidak valid.',
                'anesthesia_type_other.required_if' => 'Isi jenis anestesi lainnya.',
                'oxygen_saturation.required' => 'Pilih saturasi oksigen.',
                'oxygen_saturation.in' => 'Saturasi oksigen yang dipilih tidak valid.',
                'patient_wristband_installed.required' => 'Pilih status gelang pasien.',
                'doctor_present.required' => 'Pilih status kehadiran dokter.',
                'decision.required' => 'Pilih status akhir pengajuan.',
                'decision.in' => 'Status akhir pengajuan tidak valid.',
            ],
            [
                'requested_doctor_id' => 'dokter penanggung jawab',
                'asa_status' => 'status ASA',
                'anesthesia_type' => 'jenis anestesi',
                'anesthesia_type_other' => 'jenis anestesi lainnya',
                'oxygen_saturation' => 'saturasi oksigen',
                'patient_wristband_installed' => 'gelang pasien',
                'doctor_present' => 'kehadiran dokter',
                'decision' => 'status akhir',
            ],
        );

        DB::transaction(function () use ($validated, $request, $surgeryRequest): void {
            $surgeryRequest->preoperativeChecklist?->update([
                'anesthesia_consent_signed' => (bool) ($validated['anesthesia_consent_signed'] ?? false),
            ]);

            OkVerificationChecklist::updateOrCreate(
                ['surgery_request_id' => $surgeryRequest->id],
                [
                    'verified_by' => $request->user()->id,
                    'patient_wristband_installed' => (bool) $validated['patient_wristband_installed'],
                    'doctor_present' => (bool) $validated['doctor_present'],
                    'oxygen_saturation' => $validated['oxygen_saturation'],
                    'operating_room_ready' => $validated['decision'] === 'disetujui',
                    'anesthesiologist_name' => $validated['anesthesiologist_name'] ?? null,
                    'anesthesia_type' => $validated['anesthesia_type'] === 'Lainnya'
                        ? $validated['anesthesia_type_other']
                        : $validated['anesthesia_type'],
                    'asa_status' => $validated['asa_status'],
                    'anesthesia_approved' => (bool) ($validated['anesthesia_approved'] ?? false),
                    'doctor_anesthesia_approved' => (bool) ($validated['doctor_anesthesia_approved'] ?? false),
                    'anesthesia_note' => $validated['anesthesia_note'] ?? null,
                    'verification_note' => $validated['verification_note'] ?? null,
                ],
            );

            $oldStatus = $surgeryRequest->request_status;
            $surgeryRequest->update([
                'request_status' => $validated['decision'],
                'requested_doctor_id' => $validated['requested_doctor_id'] ?? $surgeryRequest->requested_doctor_id,
            ]);

            SurgeryHistory::create([
                'surgery_request_id' => $surgeryRequest->id,
                'changed_by' => $request->user()->id,
                'old_status' => $oldStatus,
                'new_status' => $validated['decision'],
                'note' => $validated['verification_note'] ?? 'Verifikasi Perawat OK selesai.',
            ]);
        });

        if ($validated['decision'] === 'disetujui') {
            $this->notifyDoctorRequestApproved($surgeryRequest->fresh());
        }

        return redirect()
            ->route('nurse-ok.requests.show', $surgeryRequest)
            ->with('status', 'Keputusan pengajuan berhasil disimpan.');
    }

    private function notifyDoctorRequestApproved(?SurgeryRequest $surgeryRequest): void
    {
        if (! $surgeryRequest) {
            return;
        }

        $surgeryRequest->loadMissing(['patient', 'requestedDoctor.user']);
        $doctorUser = $surgeryRequest->requestedDoctor?->user;

        if (! $doctorUser) {
            return;
        }

        try {
            Mail::to($doctorUser->email)->send(
                new SurgeryRequestApprovedMail($surgeryRequest)
            );
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengirim email pengajuan operasi disetujui ke dokter.', [
                'surgery_request_id' => $surgeryRequest->id,
                'doctor_user_id' => $doctorUser->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function ensureOkNurse(?User $user): void
    {
        abort_unless($user?->isOkNurse(), 403);
    }
}
