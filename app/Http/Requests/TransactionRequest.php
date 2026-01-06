<?php

namespace App\Http\Requests;

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
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
            'type' => ['required', 'in:income,expense'],
            'note' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'currency' => ['required', 'string', 'size:3'],
        ];
    }
}
