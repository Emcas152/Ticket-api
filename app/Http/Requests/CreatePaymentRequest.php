<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
            'provider' => ['required', Rule::in(['stripe', 'paypal'])],
            'payment_method_token' => ['nullable', 'string', 'max:255'],
            'provider_reference' => ['nullable', 'string', 'max:255'],
            'simulate_success' => ['sometimes', 'boolean'],
        ];
    }
}
