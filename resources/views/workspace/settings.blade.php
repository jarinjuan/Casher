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
        <div class="space-y-6">
            <!-- Owner Profile -->
            <div>
                <p class="text-xs uppercase tracking-widest t-muted font-bold mb-3">Owner</p>
                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10">
                    <div class="h-10 w-10 flex-shrink-0 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-sm">
                        {{ strtoupper(substr($owner->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold t-primary">{{ $owner->name }}</p>
                        <p class="text-sm t-muted">{{ $owner->email }}</p>
                    </div>
                    <div class="ml-auto">
                        <span class="text-xs bg-violet-100 text-violet-700 dark:bg-[#8b5cf6]/10 dark:text-[#a78bfa] border border-violet-200 dark:border-[#8b5cf6]/20 px-3 py-1 rounded-full font-bold">
                            Owner
                        </span>
                    </div>
                </div>
            </div>

            <!-- Members List -->
            <div>
                <p class="text-xs uppercase tracking-widest t-muted font-bold mb-3">Members</p>
                <div class="bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 divide-y divide-gray-200 dark:divide-white/10 overflow-hidden">
                    @foreach($members as $member)
                        @if($member->id === $owner->id) @continue @endif
                        <div class="flex items-center justify-between p-4 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-gray-200 dark:bg-gray-700/50 flex items-center justify-center t-secondary font-bold border border-gray-300 dark:border-gray-600">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold t-primary">{{ $member->name }}</p>
                                    <p class="text-sm t-muted">{{ $member->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-xs bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-700 px-3 py-1 rounded-full font-bold capitalize">
                                    {{ $member->pivot->role ?? 'Member' }}
                                </span>
                                @if(auth()->id() === $team->user_id && $member->id !== auth()->id())
                                    <form method="POST" action="{{ route('workspace.remove-member', ['team' => $team->id, 'user' => $member->id]) }}" onsubmit="return confirm('Are you sure you want to remove this member from the workspace? They will no longer be able to access it or rejoin using the current invite code.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Remove Member">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    @if($members->count() <= 1)
                        <div class="p-6 text-center">
                            <p class="text-sm t-muted">No other members in this workspace yet.</p>
                        </div>
                    @endif
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
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <code class="flex-1 text-lg font-mono bg-gray-50 dark:bg-white/5 p-3 rounded-lg border border-gray-200 dark:border-white/10 t-primary break-all">
                            {{ $team->invite_code }}
                        </code>
                        <button type="button" onclick="copyInviteCode(event, '{{ $team->invite_code }}')" class="btn-secondary text-xs px-4 py-3 whitespace-nowrap min-w-[70px] text-center transition-colors">
                            <span>Copy</span>
                        </button>
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
        </div>
    @else
        <div class="card p-6 border border-red-200 dark:border-red-900/30">
            <h3 class="text-lg font-bold text-red-600 dark:text-red-400 mb-2">{{ __('Leave Workspace') }}</h3>
            <p class="text-sm t-muted mb-4">Are you sure you want to leave this workspace? You will lose access to all its data and will need a new invite code from the owner to rejoin.</p>
            <form method="POST" action="{{ route('workspace.leave', $team->id) }}" onsubmit="return confirm('Are you sure you want to leave this workspace? You will lose access immediately.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-primary !bg-red-600 hover:!bg-red-700 !text-white !border-red-600 text-sm transition-colors">Leave Workspace</button>
            </form>
        </div>
    @endif
</div>

<script>
function copyInviteCode(event, text) {
    const btn = event.currentTarget;
    const span = btn.querySelector('span');
    const originalText = span.innerText;

    const successFallback = () => {
        span.innerText = 'Copied!';
        btn.classList.add('!bg-emerald-500', '!text-white', '!border-emerald-500');
        setTimeout(() => {
            span.innerText = originalText;
            btn.classList.remove('!bg-emerald-500', '!text-white', '!border-emerald-500');
        }, 2000);
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(successFallback).catch(() => {
            fallbackCopy(text, successFallback);
        });
    } else {
        fallbackCopy(text, successFallback);
    }
}

function fallbackCopy(text, successCallback) {
    const el = document.createElement('textarea');
    el.value = text;
    document.body.appendChild(el);
    el.select();
    try {
        document.execCommand('copy');
        successCallback();
    } catch (e) {
        console.error('Fallback copy failed', e);
    }
    document.body.removeChild(el);
}
</script>
@endsection
