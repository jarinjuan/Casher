<?php

namespace App\Http\Requests;

use App\Enums\CurrencyList;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'type'        => ['required', 'in:income,expense'],
            'note'        => ['nullable', 'string', 'max:10000'],
            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    if ($value && \App\Models\Category::where('id', '=', $value)->where('user_id', '=', auth()->id())->doesntExist()) {
                        $fail('Vybraná kategorie vám nepatří.');
                    }
                },
            ],
            'currency'    => ['required', 'string', 'size:3', CurrencyList::validationRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min'      => 'Částka musí být alespoň 0.01.',
            'amount.max'      => 'Částka nesmí překročit 99 999 999.99.',
            'amount.numeric'  => 'Částka musí být číslo.',
            'note.max'        => 'Poznámka nesmí překročit 10 000 znaků.',
            'currency.in'     => 'Neplatná měna.',
            'title.max'       => 'Název nesmí překročit 255 znaků.',
            'title.required'  => 'Název je povinný.',
            'amount.required' => 'Částka je povinná.',
            'type.required'   => 'Typ transakce je povinný.',
            'type.in'         => 'Typ musí být příjem nebo výdaj.',
        ];
    }
}
