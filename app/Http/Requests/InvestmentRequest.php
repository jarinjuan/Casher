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
            'currency'      => ['required', 'string', 'size:3', CurrencyList::validationRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.max'      => 'Množství nesmí překročit 9 999 999 999.',
            'quantity.min'      => 'Množství musí být kladné číslo.',
            'quantity.numeric'  => 'Množství musí být číslo.',
            'amount.max'        => 'Částka nesmí překročit 9 999 999 999.',
            'amount.min'        => 'Částka musí být alespoň 0.01.',
            'amount.numeric'    => 'Částka musí být číslo.',
            'average_price.max' => 'Průměrná cena nesmí překročit 9 999 999 999.',
            'average_price.numeric' => 'Průměrná cena musí být číslo.',
            'currency.in'       => 'Neplatná měna.',
            'symbol.required'   => 'Symbol je povinný.',
            'symbol.max'        => 'Symbol nesmí překročit 15 znaků.',
            'type.in'           => 'Typ musí být stock nebo crypto.',
        ];
    }
}
