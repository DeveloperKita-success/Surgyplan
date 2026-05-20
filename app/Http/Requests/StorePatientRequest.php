<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'medical_record_number' => ['required', 'string', 'max:50', Rule::unique('patients', 'medical_record_number')],
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'age' => ['nullable', 'integer', 'min:0', 'max:130'],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'origin_room' => ['required', Rule::in(['IGD', 'Bangsal', 'Poli'])],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function attributes(): array
    {
        return [
            'medical_record_number' => 'nomor rekam medis',
            'birth_date' => 'tanggal lahir',
            'origin_room' => 'ruang asal',
        ];
    }
}
