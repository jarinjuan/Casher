<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvestmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:stock,crypto'],
            'symbol' => ['required', 'string', 'max:15'],
            'name' => ['nullable', 'string', 'max:100'],
            'external_id' => ['nullable', 'string', 'max:100'],
            'buy_mode' => ['required', 'in:quantity,amount'],
            'quantity' => ['required_if:buy_mode,quantity', 'nullable', 'numeric', 'min:0.00000001'],
            'amount' => ['required_if:buy_mode,amount', 'nullable', 'numeric', 'min:0.01'],
            'average_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
        ];
    }
}
