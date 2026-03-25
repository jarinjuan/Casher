@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">Edit Investment</h2>
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
                    <label class="label-dark">Type</label>
                    <select name="type" class="select-dark">
                        <option value="stock" {{ $investment->type === 'stock' ? 'selected' : '' }}>Stock</option>
                        <option value="crypto" {{ $investment->type === 'crypto' ? 'selected' : '' }}>Crypto</option>
                    </select>
                </div>
                <div>
                    <label class="label-dark">Symbol</label>
                    <input name="symbol" value="{{ $investment->symbol }}" class="input-dark" required maxlength="15">
                </div>
                <div>
                    <label class="label-dark">Name</label>
                    <input name="name" value="{{ $investment->name }}" class="input-dark" maxlength="100">
                </div>
                <div>
                    <label class="label-dark">External ID</label>
                    <input name="external_id" value="{{ $investment->external_id }}" class="input-dark" maxlength="100">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label-dark">Quantity</label>
                        <input name="quantity" type="number" step="0.00000001" min="0.00000001" max="9999999999" value="{{ $investment->quantity }}" class="input-dark" required>
                    </div>
                    <div>
                        <label class="label-dark">Avg Price</label>
                        <input name="average_price" type="number" step="0.00000001" min="0" max="9999999999" value="{{ $investment->average_price }}" class="input-dark" required>
                    </div>
                </div>
                <div>
                    <label class="label-dark">Currency</label>
                    <select name="currency" class="select-dark" required>
                        @foreach(['CZK','EUR','USD','GBP','JPY','CHF','PLN','SEK','NOK','DKK','HUF','CAD','AUD','NZD','CNY'] as $c)
                            <option value="{{ $c }}" {{ $investment->currency === $c ? 'selected' : '' }}>{{ $c }} ({{ \App\Models\Team::getCurrencySymbolFor($c) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button class="btn-primary text-sm">Save</button>
                    <a href="{{ route('investments.index') }}" class="btn-secondary text-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
