<?php

namespace App\Http\Requests;

use App\Enums\CurrencyList;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'color'           => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'monthly_budget'  => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'budget_currency' => ['nullable', 'string', 'size:3', CurrencyList::validationRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Název kategorie je povinný.',
            'name.max'            => 'Název nesmí překročit 255 znaků.',
            'color.regex'         => 'Barva musí být platný HEX kód (např. #FF5733).',
            'monthly_budget.numeric' => 'Měsíční rozpočet musí být číslo.',
            'monthly_budget.min'  => 'Měsíční rozpočet nesmí být záporný.',
            'monthly_budget.max'  => 'Měsíční rozpočet nesmí překročit 999 999 999 999.99.',
            'budget_currency.in'  => 'Neplatná měna.',
        ];
    }
}
