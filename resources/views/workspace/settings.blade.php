@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Workspace Settings') }}
    </h2>
@endsection

@section('content')
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
    
    @if(session('success'))
        <div class="p-3 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Workspace Info -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-semibold mb-4">{{ $team->name }}</h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Owner</p>
                    <p class="font-semibold">{{ $owner->name }} ({{ $owner->email }})</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Members</p>
                    <div class="space-y-2">
                        @foreach($members as $member)
                            <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                <div>
                                    <p class="font-medium">{{ $member->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $member->email }}</p>
                                </div>
                                <span class="text-xs bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-3 py-1 rounded-full">
                                    {{ $member->pivot->role ?? 'member' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Currency Settings (Owner only) -->
    @if(auth()->id() === $team->user_id)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">{{ __('Currency Settings') }}</h3>
                
                <form method="POST" action="{{ route('workspace.update-currency') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Default Currency
                        </label>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                            All transactions and investments will be displayed in this currency. Amounts in other currencies will be automatically converted.
                        </p>
                        <select name="default_currency" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="CZK" {{ $team->default_currency === 'CZK' ? 'selected' : '' }}>CZK - Czech Koruna (Kč)</option>
                            <option value="EUR" {{ $team->default_currency === 'EUR' ? 'selected' : '' }}>EUR - Euro (€)</option>
                            <option value="USD" {{ $team->default_currency === 'USD' ? 'selected' : '' }}>USD - US Dollar ($)</option>
                            <option value="GBP" {{ $team->default_currency === 'GBP' ? 'selected' : '' }}>GBP - British Pound (£)</option>
                            <option value="JPY" {{ $team->default_currency === 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen (¥)</option>
                            <option value="CHF" {{ $team->default_currency === 'CHF' ? 'selected' : '' }}>CHF - Swiss Franc</option>
                            <option value="PLN" {{ $team->default_currency === 'PLN' ? 'selected' : '' }}>PLN - Polish Zloty</option>
                            <option value="SEK" {{ $team->default_currency === 'SEK' ? 'selected' : '' }}>SEK - Swedish Krona</option>
                            <option value="NOK" {{ $team->default_currency === 'NOK' ? 'selected' : '' }}>NOK - Norwegian Krone</option>
                            <option value="DKK" {{ $team->default_currency === 'DKK' ? 'selected' : '' }}>DKK - Danish Krone</option>
                        </select>
                    </div>

                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4">
                        <p class="text-sm text-amber-800 dark:text-amber-200">
                            <strong>Current:</strong> {{ $team->default_currency }} - {{ $team->getCurrencySymbol() }}
                        </p>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                        Update Currency
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Invite Code Section (Owner only) -->
    @if(auth()->id() === $team->user_id)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">{{ __('Invite Members') }}</h3>
                
                @if($team->invite_code)
                    <div class="bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Current invite code:</p>
                        <div class="flex items-center gap-2">
                            <code class="flex-1 text-lg font-mono bg-white dark:bg-gray-700 p-3 rounded border border-gray-300 dark:border-gray-600">
                                {{ $team->invite_code }}
                            </code>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $team->invite_code }}')" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                                Copy
                            </button>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Share this code with anyone who wants to join your workspace.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('workspace.generate-invite') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-amber-400 hover:bg-amber-500 text-gray-900 font-semibold rounded-lg transition">
                        {{ $team->invite_code ? 'Generate New Code' : 'Generate Invite Code' }}
                    </button>
                </form>
            </div>
        </div>
    @endif

@endsection
