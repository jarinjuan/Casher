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
            'amount'      => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
            'type'        => ['required', 'in:income,expense'],
            'note'        => ['nullable', 'string', 'max:10000'],
            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    if ($value && \App\Models\Category::where('id', '=', $value)->where('user_id', '=', auth()->id())->doesntExist()) {
                        $fail('Selected category does not belong to you.');
                    }
                },
            ],
            'currency'    => ['required', 'string', 'size:3', CurrencyList::validationRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min'      => 'Amount must be at least 0.01.',
            'amount.max'      => 'Amount must not exceed 99,999,999.99.',
            'amount.numeric'  => 'Amount must be a number.',
            'note.max'        => 'Note must not exceed 10,000 characters.',
            'currency.in'     => 'Invalid currency.',
            'title.max'       => 'Title must not exceed 255 characters.',
            'title.required'  => 'Title is required.',
            'amount.required' => 'Amount is required.',
            'type.required'   => 'Transaction type is required.',
            'type.in'         => 'Type must be income or expense.',
        ];
    }
}
