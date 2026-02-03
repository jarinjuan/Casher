@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">Categories</h2>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <div class="mb-6 p-4 bg-white rounded shadow">
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-3">
            @csrf
            <div class="flex gap-2 items-center">
                <input name="name" placeholder="New category" required class="border p-2 rounded flex-1">
                <input type="color" name="color" value="{{ old('color', '#4f46e5') }}" class="border p-1 rounded w-12 h-10" title="Vyber barvu">
            </div>
            <div class="flex gap-2 items-center">
                <input type="number" name="monthly_budget" placeholder="Monthly budget (CZK)" step="0.01" min="0" class="border p-2 rounded flex-1">
                <select name="budget_currency" class="border p-2 rounded w-20">
                    <option value="CZK">CZK</option>
                    <option value="EUR">EUR</option>
                    <option value="USD">USD</option>
                </select>
            </div>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Add</button>
        </form>
    </div>

    <div class="bg-white rounded shadow p-4">
        <h3 class="font-medium mb-3">Category List</h3>
        <ul>
            @foreach($categories as $cat)
                <li class="flex items-center justify-between py-2 border-b">
                    <div class="flex items-center gap-3">
                        <div style="width:18px;height:18px;background:{{ $cat->color ?? '#ddd' }};border-radius:4px"></div>
                        <div><a href="{{ route('categories.show', $cat) }}" class="text-indigo-600">{{ $cat->name }}</a></div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('categories.edit', $cat) }}" class="text-indigo-600">Edit</a>
                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Delete category?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Delete</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
