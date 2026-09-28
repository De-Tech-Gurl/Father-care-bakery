<?php

namespace App\Http\Requests\Admin;

use App\Enums\InventoryReason;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdjustInventoryRequest extends FormRequest
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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity_change' => ['required', 'integer', 'not_in:0'],
            'reason' => ['required', Rule::enum(InventoryReason::class)],
            'reference' => ['nullable', 'string', 'max:255'],
        ];
    }
}
