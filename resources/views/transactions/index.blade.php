@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">{{ __('Transactions') }}</h2>
@endsection

@section('content')
<div x-data="transactionsPage()" class="max-w-5xl mx-auto px-4 sm:px-6 py-6">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <form @submit.prevent="performSearch" class="w-full sm:w-64 relative">
            <input type="text" x-model="search" @input.debounce.300ms="performSearch" placeholder="{{ __('Search transactions...') }}" class="w-full bg-white dark:bg-[#18181b] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl px-4 py-2 text-sm focus:border-[#fbbf24] focus:ring-1 focus:ring-[#fbbf24] outline-none transition">
            
            <div x-show="isLoading" style="display: none;" class="absolute right-10 top-1/2 -translate-y-1/2 text-gray-500">
                <i class="fa-solid fa-circle-notch fa-spin"></i>
            </div>
            
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#fbbf24] transition">
                <i class="fa-solid fa-search"></i>
            </button>
        </form>

        <div class="hidden sm:flex justify-end">
        <div class="bg-gray-200/50 dark:bg-white/5 p-1 rounded-xl inline-flex gap-1 shadow-sm border border-gray-200/50 dark:border-white/10">
            <button @click="cols = 1" :class="cols === 1 ? 'bg-white dark:bg-white/10 shadow text-[#fbbf24]' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="w-12 py-1.5 rounded-lg text-sm font-bold transition flex justify-center items-center">
                1
            </button>
            <button @click="cols = 2" :class="cols === 2 ? 'bg-white dark:bg-white/10 shadow text-[#fbbf24]' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="w-12 py-1.5 rounded-lg text-sm font-bold transition flex justify-center items-center">
                2
            </button>
            <button @click="cols = 3" :class="cols === 3 ? 'bg-white dark:bg-white/10 shadow text-[#fbbf24]' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="w-12 py-1.5 rounded-lg text-sm font-bold transition flex justify-center items-center">
                3
            </button>
            <button @click="cols = 4" :class="cols === 4 ? 'bg-white dark:bg-white/10 shadow text-[#fbbf24]' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="w-12 py-1.5 rounded-lg text-sm font-bold transition flex justify-center items-center">
                4
            </button>
        </div>
        </div>
    </div>

    <div class="hidden sm:grid-cols-1 sm:grid-cols-2 sm:grid-cols-3 sm:grid-cols-4 lg:grid-cols-1 lg:grid-cols-2 lg:grid-cols-3 lg:grid-cols-4"></div>

    <div id="transactions-grid" class="grid gap-4 grid-cols-1" :class="{
        'sm:grid-cols-1': cols === 1,
        'sm:grid-cols-2': cols === 2,
        'sm:grid-cols-3 lg:grid-cols-3': cols === 3,
        'sm:grid-cols-3 lg:grid-cols-4': cols === 4
    }">
        @forelse($transactions as $t)
            @php
                $amountInDefault = $currentTeam->convertToDefaultCurrency($t->amount, $t->currency, $t->created_at);
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
                        <div class="truncate font-bold t-primary leading-tight">{{ $t->title }}</div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[10px] uppercase font-bold tracking-wider t-muted">@date($t->created_at)</span>
                        </div>
                        @if($t->note)
                            <div class="truncate text-xs t-muted mt-0.5">{{ $t->note }}</div>
                        @endif
                    </div>
                </div>
                <div class="mt-1">
                    <span class="font-extrabold text-xl t-primary group-hover:text-[#fbbf24] transition">
                        @money($amountInDefault) {{ $currencySymbol }}
                    </span>
                    <div class="text-xs t-muted mt-0.5">
                        {{ __('Originally') }}: @money($t->amount) {{ $t->currency }}
                    </div>
                    <div class="text-xs mt-1 font-bold uppercase tracking-widest {{ $t->type === 'income' ? 'text-emerald-500' : 'text-red-500' }}">{{ __($t->type) }}</div>
                </div>
                @if(auth()->user()->canEdit($currentTeam->id))
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-white/5 flex gap-2 w-full">
                        <a href="{{ route('transactions.edit', $t) }}" class="flex-1 flex justify-center items-center rounded-lg px-2 py-2 text-xs font-bold text-[#8b5cf6] bg-[#8b5cf6]/10 hover:bg-[#8b5cf6]/20 transition">{{ __('Edit') }}</a>
                        <form method="POST" action="{{ route('transactions.destroy', $t) }}" 
                            x-data
                            @submit.prevent="$dispatch('confirm', {
                                title: '{{ __('Delete transaction?') }}',
                                message: '{{ __('Are you sure you want to delete this transaction?') }}',
                                confirmText: '{{ __('Delete') }}',
                                variant: 'danger',
                                onConfirm: () => $el.submit()
                            })"
                            class="flex-1 flex">
                            @csrf
                            @method('DELETE')
                            <button class="w-full flex justify-center items-center rounded-lg px-2 py-2 text-xs font-bold text-red-500 bg-red-500/10 hover:bg-red-500/20 transition">{{ __('Delete') }}</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full py-12 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-white/5 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-receipt text-2xl t-muted"></i>
                </div>
                <p class="text-sm t-secondary">{{ __('No transactions yet.') }}</p>
                @can('create', \App\Models\Transaction::class)
                    <p class="text-xs t-muted mt-1">{{ __('Start by adding your first income or expense.') }}</p>
                @endcan
            </div>
        @endforelse
    </div>

    <div id="transactions-pagination" class="mt-6">{{ $transactions->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('transactionsPage', () => ({
        cols: parseInt(localStorage.getItem('transaction_cols')) || 2,
        search: new URLSearchParams(window.location.search).get('search') || '',
        isLoading: false,
        controller: null,

        init() {
            this.$watch('cols', value => localStorage.setItem('transaction_cols', value));
        },

        performSearch() {
            this.isLoading = true;
            
            if (this.controller) {
                this.controller.abort();
            }
            this.controller = new AbortController();
            
            const url = new URL(window.location.href);
            if (this.search) {
                url.searchParams.set('search', this.search);
                url.searchParams.delete('page'); // při novém hledání přepne na 1. stránku
            } else {
                url.searchParams.delete('search');
            }
            
            window.history.replaceState({}, '', url);

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: this.controller.signal
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newGrid = doc.getElementById('transactions-grid');
                if (newGrid) document.getElementById('transactions-grid').innerHTML = newGrid.innerHTML;

                const newPagination = doc.getElementById('transactions-pagination');
                if (newPagination) document.getElementById('transactions-pagination').innerHTML = newPagination.innerHTML;
            })
            .catch(err => {
                if (err.name !== 'AbortError') console.error(err);
            })
            .finally(() => {
                this.isLoading = false;
                this.controller = null;
            });
        }
    }));
});
</script>
@endpush
