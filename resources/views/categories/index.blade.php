@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">{{ __('Categories') }}</h2>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    @if($errors->any())
        <div class="flash-error mb-6">
            <div class="flex flex-col gap-1">
                @foreach($errors->all() as $error)
                    <div class="text-sm font-medium leading-tight">{{ __($error) }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card p-6 mb-6">
        <form method="POST" action="{{ route('categories.store') }}" class="flex flex-col gap-3">
            @csrf
            <h2 class="t-primary text-xs font-extrabold tracking-wider leading-tight uppercase mb-1">{{ __('Add category') }}</h2>
            <div class="flex flex-col gap-1">
                <label class="label-dark">{{ __('Name') }}</label>
                <input name="name" type="text" required class="input-dark @error('name') border-red-500 @enderror" placeholder="{{ __('Category name') }}" maxlength="255" value="{{ old('name') }}" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="label-dark">{{ __('Color') }}</label>
                <input name="color" type="color" value="{{ old('color', '#fbbf24') }}" class="w-10 h-9 bg-transparent border border-gray-300 dark:border-white/10 rounded-lg cursor-pointer" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="label-dark">{{ __('Monthly budget') }}</label>
                <input type="number" name="monthly_budget" placeholder="{{ __('Monthly budget') }}" step="0.01" min="0" max="999999999999.99" class="input-dark" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="label-dark">{{ __('Currency') }}</label>
                <select name="budget_currency" class="select-dark">
                    @foreach(['CZK','EUR','USD','GBP','JPY','CHF','PLN','SEK','NOK','DKK','HUF','CAD','AUD','NZD','CNY'] as $c)
                        <option value="{{ $c }}" {{ (old('budget_currency') ?? auth()->user()->currentTeam->default_currency ?? 'CZK') === $c ? 'selected' : '' }}>{{ $c }} ({{ \App\Models\Team::getCurrencySymbolFor($c) }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary w-full text-sm mt-1">{{ __('Add category') }}</button>
        </form>
    </div>

    @if($categories->isEmpty())
        <div class="py-10 text-center t-muted border border-dashed border-gray-200 dark:border-white/5 rounded-2xl mt-4">
            <span class="font-bold t-primary uppercase tracking-widest text-xs">{{ __('No categories yet.') }}</span>
        </div>
    @else
        <h3 class="font-bold t-primary mb-3 text-sm uppercase tracking-wider">{{ __('Category list') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($categories as $cat)
                <div class="card p-5 flex flex-col gap-2 hover:border-[#fbbf24]/30 transition group">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg" style="background: {{ $cat->color ?? '#fbbf24' }}20">
                            <i class="fa-solid fa-tags" style="color: {{ $cat->color ?? '#fbbf24' }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('categories.show', $cat) }}" class="truncate font-bold t-primary hover:text-[#fbbf24] transition">{{ $cat->name }}</a>
                        </div>
                    </div>
                    <a href="{{ route('categories.show', $cat) }}" class="mt-2 h-16 flex flex-col justify-center group-hover:opacity-80 transition">
                        @if($cat->monthly_budget)
                            <span class="font-extrabold text-xl t-primary group-hover:text-[#fbbf24] transition">
                                @money($cat->monthly_budget) {{ $cat->budget_currency ?? 'CZK' }}
                            </span>
                            <div class="text-xs t-muted mt-0.5">
                                {{ __('Monthly budget') }}
                            </div>
                        @else
                            <span class="text-sm t-muted italic">{{ __('No budget set') }}</span>
                        @endif
                    </a>
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-white/5 flex gap-2 w-full">
                        <a href="{{ route('categories.show', $cat) }}" class="flex-1 flex justify-center items-center rounded-lg px-2 py-2 text-xs font-bold text-[#fbbf24] bg-[#fbbf24]/10 hover:bg-[#fbbf24]/20 transition">{{ __('View') }}</a>
                        <a href="{{ route('categories.edit', $cat) }}" class="flex-1 flex justify-center items-center rounded-lg px-2 py-2 text-xs font-bold text-[#8b5cf6] bg-[#8b5cf6]/10 hover:bg-[#8b5cf6]/20 transition">{{ __('Edit') }}</a>
                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" 
                            x-data
                            @submit.prevent="$dispatch('confirm', {
                                title: '{{ __('Delete category?') }}',
                                message: '{{ __('Are you sure you want to delete this category? This action cannot be undone.') }}',
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
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
