@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">Upravit kategorii</h2>
@endsection

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white p-4 rounded shadow">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="block text-sm font-medium">Název</label>
                <input name="name" value="{{ old('name', $category->name) }}" required class="border p-2 w-full rounded">
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium">Barva</label>
                <input name="color" value="{{ old('color', $category->color) }}" class="border p-2 w-full rounded">
            </div>

            <div>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded">Uložit</button>
                <a href="{{ route('categories.index') }}" class="ml-2 text-gray-600">Zpět</a>
            </div>
        </form>
    </div>
</div>
@endsection
