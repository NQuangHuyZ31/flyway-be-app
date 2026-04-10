<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic
    }

    public function rules(): array
    {
        return [
            'quantity_on_hand' => 'sometimes|required|numeric|min:0',
            'quantity_reserved' => 'sometimes|required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity_on_hand.numeric' => 'Quantity on hand must be a number',
            'quantity_reserved.numeric' => 'Quantity reserved must be a number',
        ];
    }
}
