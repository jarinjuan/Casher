@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Join Workspace') }}
    </h2>
@endsection

@section('content')
<div class="max-w-md mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-semibold mb-4">{{ __('Enter Invite Code') }}</h3>
            
            <form method="POST" action="{{ route('workspace.join.submit') }}">
                @csrf

                <div class="mb-4">
                    <label for="invite_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Invite Code') }}
                    </label>
                    <input 
                        type="text" 
                        name="invite_code" 
                        id="invite_code" 
                        value="{{ old('invite_code') }}"
                        placeholder="Enter the code shared by workspace owner"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
                        autofocus
                    />
                    @error('invite_code')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                    {{ __('Join Workspace') }}
                </button>
            </form>

            @if(session('error'))
                <div class="mt-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mt-4 p-3 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded">
                    {{ session('info') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
