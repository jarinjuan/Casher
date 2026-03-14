@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">Edit record</h2>
@endsection

@section('content')
<div class="max-w-lg mx-auto px-4 py-6">
    <x-transaction-form :transaction="$transaction" :categories="$categories" :budgets="$budgets" action="{{ route('transactions.update', $transaction) }}" method="PUT" buttonText="Update" />
</div>
@endsection
