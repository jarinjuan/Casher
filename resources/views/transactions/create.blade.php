@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">{{ __('New record') }}</h2>
@endsection

@section('content')
<div class="max-w-lg mx-auto px-4 py-6">
    <x-transaction-form :transaction="$transaction" :categories="$categories" action="{{ route('transactions.store') }}" buttonText="Save" />
</div>
@endsection
