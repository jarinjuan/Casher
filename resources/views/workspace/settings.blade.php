@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Workspace Settings') }}
    </h2>
@endsection

@section('content')
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
    <!-- Workspace Info -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
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

                @if(session('success'))
                    <div class="mt-4 p-3 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
