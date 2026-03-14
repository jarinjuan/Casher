@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">{{ __('Workspace Settings') }}</h2>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-6">
    @if(session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash-error">{{ session('error') }}</div>
    @endif

    <div class="card p-6">
        <h3 class="text-lg font-bold t-primary mb-4">{{ $team->name }}</h3>
        <div class="space-y-4">
            <div>
                <p class="text-xs uppercase tracking-widest t-muted font-bold">Owner</p>
                <p class="font-semibold t-primary mt-1">{{ $owner->name }} ({{ $owner->email }})</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-widest t-muted font-bold mb-2">Members</p>
                <div class="space-y-2">
                    @foreach($members as $member)
                        <div class="flex items-center justify-between bg-gray-50 dark:bg-white/5 p-3 rounded-lg border border-gray-200 dark:border-white/5">
                            <div>
                                <p class="font-medium t-primary">{{ $member->name }}</p>
                                <p class="text-xs t-muted">{{ $member->email }}</p>
                            </div>
                            <span class="text-xs bg-[#8b5cf6]/10 border border-[#8b5cf6]/20 text-[#7c3aed] dark:text-[#a78bfa] px-3 py-1 rounded-full font-bold">
                                {{ $member->pivot->role ?? 'member' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if(auth()->id() === $team->user_id)
        <div class="card p-6">
            <h3 class="text-lg font-bold t-primary mb-4">{{ __('Currency Settings') }}</h3>
            <form method="POST" action="{{ route('workspace.update-currency') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="label-dark">Default Currency</label>
                    <p class="text-xs t-muted mb-3">All transactions and investments will be displayed in this currency.</p>
                    <select name="default_currency" class="select-dark">
                        <option value="CZK" {{ $team->default_currency === 'CZK' ? 'selected' : '' }}>CZK - Czech Koruna</option>
                        <option value="EUR" {{ $team->default_currency === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                        <option value="USD" {{ $team->default_currency === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                        <option value="GBP" {{ $team->default_currency === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                        <option value="JPY" {{ $team->default_currency === 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen</option>
                        <option value="CHF" {{ $team->default_currency === 'CHF' ? 'selected' : '' }}>CHF - Swiss Franc</option>
                        <option value="PLN" {{ $team->default_currency === 'PLN' ? 'selected' : '' }}>PLN - Polish Zloty</option>
                        <option value="SEK" {{ $team->default_currency === 'SEK' ? 'selected' : '' }}>SEK - Swedish Krona</option>
                        <option value="NOK" {{ $team->default_currency === 'NOK' ? 'selected' : '' }}>NOK - Norwegian Krone</option>
                        <option value="DKK" {{ $team->default_currency === 'DKK' ? 'selected' : '' }}>DKK - Danish Krone</option>
                    </select>
                </div>
                <div class="bg-amber-50 dark:bg-[#fbbf24]/5 border border-amber-200 dark:border-[#fbbf24]/10 rounded-xl p-4">
                    <p class="text-sm text-amber-700 dark:text-[#fbbf24]">
                        <strong>Current:</strong> {{ $team->default_currency }} - {{ $team->getCurrencySymbol() }}
                    </p>
                </div>
                <button type="submit" class="btn-primary text-sm">Update Currency</button>
            </form>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-bold t-primary mb-4">{{ __('Invite Members') }}</h3>
            @if($team->invite_code)
                <div class="bg-violet-50 dark:bg-[#8b5cf6]/5 border border-violet-200 dark:border-[#8b5cf6]/10 rounded-xl p-4 mb-4">
                    <p class="text-xs t-secondary mb-2">Current invite code:</p>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 text-lg font-mono bg-gray-50 dark:bg-white/5 p-3 rounded-lg border border-gray-200 dark:border-white/10 t-primary">
                            {{ $team->invite_code }}
                        </code>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $team->invite_code }}')" class="btn-secondary text-xs px-4 py-3">Copy</button>
                    </div>
                    <p class="text-xs t-muted mt-2">Share this code with anyone who wants to join your workspace.</p>
                </div>
            @endif
            <form method="POST" action="{{ route('workspace.generate-invite') }}">
                @csrf
                <button type="submit" class="btn-primary text-sm">
                    {{ $team->invite_code ? 'Generate New Code' : 'Generate Invite Code' }}
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
