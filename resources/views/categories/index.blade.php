@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">Categories</h2>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    @if(session('success'))
        <div class="flash-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="card p-6 mb-6">
        <form method="POST" action="{{ route('categories.store') }}" class="flex flex-col gap-3">
            @csrf
            <h2 class="t-primary text-xs font-extrabold tracking-wider leading-tight uppercase mb-1">Add Category</h2>
            <div class="flex flex-col gap-1">
                <label class="label-dark">Name</label>
                <input name="name" type="text" required class="input-dark" placeholder="Category name" maxlength="255" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="label-dark">Color</label>
                <input name="color" type="color" value="{{ old('color', '#fbbf24') }}" class="w-10 h-9 bg-transparent border border-gray-300 dark:border-white/10 rounded-lg cursor-pointer" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="label-dark">Monthly budget</label>
                <input type="number" name="monthly_budget" placeholder="Monthly budget" step="0.01" min="0" max="999999999999.99" class="input-dark" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="label-dark">Currency</label>
                <select name="budget_currency" class="select-dark">
                    @foreach(['CZK','EUR','USD','GBP','JPY','CHF','PLN','SEK','NOK','DKK','HUF','CAD','AUD','NZD','CNY'] as $c)
                        <option value="{{ $c }}" {{ (old('budget_currency') ?? auth()->user()->currentTeam->default_currency ?? 'CZK') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary w-full text-sm mt-1">Add Category</button>
        </form>
    </div>

    <h3 class="font-bold t-primary mb-3">Category List</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($categories as $cat)
            <div class="card p-5 flex flex-col gap-2 hover:border-[#fbbf24]/30 transition group">
                <div class="flex items-center gap-3 mb-1">
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg" style="background:{{ $cat->color ?? '#fbbf24' }}20">
                        <div class="w-3.5 h-3.5 rounded" style="background:{{ $cat->color ?? '#fbbf24' }}"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('categories.show', $cat) }}" class="truncate font-bold t-primary hover:text-[#fbbf24] transition block">{{ $cat->name }}</a>
                    </div>
                </div>
                <div class="flex items-end justify-between mt-1">
                    <div>
                        @if($cat->monthly_budget)
                            <span class="font-extrabold text-lg t-primary group-hover:text-[#fbbf24] transition">{{ number_format($cat->monthly_budget, 2, ',', ' ') }} {{ $cat->budget_currency ?? 'CZK' }}</span>
                            <div class="text-xs mt-0.5 font-bold uppercase tracking-widest t-muted">Monthly budget</div>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('categories.edit', $cat) }}" class="rounded-lg px-2.5 py-1.5 text-xs font-bold text-[#8b5cf6] bg-[#8b5cf6]/10 hover:bg-[#8b5cf6]/20 transition">Edit</a>
                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Delete category?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
