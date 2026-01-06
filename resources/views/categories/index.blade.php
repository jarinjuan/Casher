@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">Kategorie</h2>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <div class="mb-6 p-4 bg-white rounded shadow">
        <form method="POST" action="{{ route('categories.store') }}" class="flex gap-2">
            @csrf
            <input name="name" placeholder="Nová kategorie" required class="border p-2 rounded flex-1">
            <input name="color" placeholder="#ffcc00" class="border p-2 rounded w-32">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Přidat</button>
        </form>
    </div>

    <div class="bg-white rounded shadow p-4">
        <h3 class="font-medium mb-3">Seznam kategorií</h3>
        <ul>
            @foreach($categories as $cat)
                <li class="flex items-center justify-between py-2 border-b">
                    <div class="flex items-center gap-3">
                        <div style="width:18px;height:18px;background:{{ $cat->color ?? '#ddd' }};border-radius:4px"></div>
                        <div><a href="{{ route('categories.show', $cat) }}" class="text-indigo-600">{{ $cat->name }}</a></div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('categories.edit', $cat) }}" class="text-indigo-600">Edit</a>
                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Smazat kategorii?')">
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
