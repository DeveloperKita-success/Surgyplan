<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomOperationRequest;
use App\Models\Doctor;
use App\Models\Guideline;
use App\Models\Patient;
use App\Models\SurgeryRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RoomOperationRequestController extends Controller
{
    private function abortUnlessRegularNurse(): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        abort_unless($user && $user->isRegularNurse(), 403);
    }

    public function create(): View
    {
        $this->abortUnlessRegularNurse();

        return view('room-operation-requests.create', [
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

    public function store(StoreRoomOperationRequest $request): RedirectResponse
    {
        $this->abortUnlessRegularNurse();

        $data = $request->validated();

        $patient = DB::transaction(function () use ($data) {
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
                $patient->created_by = auth()->id();
            }

            $patient->save();

            $surgeryRequest = SurgeryRequest::create([
                'patient_id' => $patient->id,
                'diagnosis_text' => $data['diagnosis_text'],
                'procedure_text' => $data['procedure_text'],
                'requested_by' => auth()->id(),
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

            return $patient;
        });

        return redirect()
            ->route('nurse.regular.room-operation.create')
            ->with('status', 'Pengajuan operasi ruang berhasil disimpan.');
    }

    private function storeUploadedFile(string $field): ?string
    {
        $request = request();

        if (! $request->hasFile($field)) {
            return null;
        }

        return $request->file($field)->store('room-operation-requests', 'public');
    }
}
