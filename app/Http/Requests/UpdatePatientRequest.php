<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Patient $patient */
        $patient = $this->route('patient');

        return [
            'medical_record_number' => ['required', 'string', 'max:50', 'unique:patients,medical_record_number,'.$patient->id],
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'age' => ['nullable', 'integer', 'min:0', 'max:130'],
            'gender' => ['required', 'string', 'in:L,P'],
            'origin_room' => ['nullable', 'string', 'in:IGD,Bangsal,Poli'],
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
