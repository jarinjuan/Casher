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
                    <input name="symbol" value="{{ $investment->symbol }}" class="input-dark" required>
                </div>
                <div>
                    <label class="label-dark">Name</label>
                    <input name="name" value="{{ $investment->name }}" class="input-dark">
                </div>
                <div>
                    <label class="label-dark">External ID</label>
                    <input name="external_id" value="{{ $investment->external_id }}" class="input-dark">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label-dark">Quantity</label>
                        <input name="quantity" type="number" step="0.00000001" value="{{ $investment->quantity }}" class="input-dark" required>
                    </div>
                    <div>
                        <label class="label-dark">Avg Price</label>
                        <input name="average_price" type="number" step="0.00000001" value="{{ $investment->average_price }}" class="input-dark" required>
                    </div>
                </div>
                <div>
                    <label class="label-dark">Currency</label>
                    <input name="currency" value="{{ $investment->currency }}" class="input-dark" required>
                </div>
                <div class="flex gap-3 pt-2">
                    <button class="btn-primary text-sm">Save</button>
                    <a href="{{ route('investments.index') }}" class="btn-secondary text-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
