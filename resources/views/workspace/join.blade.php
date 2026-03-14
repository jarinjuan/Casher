@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">{{ __('Join Workspace') }}</h2>
@endsection

@section('content')
<div class="max-w-md mx-auto px-4 py-6">
    <div class="card p-6">
        <h3 class="text-lg font-bold t-primary mb-4">{{ __('Enter Invite Code') }}</h3>
        <form method="POST" action="{{ route('workspace.join.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label for="invite_code" class="label-dark">{{ __('Invite Code') }}</label>
                <input type="text" name="invite_code" id="invite_code" value="{{ old('invite_code') }}" placeholder="Enter the code shared by workspace owner" class="input-dark" autofocus />
                @error('invite_code')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn-primary w-full text-sm">{{ __('Join Workspace') }}</button>
        </form>
        @if(session('error'))
            <div class="flash-error mt-4">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="flash-info mt-4">{{ session('info') }}</div>
        @endif
    </div>
</div>
@endsection
