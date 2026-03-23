@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">Category: {{ $category->name }}</h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <div class="mb-4">
        <a href="{{ route('categories.index') }}" class="t-secondary hover:text-[#fbbf24] transition text-sm font-medium">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to categories
        </a>
    </div>

    <div class="card p-5 mb-4">
        <div class="flex items-center gap-4">
            <div class="w-7 h-7 rounded-md" style="background:{{ $category->color ?? '#fbbf24' }}"></div>
            <div>
                <h3 class="text-lg font-bold t-primary">{{ $category->name }}</h3>
                <div class="text-xs t-muted">Created: {{ $category->created_at->format('Y-m-d') }}</div>
            </div>
        </div>
    </div>

    <div class="card p-5">
        <h4 class="font-bold t-primary mb-3">Expenses</h4>
        @if($transactions->count())
            <ul class="divide-y divide-gray-200 dark:divide-white/5">
                @foreach($transactions as $t)
                    <li class="py-3 flex flex-col sm:flex-row sm:justify-between gap-1">
                        <div>
                            <div class="font-semibold t-primary">{{ $t->title }}</div>
                            <div class="text-xs t-muted">{{ $t->note }}</div>
                        </div>
                        <div class="sm:text-right">
                            <div class="font-semibold t-primary">@money($t->amount) {{ $t->currency }}</div>
                            <div class="text-xs t-muted">{{ $t->created_at->format('Y-m-d') }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-4">{{ $transactions->links() }}</div>
        @else
            <div class="t-muted text-sm">No expenses in this category.</div>
        @endif
    </div>
</div>
@endsection
