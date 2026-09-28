<?php

namespace App\Http\Requests\Admin;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route middleware + policies handle access
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[\d\s\+\-\(\)]{7,30}$/'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'service' => ['required', 'string', 'max:100'],
            'preferred_date' => ['nullable', 'date'],
            'preferred_time' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(Booking::STATUSES)],
            'source' => ['required', Rule::in(Booking::SOURCES)],
            'appointment_at' => ['nullable', 'date'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
            'assigned_to' => ['nullable', Rule::exists(User::class, 'id')->where('is_active', true)],
        ];
    }

    public function bookingData(): array
    {
        $data = $this->safe()->only([
            'name', 'phone', 'email', 'service', 'preferred_date', 'preferred_time', 'notes',
            'status', 'source', 'appointment_at', 'internal_notes', 'assigned_to',
        ]);

        foreach (['email', 'preferred_date', 'preferred_time', 'notes', 'appointment_at', 'internal_notes', 'assigned_to'] as $k) {
            if (array_key_exists($k, $data) && $data[$k] === '') {
                $data[$k] = null;
            }
        }

        if (($data['status'] ?? null) === 'contacted' && ! $this->route('booking')?->contacted_at) {
            $data['contacted_at'] = now();
        }

        return $data;
    }
}
