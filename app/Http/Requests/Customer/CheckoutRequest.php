<?php

namespace App\Http\Requests\Customer;

use App\Enums\DeliveryType;
use App\Enums\PaymentMethod;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'delivery_type' => ['required', Rule::enum(DeliveryType::class)],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'phone' => ['required', 'string', 'max:30'],
            'delivery_address' => ['required_if:delivery_type,delivery', 'nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_receipt' => [
                'required_if:payment_method,bank_transfer',
                'nullable',
                'file',
                'mimes:jpeg,jpg,png,webp,pdf',
                'max:5120',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payment_receipt.required_if' => 'Please upload your bank transfer receipt before placing the order.',
            'payment_receipt.mimes' => 'The receipt must be a JPG, PNG, WEBP, or PDF file.',
            'payment_receipt.max' => 'The receipt may not be larger than 5MB.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (
                $this->input('payment_method') === PaymentMethod::CASH_ON_DELIVERY->value
                && $this->input('delivery_type') !== DeliveryType::DELIVERY->value
            ) {
                $validator->errors()->add(
                    'payment_method',
                    'Cash on delivery is only available for home delivery orders.',
                );
            }

            if (
                $this->input('payment_method') === PaymentMethod::BANK_TRANSFER->value
                && $this->files->has('payment_receipt')
                && ! $this->file('payment_receipt') instanceof UploadedFile
            ) {
                $validator->errors()->add(
                    'payment_receipt',
                    'The receipt could not be uploaded. Please try again with a JPG, PNG, WEBP, or PDF under 5MB.',
                );
            }
        });
    }
}
