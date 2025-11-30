@props(['transaction' => null, 'action' => null, 'method' => null, 'buttonText' => 'Uložit'])

<form method="POST" action="{{ $action ?? url()->current() }}" class="space-y-4 text-black">
    @csrf
    @if(!empty($method) && strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <div>
        <label class="block text-sm font-medium text-white">Item</label>
        <input name="title" type="text" value="{{ old('title', $transaction->title ?? '') }}" required
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 text-black">
    </div>

    <div>
        <label class="block text-sm font-medium text-white">Amount</label>
        <input name="amount" type="number" step="0.01" value="{{ old('amount', $transaction->amount ?? '') }}" required
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 text-black">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1 text-white">Type</label>
        <div class="flex gap-4">
            <label class="inline-flex items-center text-white">
                <input type="radio" name="type" value="income" {{ old('type', $transaction->type ?? '') === 'income' ? 'checked' : '' }} class="mr-2">
                Income
            </label>
            <label class="inline-flex items-center text-white">
                <input type="radio" name="type" value="expense" {{ old('type', $transaction->type ?? '') === 'expense' ? 'checked' : '' }} class="mr-2">
                Expense
            </label>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-white">Note</label>
        <textarea name="note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 text-black">{{ old('note', $transaction->note ?? '') }}</textarea>
    </div>

    <div class="pt-2">
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
            {{ $buttonText ?? 'Uložit' }}
        </button>
    </div>
</form>