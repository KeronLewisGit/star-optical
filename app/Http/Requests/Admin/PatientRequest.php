<?php

namespace App\Http\Requests\Admin;

use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[\d\s\+\-\(\)]{7,30}$/'],
            'phone_alt' => ['nullable', 'string', 'max:30', 'regex:/^[\d\s\+\-\(\)]{7,30}$/'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today', 'after:1900-01-01'],
            'gender' => ['nullable', Rule::in(Patient::GENDERS)],
            'address' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'medical_notes' => ['nullable', 'string', 'max:5000'],
            'allergies' => ['nullable', 'string', 'max:2000'],
            'insurance_provider' => ['nullable', 'string', 'max:150'],
            'status' => ['required', Rule::in(Patient::STATUSES)],
        ];
    }

    public function patientData(): array
    {
        $data = array_map(fn ($v) => $v === '' ? null : $v, $this->safe()->all());
        $data['last_name'] = $data['last_name'] ?? '';
        if (isset($data['email'])) {
            $data['email'] = strtolower(trim($data['email']));
        }

        return $data;
    }
}
