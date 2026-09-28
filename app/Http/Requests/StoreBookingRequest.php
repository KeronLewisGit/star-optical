<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validates the public "Book an appointment" form.
 *
 * Bot protection without a third-party captcha:
 *  - a hidden honeypot field ("website") that humans never fill in;
 *  - an encrypted "form opened at" timestamp: submissions faster than 3 seconds are rejected.
 * The route is additionally rate limited (see AppServiceProvider).
 */
class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:150', 'regex:/^[\pL\pM\s\'\-\.]+$/u'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[\d\s\+\-\(\)]{7,30}$/'],
            'email' => ['nullable', 'string', 'email:rfc', 'max:255'],
            'service' => ['required', 'string', Rule::in(Booking::SERVICES)],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today', 'before:+1 year'],
            'preferred_time' => ['nullable', 'string', Rule::in(Booking::TIMES)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'website' => ['prohibited'],              // honeypot
            'form_token' => ['required', 'string'],   // encrypted timestamp
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Please enter your name using letters only.',
            'phone.regex' => 'Please enter a valid phone number.',
            'website.prohibited' => 'Your submission could not be processed.',
            'form_token.required' => 'Your session expired. Please reload the page and try again.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->has('form_token')) {
                return;
            }
            try {
                $openedAt = (int) Crypt::decryptString($this->input('form_token'));
            } catch (\Throwable) {
                $v->errors()->add('form_token', 'Your session expired. Please reload the page and try again.');

                return;
            }
            $age = time() - $openedAt;
            if ($age < 3 || $age > 60 * 60 * 6) {
                $v->errors()->add('form_token', 'Please take a moment to review the form, then submit again.');
            }
        });
    }

    /** Ready-to-save attributes. */
    public function bookingData(): array
    {
        return [
            'name' => trim($this->input('name')),
            'phone' => $this->input('phone'),
            'email' => $this->filled('email') ? strtolower(trim($this->input('email'))) : null,
            'service' => $this->input('service'),
            'preferred_date' => $this->input('preferred_date') ?: null,
            'preferred_time' => $this->input('preferred_time') ?: null,
            'notes' => $this->filled('notes') ? trim($this->input('notes')) : null,
            'source' => 'website',
        ];
    }
}
