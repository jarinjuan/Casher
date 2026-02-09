@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">Categories</h2>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <div class="mb-6 bg-slate-800 rounded-lg shadow-2xl border border-slate-700 p-6">
        <form method="POST" action="{{ route('categories.store') }}" class="flex flex-col gap-2">
            @csrf
            <h2 class="text-white text-xs font-extrabold tracking-wider leading-tight uppercase mb-2">Add Category</h2>
            <div class="flex flex-col gap-0">
                <label class="text-slate-400 text-xs font-bold uppercase tracking-widest">Name</label>
                <input name="name" type="text" required class="w-full bg-slate-700 border border-slate-600 rounded h-8 px-2 text-xs text-white placeholder:text-slate-500 focus:ring-1 focus:ring-primary focus:border-transparent transition-all" placeholder="Category name" />
            </div>
            <div class="flex flex-col gap-0">
                <label class="text-slate-400 text-xs font-bold uppercase tracking-widest">Color</label>
                <input name="color" type="color" value="{{ old('color', '#f4d125') }}" class="w-10 h-8 bg-transparent border-none" />
            </div>
            <div class="flex flex-col gap-0">
                <label class="text-slate-400 text-xs font-bold uppercase tracking-widest">Monthly budget</label>
                <input type="number" name="monthly_budget" placeholder="Monthly budget (CZK)" step="0.01" min="0" class="w-full bg-slate-700 border border-slate-600 rounded h-8 px-2 text-xs text-white placeholder:text-slate-500 focus:ring-1 focus:ring-primary focus:border-transparent transition-all" />
            </div>
            <div class="flex flex-col gap-0">
                <label class="text-slate-400 text-xs font-bold uppercase tracking-widest">Currency</label>
                <select name="budget_currency" class="w-full bg-slate-700 border border-slate-600 rounded h-8 px-2 text-xs text-white appearance-none focus:ring-1 focus:ring-primary focus:border-transparent cursor-pointer transition-all">
                    <option value="CZK">CZK</option>
                    <option value="EUR">EUR</option>
                    <option value="USD">USD</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-primary hover:bg-yellow-400 text-slate-900 font-bold py-1.5 rounded transition-colors flex items-center justify-center gap-1 shadow-lg shadow-primary/20">Add Category</button>
        </form>
    </div>

    <div class="bg-transparent p-0">
        <h3 class="font-medium mb-3 text-white">Category List</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
            @foreach($categories as $cat)
                <div class="transition group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-lg p-5 flex flex-col gap-2 hover:shadow-2xl hover:border-yellow-400 mx-2 min-w-[280px]">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg" style="background:{{ $cat->color ?? '#ddd' }}">
                            <i class="fa-solid fa-tags text-yellow-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="truncate font-bold text-lg text-gray-900 dark:text-white">
                                <a href="{{ route('categories.show', $cat) }}" class="hover:text-yellow-500 transition">{{ $cat->name }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-end justify-between mt-2">
                        <div>
                            @if($cat->monthly_budget)
                                <span class="font-extrabold text-xl text-gray-900 dark:text-white group-hover:text-yellow-500 transition">{{ number_format($cat->monthly_budget, 2, ',', ' ') }} {{ $cat->budget_currency ?? 'CZK' }}</span>
                                <div class="text-xs mt-1 font-semibold uppercase tracking-widest text-yellow-500">Monthly budget</div>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('categories.edit', $cat) }}" class="rounded px-2 py-1 text-xs font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-800 transition">Edit</a>
                            <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Delete category?')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded px-2 py-1 text-xs font-bold text-red-600 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-800 transition">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
