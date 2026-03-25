<?php

namespace App\Http\Requests;

use App\Enums\CurrencyList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('categories')->where(fn ($query) => $query->where('team_id', $this->user()->current_team_id))
                    ->ignore($this->category?->id)
            ],
            'color'           => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'monthly_budget'  => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'budget_currency' => ['nullable', 'string', 'size:3', CurrencyList::validationRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Category name is required.',
            'name.unique'         => 'Category with this name already exists in this workspace.',
            'name.max'            => 'Name must not exceed 255 characters.',
            'color.regex'         => 'Color must be a valid HEX code (e.g. #FF5733).',
            'monthly_budget.numeric' => 'Monthly budget must be a number.',
            'monthly_budget.min'  => 'Monthly budget must not be negative.',
            'monthly_budget.max'  => 'Monthly budget must not exceed 999,999,999,999.99.',
            'budget_currency.in'  => 'Invalid currency.',
        ];
    }
}
