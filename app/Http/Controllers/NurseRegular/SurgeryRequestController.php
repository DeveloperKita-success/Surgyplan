<?php

namespace App\Http\Controllers\NurseRegular;

use App\Http\Controllers\Controller;
use App\Mail\DoctorScheduleConflictMail;
use App\Models\Doctor;
use App\Models\Guideline;
use App\Models\Patient;
use App\Models\PatientPreoperativeChecklist;
use App\Models\SurgeryHistory;
use App\Models\SurgeryRequest;
use App\Models\SurgerySchedule;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SurgeryRequestController extends Controller
{
    private function abortUnlessRegularNurse(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless($user && $user->isRegularNurse(), 403);
    }

    public function create(): View
    {
        $this->abortUnlessRegularNurse();

        return view('nurse-regular.surgery-requests.create', [
            'doctors' => Doctor::query()
                ->with(['user', 'specialist'])
                ->orderBy('id')
                ->get(),
            'originRooms' => ['IGD', 'Bangsal', 'Poli'],
            'statusOptions' => [
                ['value' => 'Imminent', 'label' => 'Imminent', 'tone' => 'border-rose-200 bg-rose-50 text-rose-700'],
                ['value' => 'Cito', 'label' => 'Cito', 'tone' => 'border-orange-200 bg-orange-50 text-orange-700'],
                ['value' => 'Urgent', 'label' => 'Urgent', 'tone' => 'border-amber-200 bg-amber-50 text-amber-700'],
                ['value' => 'Expedited', 'label' => 'Expedited', 'tone' => 'border-sky-200 bg-sky-50 text-sky-700'],
                ['value' => 'Elektif', 'label' => 'Elektif', 'tone' => 'border-emerald-200 bg-emerald-50 text-emerald-700'],
            ],
            'icd10Guidelines' => Guideline::query()
                ->where('type', 'like', '%ICD-10%')
                ->latest()
                ->get(),
            'icd9Guidelines' => Guideline::query()
                ->where('type', 'like', '%ICD-9%')
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->abortUnlessRegularNurse();

        $data = $request->validate([
            'medical_record_number' => ['required', 'string', 'max:50'],
            'patient_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'string', 'max:20'],
            'origin_room' => ['required', Rule::in(['IGD', 'Bangsal', 'Poli'])],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'diagnosis_text' => ['required', 'string', 'max:255'],
            'procedure_text' => ['required', 'string', 'max:255'],
            'requested_date' => ['required', 'date'],
            'requested_start_time' => ['required', 'date_format:H:i'],
            'requested_end_time' => ['required', 'date_format:H:i', 'after:requested_start_time'],
            'requested_doctor_id' => ['nullable', 'exists:doctors,id'],
            'patient_priority' => ['required', Rule::in(['Imminent', 'Cito', 'Urgent', 'Expedited', 'Elektif'])],
            'surgical_consent' => ['required', 'boolean'],
            'surgical_consent_file' => ['required_if:surgical_consent,1', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'anesthesia_consent' => ['required', 'boolean'],
            'anesthesia_consent_file' => ['required_if:anesthesia_consent,1', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'lab_result_complete' => ['required', 'boolean'],
            'lab_result_file' => ['required_if:lab_result_complete,1', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'radiology_available' => ['required', 'boolean'],
            'radiology_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'anesthesia_consultation_done' => ['nullable', Rule::in(['Sudah dikonsultasikan'])],
            'anesthesia_risk_estimation' => ['required', 'string', 'max:255'],
            'vital_sign_stable' => ['required', Rule::in(['Beresiko', 'Stabil'])],
            'vital_sign_note' => ['required_if:vital_sign_stable,Stabil', 'nullable', 'string', 'max:255'],
            'blood_pressure' => ['required', 'string', 'max:255'],
            'allergy' => ['nullable', 'string', 'max:255'],
            'fasting_more_than_6_hours' => ['required', Rule::in(['Kurang dari 6 jam', 'Lebih dari 6 jam'])],
            'blood_type' => ['required', 'string', 'max:10'],
            'blood_available' => ['required', Rule::in(['Tersedia', 'Tidak tersedia'])],
            'infusion_installed' => ['required', Rule::in(['Terpasang', 'Tidak terpasang'])],
            'catheter_installed' => ['required', Rule::in(['Terpasang', 'Tidak terpasang'])],
            'surgical_area_shaved' => ['required', Rule::in(['Belum dilakukan', 'Sudah dilakukan'])],
            'jewelry_removed' => ['required', Rule::in(['Terlepas', 'Tidak terlepas'])],
            'disease_history' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
            'has_previous_surgery' => ['nullable', Rule::in(['Ada riwayat operasi'])],
            'previous_surgery_note' => ['required_if:has_previous_surgery,Ada riwayat operasi', 'nullable', 'string'],
            'previous_surgery_date' => ['required_if:has_previous_surgery,Ada riwayat operasi', 'nullable', 'date', 'before_or_equal:today'],
            'final_note' => ['nullable', 'string'],
        ]);

        $surgeryRequest = DB::transaction(function () use ($data) {
            $patient = Patient::firstOrNew([
                'medical_record_number' => $data['medical_record_number'],
            ]);

            $patient->fill([
                'name' => $data['patient_name'],
                'birth_date' => $data['birth_date'],
                'age' => Carbon::parse($data['birth_date'])->age,
                'gender' => $data['gender'],
                'origin_room' => $data['origin_room'],
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]);

            if (! $patient->exists) {
                $patient->created_by = Auth::id();
            }

            $patient->save();

            $surgeryRequest = SurgeryRequest::create([
                'patient_id' => $patient->id,
                'diagnosis_text' => $data['diagnosis_text'],
                'procedure_text' => $data['procedure_text'],
                'requested_by' => Auth::id(),
                'requested_doctor_id' => $data['requested_doctor_id'] ?? null,
                'requested_date' => $data['requested_date'],
                'requested_start_time' => $data['requested_start_time'],
                'requested_end_time' => $data['requested_end_time'],
                'patient_priority' => $data['patient_priority'],
                'request_status' => 'menunggu',
                'notes' => $data['final_note'] ?? null,
            ]);

            $surgeryRequest->preoperativeChecklist()->create([
                'surgical_consent' => (bool) $data['surgical_consent'],
                'surgical_consent_file' => $this->storeUploadedFile('surgical_consent_file'),
                'anesthesia_consent' => (bool) $data['anesthesia_consent'],
                'anesthesia_consent_file' => $this->storeUploadedFile('anesthesia_consent_file'),
                'lab_result_complete' => (bool) $data['lab_result_complete'],
                'lab_result_file' => $this->storeUploadedFile('lab_result_file'),
                'radiology_available' => (bool) $data['radiology_available'],
                'radiology_file' => $this->storeUploadedFile('radiology_file'),
                'anesthesia_consultation_done' => isset($data['anesthesia_consultation_done']),
                'anesthesia_risk_estimation' => $data['anesthesia_risk_estimation'],
                'vital_sign_stable' => $data['vital_sign_stable'] === 'Stabil',
                'vital_sign_note' => $data['vital_sign_stable'] === 'Stabil' ? ($data['vital_sign_note'] ?? null) : null,
                'blood_pressure' => $data['blood_pressure'],
                'allergy' => $data['allergy'] ?? null,
                'fasting_more_than_6_hours' => $data['fasting_more_than_6_hours'] === 'Lebih dari 6 jam',
                'blood_type' => $data['blood_type'],
                'blood_available' => $data['blood_available'] === 'Tersedia',
                'infusion_installed' => $data['infusion_installed'] === 'Terpasang',
                'catheter_installed' => $data['catheter_installed'] === 'Terpasang',
                'surgical_area_shaved' => $data['surgical_area_shaved'] === 'Sudah dilakukan',
                'jewelry_removed' => $data['jewelry_removed'] === 'Terlepas',
                'disease_history' => $data['disease_history'] ?? null,
                'current_medications' => $data['current_medications'] ?? null,
                'has_previous_surgery' => isset($data['has_previous_surgery']),
                'previous_surgery_note' => $data['previous_surgery_note'] ?? null,
                'previous_surgery_date' => $data['previous_surgery_date'] ?? null,
                'final_note' => $data['final_note'] ?? null,
            ]);

            return $surgeryRequest;
        });

        $this->notifyDoctorIfScheduleConflict($surgeryRequest);

        return redirect()
            ->route('nurse-regular.surgery-requests.index')
            ->with('status', 'Pengajuan operasi ruang berhasil disimpan.');
    }

    private function notifyDoctorIfScheduleConflict(SurgeryRequest $surgeryRequest): void
    {
        if (! $surgeryRequest->requested_doctor_id || ! $surgeryRequest->requested_end_time) {
            return;
        }

        $conflictingSchedules = SurgerySchedule::query()
            ->with(['patient', 'operatingRoom'])
            ->where('doctor_id', $surgeryRequest->requested_doctor_id)
            ->whereDate('surgery_date', $surgeryRequest->requested_date)
            ->where('schedule_status', 'scheduled')
            ->where('start_time', '<', $surgeryRequest->requested_end_time)
            ->where('end_time', '>', $surgeryRequest->requested_start_time)
            ->orderBy('start_time')
            ->get();

        if ($conflictingSchedules->isEmpty()) {
            return;
        }

        $surgeryRequest->loadMissing(['patient', 'requestedDoctor.user']);
        $doctorUser = $surgeryRequest->requestedDoctor?->user;

        if (! $doctorUser) {
            return;
        }

        UserNotification::create([
            'user_id' => $doctorUser->id,
            'title' => 'Jadwal dokter bentrok',
            'message' => sprintf(
                'Pengajuan operasi pasien %s pada %s pukul %s-%s bentrok dengan jadwal dokter yang sudah ada.',
                $surgeryRequest->patient?->name ?? '-',
                $surgeryRequest->requested_date?->format('d M Y') ?? '-',
                substr((string) $surgeryRequest->requested_start_time, 0, 5),
                substr((string) $surgeryRequest->requested_end_time, 0, 5),
            ),
        ]);

        try {
            Mail::to($doctorUser->email)->send(
                new DoctorScheduleConflictMail($surgeryRequest, $conflictingSchedules)
            );
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengirim email bentrok jadwal dokter.', [
                'surgery_request_id' => $surgeryRequest->id,
                'doctor_user_id' => $doctorUser->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function storeUploadedFile(string $field): ?string
    {
        $request = request();

        if (! $request->hasFile($field)) {
            return null;
        }

        return $request->file($field)->store('room-operation-requests', 'public');
    }

    public function index(Request $request): View
    {
        $this->ensureRegularNurse($request->user());

        $status = $request->string('status')->toString();
        $allowedStatuses = ['menunggu', 'disetujui', 'ditolak'];

        $requests = $request->user()
            ->surgeryRequests()
            ->with(['patient', 'requestedDoctor.user'])
            ->when(in_array($status, $allowedStatuses, true), function ($query) use ($status): void {
                $query->where('request_status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('nurse-regular.surgery-requests.index', [
            'requests' => $requests,
            'activeStatus' => in_array($status, $allowedStatuses, true) ? $status : null,
            'statusCounts' => [
                'all' => $request->user()->surgeryRequests()->count(),
                'menunggu' => $request->user()->surgeryRequests()->where('request_status', 'menunggu')->count(),
                'disetujui' => $request->user()->surgeryRequests()->where('request_status', 'disetujui')->count(),
                'ditolak' => $request->user()->surgeryRequests()->where('request_status', 'ditolak')->count(),
            ],
        ]);
    }

    public function show(Request $request, SurgeryRequest $surgeryRequest): View
    {
        $this->ensureOwnedByRegularNurse($request->user(), $surgeryRequest);

        return view('nurse-regular.surgery-requests.show', [
            'surgeryRequest' => $surgeryRequest->load([
                'patient',
                'requestedDoctor.user',
                'preoperativeChecklist',
                'surgeryHistories.changedBy',
            ]),
        ]);
    }

    public function edit(Request $request, SurgeryRequest $surgeryRequest): View
    {
        $this->ensureEditableByRegularNurse($request->user(), $surgeryRequest);

        return view('nurse-regular.surgery-requests.edit', [
            'surgeryRequest' => $surgeryRequest->load([
                'patient',
                'preoperativeChecklist',
            ]),
            'doctors' => Doctor::query()
                ->with('user')
                ->orderBy('id')
                ->get(),
        ]);
    }



    public function update(Request $request, SurgeryRequest $surgeryRequest): RedirectResponse
    {
        $this->ensureEditableByRegularNurse($request->user(), $surgeryRequest);

        $validated = $this->validatedPayload($request, $surgeryRequest);

        DB::transaction(function () use ($validated, $request, $surgeryRequest): void {
            $surgeryRequest->patient->update([
                'medical_record_number' => $validated['medical_record_number'],
                'name' => $validated['patient_name'],
                'birth_date' => $validated['birth_date'] ?? null,
                'age' => $validated['age'] ?? null,
                'gender' => $validated['gender'],
                'origin_room' => $validated['origin_room'],
                'address' => $validated['address'] ?? null,
                'phone' => $validated['phone'] ?? null,
            ]);

            $surgeryRequest->update([
                'diagnosis_text' => $validated['diagnosis_text'],
                'procedure_text' => $validated['procedure_text'],
                'requested_doctor_id' => $validated['requested_doctor_id'],
                'requested_date' => $validated['requested_date'],
                'requested_start_time' => $validated['requested_start_time'],
                'patient_priority' => $validated['patient_priority'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $checklist = $surgeryRequest->preoperativeChecklist;
            $checklist?->update($this->checklistPayload($validated, $request, $checklist));

            SurgeryHistory::create([
                'surgery_request_id' => $surgeryRequest->id,
                'changed_by' => $request->user()->id,
                'old_status' => $surgeryRequest->request_status,
                'new_status' => $surgeryRequest->request_status,
                'note' => 'Pengajuan diperbarui oleh perawat biasa.',
            ]);
        });

        return redirect()
            ->route('nurse-regular.surgery-requests.show', $surgeryRequest)
            ->with('status', 'Pengajuan berhasil diperbarui.');
    }

    public function destroy(Request $request, SurgeryRequest $surgeryRequest): RedirectResponse
    {
        $this->ensureEditableByRegularNurse($request->user(), $surgeryRequest);

        abort_if($surgeryRequest->surgerySchedules()->exists(), 409);

        DB::transaction(function () use ($surgeryRequest): void {
            $surgeryRequest->preoperativeChecklist?->delete();
            $surgeryRequest->surgeryHistories()->delete();
            $surgeryRequest->delete();
        });

        return redirect()
            ->route('nurse-regular.surgery-requests.index')
            ->with('status', 'Pengajuan berhasil dihapus.');
    }

    private function ensureRegularNurse(?User $user): void
    {
        abort_unless($user?->isRegularNurse(), 403);
    }

    private function ensureOwnedByRegularNurse(?User $user, SurgeryRequest $surgeryRequest): void
    {
        $this->ensureRegularNurse($user);
        abort_unless($surgeryRequest->requested_by === $user?->id, 403);
    }

    private function ensureEditableByRegularNurse(?User $user, SurgeryRequest $surgeryRequest): void
    {
        $this->ensureOwnedByRegularNurse($user, $surgeryRequest);
        abort_unless($surgeryRequest->request_status === 'menunggu', 403);
    }

    private function validatedPayload(Request $request, ?SurgeryRequest $surgeryRequest = null): array
    {
        $patientId = $surgeryRequest?->patient_id;

        return $request->validate([
            'medical_record_number' => ['required', 'string', 'max:255', 'unique:patients,medical_record_number,' . ($patientId ?? 'NULL')],
            'patient_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender' => ['required', 'in:Laki-laki,Perempuan'],
            'origin_room' => ['required', 'in:IGD,Bangsal,Poli'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:255'],
            'diagnosis_text' => ['required', 'string', 'max:255'],
            'procedure_text' => ['required', 'string', 'max:255'],
            'patient_priority' => ['required', 'in:imminent,cito,urgent,expedited,elektif'],
            'requested_date' => ['required', 'date'],
            'requested_start_time' => ['required', 'date_format:H:i'],
            'requested_doctor_id' => ['required', 'exists:doctors,id'],
            'notes' => ['nullable', 'string'],
            'surgical_consent' => ['required', 'boolean'],
            'surgical_consent_file' => ['nullable', 'file', 'max:2048'],
            'anesthesia_consent' => ['required', 'boolean'],
            'anesthesia_consent_file' => ['nullable', 'file', 'max:2048'],
            'lab_result_complete' => ['required', 'boolean'],
            'lab_result_file' => ['nullable', 'file', 'max:2048'],
            'radiology_available' => ['required', 'boolean'],
            'radiology_file' => ['nullable', 'file', 'max:2048'],
            'anesthesia_consultation_done' => ['required', 'boolean'],
            'anesthesia_risk_estimation' => ['nullable', 'string'],
            'vital_sign_stable' => ['required', 'boolean'],
            'vital_sign_note' => ['nullable', 'string'],
            'blood_pressure' => ['nullable', 'string', 'max:255'],
            'allergy' => ['nullable', 'string'],
            'fasting_more_than_6_hours' => ['required', 'boolean'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'blood_available' => ['required', 'boolean'],
            'infusion_installed' => ['required', 'boolean'],
            'catheter_installed' => ['required', 'boolean'],
            'surgical_area_shaved' => ['required', 'boolean'],
            'jewelry_removed' => ['required', 'boolean'],
            'disease_history' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
            'has_previous_surgery' => ['required', 'boolean'],
            'previous_surgery_note' => ['nullable', 'string'],
            'previous_surgery_date' => ['nullable', 'date'],
            'final_note' => ['nullable', 'string'],
        ]);
    }

    private function checklistPayload(array $validated, Request $request, PatientPreoperativeChecklist $checklist): array
    {
        return [
            'surgical_consent' => (bool) $validated['surgical_consent'],
            'surgical_consent_file' => $request->file('surgical_consent_file')?->store('checklists', 'public') ?? $checklist->surgical_consent_file,
            'anesthesia_consent' => (bool) $validated['anesthesia_consent'],
            'anesthesia_consent_file' => $request->file('anesthesia_consent_file')?->store('checklists', 'public') ?? $checklist->anesthesia_consent_file,
            'lab_result_complete' => (bool) $validated['lab_result_complete'],
            'lab_result_file' => $request->file('lab_result_file')?->store('checklists', 'public') ?? $checklist->lab_result_file,
            'radiology_available' => (bool) $validated['radiology_available'],
            'radiology_file' => $request->file('radiology_file')?->store('checklists', 'public') ?? $checklist->radiology_file,
            'anesthesia_consultation_done' => (bool) $validated['anesthesia_consultation_done'],
            'anesthesia_risk_estimation' => $validated['anesthesia_risk_estimation'] ?? null,
            'vital_sign_stable' => (bool) $validated['vital_sign_stable'],
            'vital_sign_note' => $validated['vital_sign_note'] ?? null,
            'blood_pressure' => $validated['blood_pressure'] ?? null,
            'allergy' => $validated['allergy'] ?? null,
            'fasting_more_than_6_hours' => (bool) $validated['fasting_more_than_6_hours'],
            'blood_type' => $validated['blood_type'] ?? null,
            'blood_available' => (bool) $validated['blood_available'],
            'infusion_installed' => (bool) $validated['infusion_installed'],
            'catheter_installed' => (bool) $validated['catheter_installed'],
            'surgical_area_shaved' => (bool) $validated['surgical_area_shaved'],
            'jewelry_removed' => (bool) $validated['jewelry_removed'],
            'disease_history' => $validated['disease_history'] ?? null,
            'current_medications' => $validated['current_medications'] ?? null,
            'has_previous_surgery' => (bool) $validated['has_previous_surgery'],
            'previous_surgery_note' => $validated['previous_surgery_note'] ?? null,
            'previous_surgery_date' => $validated['previous_surgery_date'] ?? null,
            'final_note' => $validated['final_note'] ?? null,
        ];
    }
}
