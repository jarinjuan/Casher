@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
        Transactions
    </h2>
@endsection

@section('content')
<div x-data="{ cols: 2, marks: [2,3,4,5] }" class="max-w-5xl mx-auto">
    <div class="mb-6 flex flex-col items-center">
  
        <div class="w-full flex flex-col items-center">
            <input type="range" min="2" max="5" step="1" x-model="cols" class="w-64 accent-yellow-400 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
            <div class="flex justify-between w-64 mt-2">
                <template x-for="n in marks" :key="n">
                    <span :class="cols === n ? 'text-yellow-500 font-bold' : 'text-gray-500'" class="text-xs select-none" x-text="n + 'x' + n"></span>
                </template>
            </div>
        </div>
    </div>
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    @php $currencySymbols = ['CZK' => 'Kč', 'EUR' => '€', 'USD' => '$']; @endphp
    <div :class="'grid grid-cols-' + cols + ' gap-6'">
        @foreach($transactions as $t)
            <div class="transition group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-lg p-5 flex flex-col gap-2 hover:shadow-2xl hover:border-yellow-400 mx-2">
                <div class="flex items-center gap-3 mb-1">
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-yellow-100 dark:bg-yellow-900/30">
                        @if($t->type === 'income')
                            <i class="fa-solid fa-arrow-down text-green-500"></i>
                        @else
                            <i class="fa-solid fa-arrow-up text-red-500"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="truncate font-bold text-lg text-gray-900 dark:text-white">{{ $t->title }}</div>
                        <div class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $t->note }}</div>
                    </div>
                </div>
                <div class="flex items-end justify-between mt-2">
                    <div>
                        <span class="font-extrabold text-2xl text-gray-900 dark:text-white group-hover:text-yellow-500 transition">{{ number_format($t->amount, 2, ',', ' ') }} {{ $currencySymbols[$t->currency] ?? $t->currency }}</span>
                        <div class="text-xs mt-1 font-semibold uppercase tracking-widest {{ $t->type === 'income' ? 'text-green-500' : 'text-red-500' }}">{{ $t->type }}</div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('transactions.edit', $t) }}" class="rounded px-2 py-1 text-xs font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-800 transition">Edit</a>
                        <form method="POST" action="{{ route('transactions.destroy', $t) }}" onsubmit="return confirm('Delete?')">
                            @csrf
                            @method('DELETE')
                            <button class="rounded px-2 py-1 text-xs font-bold text-red-600 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-800 transition">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>
</div>
@endsection
