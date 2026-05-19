<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientPreoperativeChecklist;
use App\Models\Procedure;
use App\Models\SurgeryHistory;
use App\Models\SurgeryRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SurgeryRequestController extends Controller
{
    public function create(Request $request): View
    {
        $this->ensureRegularNurse($request->user());

        return view('surgery-requests.create', [
            'doctors' => Doctor::query()
                ->with('user')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function index(Request $request): View
    {
        $this->ensureRegularNurse($request->user());

        $status = $request->string('status')->toString();
        $allowedStatuses = ['menunggu', 'disetujui', 'ditolak'];

        $requests = $request->user()
            ->surgeryRequests()
            ->with(['patient', 'procedure', 'requestedDoctor.user'])
            ->when(in_array($status, $allowedStatuses, true), function ($query) use ($status): void {
                $query->where('request_status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('surgery-requests.index', [
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

        return view('surgery-requests.show', [
            'surgeryRequest' => $surgeryRequest->load([
                'patient',
                'diagnosis',
                'procedure',
                'requestedDoctor.user',
                'preoperativeChecklist',
                'surgeryHistories.changedBy',
            ]),
        ]);
    }

    public function edit(Request $request, SurgeryRequest $surgeryRequest): View
    {
        $this->ensureEditableByRegularNurse($request->user(), $surgeryRequest);

        return view('surgery-requests.edit', [
            'surgeryRequest' => $surgeryRequest->load([
                'patient',
                'diagnosis',
                'procedure',
                'preoperativeChecklist',
            ]),
            'doctors' => Doctor::query()
                ->with('user')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureRegularNurse($request->user());

        $validated = $request->validate([
            'medical_record_number' => ['required', 'string', 'max:255', 'unique:patients,medical_record_number'],
            'patient_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender' => ['required', 'in:Laki-laki,Perempuan'],
            'origin_room' => ['required', 'in:IGD,Bangsal,Poli'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:255'],
            'diagnosis_code' => ['nullable', 'string', 'max:255'],
            'diagnosis_name' => ['required', 'string', 'max:255'],
            'diagnosis_description' => ['nullable', 'string'],
            'procedure_code' => ['nullable', 'string', 'max:255'],
            'procedure_name' => ['required', 'string', 'max:255'],
            'procedure_description' => ['nullable', 'string'],
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

        DB::transaction(function () use ($validated, $request): void {
            $patient = Patient::create([
                'medical_record_number' => $validated['medical_record_number'],
                'name' => $validated['patient_name'],
                'birth_date' => $validated['birth_date'] ?? null,
                'age' => $validated['age'] ?? null,
                'gender' => $validated['gender'],
                'origin_room' => $validated['origin_room'],
                'address' => $validated['address'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            $diagnosis = Diagnosis::create([
                'code' => $validated['diagnosis_code'] ?? null,
                'name' => $validated['diagnosis_name'],
                'description' => $validated['diagnosis_description'] ?? null,
            ]);

            $procedure = Procedure::create([
                'code' => $validated['procedure_code'] ?? null,
                'name' => $validated['procedure_name'],
                'description' => $validated['procedure_description'] ?? null,
            ]);

            $surgeryRequest = SurgeryRequest::create([
                'patient_id' => $patient->id,
                'diagnosis_id' => $diagnosis->id,
                'procedure_id' => $procedure->id,
                'requested_by' => $request->user()->id,
                'requested_doctor_id' => $validated['requested_doctor_id'],
                'requested_date' => $validated['requested_date'],
                'requested_start_time' => $validated['requested_start_time'],
                'patient_priority' => $validated['patient_priority'],
                'request_status' => 'menunggu',
                'notes' => $validated['notes'] ?? null,
            ]);

            PatientPreoperativeChecklist::create([
                'surgery_request_id' => $surgeryRequest->id,
                'surgical_consent' => (bool) $validated['surgical_consent'],
                'surgical_consent_file' => $request->file('surgical_consent_file')?->store('checklists', 'public'),
                'anesthesia_consent' => (bool) $validated['anesthesia_consent'],
                'anesthesia_consent_file' => $request->file('anesthesia_consent_file')?->store('checklists', 'public'),
                'lab_result_complete' => (bool) $validated['lab_result_complete'],
                'lab_result_file' => $request->file('lab_result_file')?->store('checklists', 'public'),
                'radiology_available' => (bool) $validated['radiology_available'],
                'radiology_file' => $request->file('radiology_file')?->store('checklists', 'public'),
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
            ]);

            SurgeryHistory::create([
                'surgery_request_id' => $surgeryRequest->id,
                'changed_by' => $request->user()->id,
                'old_status' => null,
                'new_status' => 'menunggu',
                'note' => 'Pengajuan dibuat oleh perawat biasa.',
            ]);
        });

        return redirect()
            ->route('nurse-regular.surgery-requests.create')
            ->with('status', 'Pengajuan operasi berhasil dibuat dengan status menunggu.');
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

            $surgeryRequest->diagnosis?->update([
                'code' => $validated['diagnosis_code'] ?? null,
                'name' => $validated['diagnosis_name'],
                'description' => $validated['diagnosis_description'] ?? null,
            ]);

            $surgeryRequest->procedure?->update([
                'code' => $validated['procedure_code'] ?? null,
                'name' => $validated['procedure_name'],
                'description' => $validated['procedure_description'] ?? null,
            ]);

            $surgeryRequest->update([
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
            'medical_record_number' => ['required', 'string', 'max:255', 'unique:patients,medical_record_number,'.($patientId ?? 'NULL')],
            'patient_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender' => ['required', 'in:Laki-laki,Perempuan'],
            'origin_room' => ['required', 'in:IGD,Bangsal,Poli'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:255'],
            'diagnosis_code' => ['nullable', 'string', 'max:255'],
            'diagnosis_name' => ['required', 'string', 'max:255'],
            'diagnosis_description' => ['nullable', 'string'],
            'procedure_code' => ['nullable', 'string', 'max:255'],
            'procedure_name' => ['required', 'string', 'max:255'],
            'procedure_description' => ['nullable', 'string'],
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
