<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg">
                        {{ __("Welcome back, ") }} <strong>{{ Auth::user()->name }}</strong>!
                    </h3>
                    
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Current workspace: 
                        <span class="font-bold text-indigo-500 text-base">
                            {{ Auth::user()->currentTeam->name }}
                        </span>
                    </div>
                </div>
            </div>

            @php
                $categories = Auth::user()->categories()->where('monthly_budget', '>', 0)->get();
                $currencySymbols = ['CZK' => 'Kč', 'EUR' => '€', 'USD' => '$'];
            @endphp

            @if($categories->count() > 0)
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Monthy budget</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($categories as $cat)
                        @php
                            $spent = $cat->getMonthlySpent();
                            $budget = $cat->monthly_budget;
                            $percentage = $cat->getMonthlyBudgetPercentage();
                            $isExceeded = $spent > $budget;
                            $symbol = $currencySymbols[$cat->budget_currency] ?? $cat->budget_currency;
                        @endphp
                        <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <div style="width:12px;height:12px;background:{{ $cat->color ?? '#4f46e5' }};border-radius:3px"></div>
                                <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ $cat->name }}</h4>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-2">
                                <div class="bg-indigo-600 h-3 rounded-full transition-all" style="width: {{ min($percentage, 100) }}%; background-color: {{ $percentage >= 100 ? '#ef4444' : '#4f46e5' }}"></div>
                            </div>
                            <div class="text-sm">
                                <span class="font-semibold {{ $isExceeded ? 'text-red-600' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ number_format($spent, 2, ',', ' ') }}{{ $symbol }}
                                </span>
                                <span class="text-gray-500">/</span>
                                <span class="text-gray-700 dark:text-gray-300">
                                    {{ number_format($budget, 2, ',', ' ') }}{{ $symbol }}
                                </span>
                            </div>
                            @if($isExceeded)
                                <div class="mt-2 text-xs text-red-600 font-semibold">Překročeno o {{ number_format($spent - $budget, 2, ',', ' ') }}{{ $symbol }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>