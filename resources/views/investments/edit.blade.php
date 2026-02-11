@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
        Edit Investment
    </h2>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto p-6">
        @if($errors->any())
            <div class="p-3 bg-red-100 text-red-800 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-6">
            <form method="POST" action="{{ route('investments.update', $investment) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-gray-500">Type</label>
                    <select name="type" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="stock" {{ $investment->type === 'stock' ? 'selected' : '' }}>Stock</option>
                        <option value="crypto" {{ $investment->type === 'crypto' ? 'selected' : '' }}>Crypto</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500">Symbol</label>
                    <input name="symbol" value="{{ $investment->symbol }}" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500">Name</label>
                    <input name="name" value="{{ $investment->name }}" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500">External ID</label>
                    <input name="external_id" value="{{ $investment->external_id }}" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500">Quantity</label>
                        <input name="quantity" type="number" step="0.00000001" value="{{ $investment->quantity }}" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500">Avg Price</label>
                        <input name="average_price" type="number" step="0.00000001" value="{{ $investment->average_price }}" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500">Currency</label>
                    <input name="currency" value="{{ $investment->currency }}" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>

                <div class="flex gap-3">
                    <button class="bg-amber-400 hover:bg-amber-500 text-white font-semibold py-2 px-4 rounded-lg">Save</button>
                    <a href="{{ route('investments.index') }}" class="text-sm px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
