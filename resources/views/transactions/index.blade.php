@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">Transactions</h2>
@endsection

@section('content')
<div x-data="{ cols: 2, marks: [2,3,4,5] }" class="max-w-5xl mx-auto px-4 sm:px-6 py-6">
    <div class="mb-6 flex flex-col items-center">
        <div class="w-full flex flex-col items-center">
            <input type="range" min="2" max="5" step="1" x-model="cols" class="w-64 accent-[#fbbf24] h-1.5 bg-gray-200 dark:bg-white/10 rounded-lg appearance-none cursor-pointer">
            <div class="flex justify-between w-64 mt-2">
                <template x-for="n in marks" :key="n">
                    <span :class="cols === n ? 'text-[#fbbf24] font-bold' : 'text-gray-400'" class="text-xs select-none" x-text="n + 'x' + n"></span>
                </template>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="flash-success mb-4">{{ session('success') }}</div>
    @endif

    <div :class="'grid grid-cols-' + cols + ' gap-4'">
        @foreach($transactions as $t)
            @php
                $amountInDefault = $t->amount;
                $showOriginal = false;
                if ($t->currency !== $defaultCurrency) {
                    $amountInDefault = $currentTeam->convertToDefaultCurrency($t->amount, $t->currency, $t->created_at);
                    $showOriginal = true;
                }
            @endphp
            <div class="card p-5 flex flex-col gap-2 hover:border-[#fbbf24]/30 transition group">
                <div class="flex items-center gap-3 mb-1">
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg {{ $t->type === 'income' ? 'bg-emerald-500/10' : 'bg-red-500/10' }}">
                        @if($t->type === 'income')
                            <i class="fa-solid fa-arrow-down text-emerald-500"></i>
                        @else
                            <i class="fa-solid fa-arrow-up text-red-500"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="truncate font-bold t-primary">{{ $t->title }}</div>
                        <div class="truncate text-xs t-muted">{{ $t->note }}</div>
                    </div>
                </div>
                <div class="flex items-end justify-between mt-1">
                    <div>
                        <span class="font-extrabold text-xl t-primary group-hover:text-[#fbbf24] transition">
                            {{ number_format($amountInDefault, 2, ',', ' ') }} {{ $currencySymbol }}
                        </span>
                        @if($showOriginal)
                            <div class="text-xs t-muted mt-0.5">
                                Originally: {{ number_format($t->amount, 2, ',', ' ') }} {{ $t->currency }}
                            </div>
                        @endif
                        <div class="text-xs mt-1 font-bold uppercase tracking-widest {{ $t->type === 'income' ? 'text-emerald-500' : 'text-red-500' }}">{{ $t->type }}</div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('transactions.edit', $t) }}" class="rounded-lg px-2.5 py-1.5 text-xs font-bold text-[#8b5cf6] bg-[#8b5cf6]/10 hover:bg-[#8b5cf6]/20 transition">Edit</a>
                        <form method="POST" action="{{ route('transactions.destroy', $t) }}" onsubmit="return confirm('Delete?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $transactions->links() }}</div>
</div>
@endsection
