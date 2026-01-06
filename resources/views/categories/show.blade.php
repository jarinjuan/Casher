@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">Kategorie: {{ $category->name }}</h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('categories.index') }}" class="text-gray-600">← Zpět na seznam kategorií</a>
    </div>

    <div class="bg-white rounded shadow p-4 mb-4">
        <div class="flex items-center gap-4">
            <div style="width:28px;height:28px;background:{{ $category->color ?? '#ddd' }};border-radius:6px"></div>
            <div>
                <h3 class="text-lg font-medium">{{ $category->name }}</h3>
                <div class="text-sm text-gray-500">Vytvořeno: {{ $category->created_at->format('Y-m-d') }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded shadow p-4">
        <h4 class="font-medium mb-3">Náklady (expenses)</h4>
        @if($transactions->count())
            <ul>
                @foreach($transactions as $t)
                    <li class="py-2 border-b flex justify-between">
                        <div>
                            <div class="font-medium">{{ $t->title }}</div>
                            <div class="text-sm text-gray-500">{{ $t->note }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-medium">{{ number_format($t->amount,2,',',' ') }} {{ $t->currency }}</div>
                            <div class="text-xs text-gray-500">{{ $t->created_at->format('Y-m-d') }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-4">{{ $transactions->links() }}</div>
        @else
            <div class="text-gray-600">Žádné náklady v této kategorii.</div>
        @endif
    </div>
</div>
@endsection
