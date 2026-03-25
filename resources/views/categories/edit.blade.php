@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">Edit category</h2>
@endsection

@section('content')
<div class="max-w-lg mx-auto px-4 py-6">
    <div class="card p-6">
        <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="label-dark">Name</label>
                <input name="name" value="{{ old('name', $category->name) }}" required class="input-dark" maxlength="255">
            </div>
            <div>
                <label class="label-dark">Color</label>
                <input type="color" name="color" value="{{ old('color', $category->color ?? '#fbbf24') }}" class="w-10 h-9 bg-transparent border border-gray-300 dark:border-white/10 rounded-lg cursor-pointer">
            </div>
            <div>
                <label class="label-dark">Monthly budget</label>
                <input type="number" name="monthly_budget" placeholder="ex. 500" step="0.01" min="0" max="999999999999.99" value="{{ old('monthly_budget', $category->monthly_budget) }}" class="input-dark">
            </div>
            <div>
                <label class="label-dark">Budget currency</label>
                <select name="budget_currency" class="select-dark">
                    @foreach(['CZK','EUR','USD','GBP','JPY','CHF','PLN','SEK','NOK','DKK','HUF','CAD','AUD','NZD','CNY'] as $c)
                        <option value="{{ $c }}" {{ old('budget_currency', $category->budget_currency) === $c ? 'selected' : '' }}>{{ $c }} ({{ \App\Models\Team::getCurrencySymbolFor($c) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button class="btn-primary text-sm">Save</button>
                <a href="{{ route('categories.index') }}" class="btn-secondary text-sm">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection
