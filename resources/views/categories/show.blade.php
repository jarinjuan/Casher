@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">{{ __('Category') }}: {{ $category->name }}</h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <div class="mb-4">
        <a href="{{ route('categories.index') }}" class="t-secondary hover:text-[#fbbf24] transition text-sm font-medium">
            <i class="fa-solid fa-arrow-left mr-1"></i> {{ __('Back to categories') }}
        </a>
    </div>

    <div class="card p-5 mb-4">
        <div class="flex items-center gap-4">
            <div class="w-7 h-7 rounded-md" style="background:{{ $category->color ?? '#fbbf24' }}"></div>
            <div>
                <h3 class="text-lg font-bold t-primary">{{ $category->name }}</h3>
                <div class="text-xs t-muted">{{ __('Created') }}: @date($category->created_at)</div>
            </div>
        </div>
    </div>

    @if($category->monthly_budget > 0)
        @php
            $budgetSpent = $category->getMonthlySpent();
            $budgetPct = min(100, $category->monthly_budget > 0 ? ($budgetSpent / $category->monthly_budget) * 100 : 0);
            $remaining = max(0, $category->monthly_budget - $budgetSpent);
            $barColor = $budgetPct < 60 ? 'bg-emerald-500' : ($budgetPct < 85 ? 'bg-yellow-500' : 'bg-red-500');
            $budgetCurrencySymbol = \App\Models\Team::getCurrencySymbolFor($category->budget_currency ?? $defaultCurrency);
        @endphp
        <div class="card p-5 mb-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-bold t-primary text-sm">{{ __('Monthly budget') }}</h4>
                <span class="text-xs font-semibold {{ $budgetPct >= 100 ? 'text-red-500 dark:text-red-400' : 't-muted' }}">
                    @money($budgetSpent) / @money($category->monthly_budget) {{ $budgetCurrencySymbol }}
                </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-white/10 rounded-full h-3 overflow-hidden">
                <div class="{{ $barColor }} h-3 rounded-full transition-all duration-500" style="width: {{ $budgetPct }}%"></div>
            </div>
            <div class="flex items-center justify-between mt-2">
                <span class="text-xs t-muted">{{ \App\Helpers\Number::format($budgetPct, 1) }}% {{ __('used') }}</span>
                <span class="text-xs t-muted">{{ __('Remaining') }}: @money($remaining) {{ $budgetCurrencySymbol }}</span>
            </div>
        </div>
    @endif

    <div class="card p-5">
        <h4 class="font-bold t-primary mb-3">{{ __('Expenses') }}</h4>
        @if($transactions->count())
            <ul class="divide-y divide-gray-200 dark:divide-white/5">
                @foreach($transactions as $t)
                    <li class="py-3 flex flex-col sm:flex-row sm:justify-between gap-1">
                        <div>
                            <div class="font-semibold t-primary">{{ $t->title }}</div>
                            <div class="text-xs t-muted">{{ $t->note }}</div>
                        </div>
                        <div class="sm:text-right">
                            <div class="font-semibold t-primary">
                                @money($currentTeam->convertToDefaultCurrency($t->amount, $t->currency, $t->created_at)) {{ $currencySymbol }}
                            </div>
                            <div class="text-[10px] t-muted">
                                {{ __('Originally') }}: @money($t->amount) {{ $t->currency }}
                            </div>
                            <div class="text-[10px] t-muted mt-0.5">@date($t->created_at)</div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-4">{{ $transactions->links() }}</div>
        @else
            <div class="t-secondary text-sm">{{ __('No expenses in this category.') }}</div>
        @endif
    </div>
</div>
@endsection
