@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">New record</h2>
@endsection

@section('content')
<div class="max-w-lg mx-auto">
    <x-transaction-form :transaction="$transaction" action="{{ route('transactions.store') }}" buttonText="Save" />
</div>
@endsection
