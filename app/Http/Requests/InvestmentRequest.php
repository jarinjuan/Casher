<?php

namespace App\Http\Requests;

use App\Enums\CurrencyList;
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
            'type'          => ['required', 'in:stock,crypto'],
            'symbol'        => ['required', 'string', 'max:15'],
            'name'          => ['nullable', 'string', 'max:100'],
            'external_id'   => ['nullable', 'string', 'max:100'],
            'buy_mode'      => ['required', 'in:quantity,amount'],
            'quantity'      => ['required_if:buy_mode,quantity', 'nullable', 'numeric', 'min:0.00000001', 'max:9999999999'],
            'amount'        => ['required_if:buy_mode,amount', 'nullable', 'numeric', 'min:0.01', 'max:9999999999'],
            'average_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'currency'      => ['nullable', 'string', 'size:3', CurrencyList::validationRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.max'      => 'Quantity must not exceed 9,999,999,999.',
            'quantity.min'      => 'Quantity must be a positive number.',
            'quantity.numeric'  => 'Quantity must be a number.',
            'amount.max'        => 'Amount must not exceed 9,999,999,999.',
            'amount.min'        => 'Amount must be at least 0.01.',
            'amount.numeric'    => 'Amount must be a number.',
            'average_price.max' => 'Average price must not exceed 9,999,999,999.',
            'average_price.numeric' => 'Average price must be a number.',
            'currency.in'       => 'Invalid currency.',
            'symbol.required'   => 'Symbol is required.',
            'symbol.max'        => 'Symbol must not exceed 15 characters.',
            'type.in'           => 'Type must be stock or crypto.',
        ];
    }
}
