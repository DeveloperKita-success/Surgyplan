<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// class StoreRoomOperationRequest extends FormRequest
// {
//     public function authorize(): bool
//     {
//         return true;
//     }

//     public function rules(): array
//     {
//         return [
//             'medical_record_number' => ['required', 'string', 'max:50'],
//             'patient_name' => ['required', 'string', 'max:255'],
//             'birth_date' => ['required', 'date', 'before_or_equal:today'],
//             'gender' => ['required', 'string', 'max:20'],
//             'origin_room' => ['required', Rule::in(['IGD', 'Bangsal', 'Poli'])],
//             'address' => ['nullable', 'string'],
//             'phone' => ['nullable', 'string', 'max:30'],
//             'diagnosis_text' => ['required', 'string', 'max:255'],
//             'procedure_text' => ['required', 'string', 'max:255'],
//             'requested_date' => ['required', 'date'],
//             'requested_start_time' => ['required', 'date_format:H:i'],
//             'requested_end_time' => ['nullable', 'date_format:H:i', 'after:requested_start_time'],
//             'requested_doctor_id' => ['nullable', 'exists:doctors,id'],
//             'patient_priority' => ['required', Rule::in(['Imminent', 'Cito', 'Urgent', 'Expedited', 'Elektif'])],
//             'surgical_consent' => ['required', 'boolean'],
//             'surgical_consent_file' => ['required_if:surgical_consent,1', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
//             'anesthesia_consent' => ['required', 'boolean'],
//             'anesthesia_consent_file' => ['required_if:anesthesia_consent,1', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
//             'lab_result_complete' => ['required', 'boolean'],
//             'lab_result_file' => ['required_if:lab_result_complete,1', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
//             'radiology_available' => ['required', 'boolean'],
//             'radiology_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
//             'anesthesia_consultation_done' => ['nullable', Rule::in(['Sudah dikonsultasikan'])],
//             'anesthesia_risk_estimation' => ['required', 'string', 'max:255'],
//             'vital_sign_stable' => ['required', Rule::in(['Beresiko', 'Stabil'])],
//             'vital_sign_note' => ['required_if:vital_sign_stable,Stabil', 'nullable', 'string', 'max:255'],
//             'blood_pressure' => ['required', 'string', 'max:255'],
//             'allergy' => ['nullable', 'string', 'max:255'],
//             'fasting_more_than_6_hours' => ['required', Rule::in(['Kurang dari 6 jam', 'Lebih dari 6 jam'])],
//             'blood_type' => ['required', 'string', 'max:10'],
//             'blood_available' => ['required', Rule::in(['Tersedia', 'Tidak tersedia'])],
//             'infusion_installed' => ['required', Rule::in(['Terpasang', 'Tidak terpasang'])],
//             'catheter_installed' => ['required', Rule::in(['Terpasang', 'Tidak terpasang'])],
//             'surgical_area_shaved' => ['required', Rule::in(['Belum dilakukan', 'Sudah dilakukan'])],
//             'jewelry_removed' => ['required', Rule::in(['Terlepas', 'Tidak terlepas'])],
//             'disease_history' => ['nullable', 'string'],
//             'current_medications' => ['nullable', 'string'],
//             'has_previous_surgery' => ['nullable', Rule::in(['Ada riwayat operasi'])],
//             'previous_surgery_note' => ['required_if:has_previous_surgery,Ada riwayat operasi', 'nullable', 'string'],
//             'previous_surgery_date' => ['required_if:has_previous_surgery,Ada riwayat operasi', 'nullable', 'date', 'before_or_equal:today'],
//             'final_note' => ['nullable', 'string'],
//         ];
//     }
// }