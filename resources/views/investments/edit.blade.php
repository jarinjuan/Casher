@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">{{ __('Edit investment') }}</h2>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-6">
        @if($errors->any())
            <div class="flash-error mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card p-6">
            <form method="POST" action="{{ route('investments.update', $investment) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="label-dark">{{ __('Type') }}</label>
                    <select name="type_display" class="select-dark bg-gray-100 dark:bg-white/10" disabled>
                        <option value="stock" {{ $investment->type === 'stock' ? 'selected' : '' }}>{{ __('Stock') }}</option>
                        <option value="crypto" {{ $investment->type === 'crypto' ? 'selected' : '' }}>{{ __('Crypto') }}</option>
                    </select>
                    <input type="hidden" name="type" value="{{ $investment->type }}">
                </div>
                <div>
                    <label class="label-dark">{{ __('Symbol') }}</label>
                    <input name="symbol" value="{{ $investment->symbol }}" class="input-dark bg-gray-100 dark:bg-white/10" required maxlength="15" readonly>
                    <p class="text-[10px] t-muted mt-1">{{ __('Symbol cannot be changed after creation.') }}</p>
                </div>
                <div>
                    <label class="label-dark">{{ __('Name') }}</label>
                    <input name="name" value="{{ $investment->name }}" class="input-dark" maxlength="100">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label-dark">{{ __('Quantity') }}</label>
                        <input name="quantity" type="number" step="0.00000001" min="0.00000001" max="9999999999" value="{{ $investment->quantity }}" class="input-dark" required>
                    </div>
                    <div>
                        <label class="label-dark">{{ __('Average price') }}</label>
                        <input name="average_price" type="number" step="0.00000001" min="0" max="9999999999" value="{{ $investment->average_price }}" class="input-dark" required>
                    </div>
                </div>
                <div>
                    <label class="label-dark">{{ __('Currency') }}</label>
                    <select name="currency" class="select-dark" required>
                        @foreach(['CZK','EUR','USD','GBP','JPY','CHF','PLN','SEK','NOK','DKK','HUF','CAD','AUD','NZD','CNY'] as $c)
                            <option value="{{ $c }}" {{ $investment->currency === $c ? 'selected' : '' }}>{{ $c }} ({{ \App\Models\Team::getCurrencySymbolFor($c) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button class="btn-primary text-sm">{{ __('Save') }}</button>
                    <a href="{{ route('investments.index') }}" class="btn-secondary text-sm">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
