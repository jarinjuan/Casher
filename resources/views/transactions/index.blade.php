@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
        Transactions
    </h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('transactions.create') }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded">New transaction</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    @php $currencySymbols = ['CZK' => 'Kč', 'EUR' => '€', 'USD' => '$']; @endphp
    @foreach($transactions as $t)
        <div class="p-4 mb-2 bg-white rounded shadow text-black w-[50%]">
            <div class="flex justify-between">
                <div>
                    <strong>{{ $t->title }}</strong>
                    <div class="text-sm text-gray-500">{{ $t->note }}</div>
                </div>
                <div class="text-right">
                    <div class="font-medium text-black">{{ number_format($t->amount, 2, ',', ' ') }} {{ $currencySymbols[$t->currency] ?? $t->currency }}</div>
                    <div class="text-xs text-gray-500">{{ $t->type }}</div>
                </div>
            </div>
            <div class="mt-2 flex gap-4">
                <a href="{{ route('transactions.edit', $t) }}" class="text-xl text-indigo-600">Edit</a>
                <form method="POST" action="{{ route('transactions.destroy', $t) }}" onsubmit="return confirm('Delete?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-xl text-red-600">Delete</button>
                </form>
            </div>
        </div>
    @endforeach

    <div class="mt-4">{{ $transactions->links() }}</div>
</div>
@endsection
