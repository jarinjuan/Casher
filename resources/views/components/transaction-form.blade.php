@props(['transaction' => null, 'action' => null, 'method' => null, 'buttonText' => 'Add Transaction', 'categories' => [], 'budgets' => []])

<div class="w-full max-w-[480px] card overflow-hidden flex flex-col mx-auto my-6">
    <div class="px-5 pt-5 pb-1">
        <h1 class="t-primary text-xs font-extrabold tracking-wider leading-tight uppercase">
            {{ $buttonText === 'Save' || $buttonText === 'Update' ? 'Edit Transaction' : 'Add Transaction' }}
        </h1>
    </div>
    <form method="POST" action="{{ $action ?? url()->current() }}" class="px-5 py-3 flex flex-col gap-3">
            @csrf
            @if(!empty($method) && strtoupper($method) !== 'POST')
                @method($method)
            @endif

            <div class="flex">
                <div class="flex h-10 flex-1 items-center justify-center rounded-lg bg-gray-100 dark:bg-white/5 p-0.5 border border-gray-200 dark:border-white/10 gap-0.5">
                    <label class="flex cursor-pointer h-full grow items-center justify-center overflow-hidden rounded-md px-1 has-[:checked]:bg-[#fbbf24] has-[:checked]:text-black text-gray-400 text-xs font-bold transition-all">
                        <span class="truncate">INCOME</span>
                        <input type="radio" name="type" value="income" {{ old('type', $transaction->type ?? '') === 'income' ? 'checked' : '' }} class="invisible w-0"/>
                    </label>
                    <label class="flex cursor-pointer h-full grow items-center justify-center overflow-hidden rounded-md px-1 has-[:checked]:bg-[#fbbf24] has-[:checked]:text-black text-gray-400 text-xs font-bold transition-all">
                        <span class="truncate">EXPENSE</span>
                        <input type="radio" name="type" value="expense" {{ old('type', $transaction->type ?? 'expense') === 'expense' ? 'checked' : '' }} class="invisible w-0"/>
                    </label>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="label-dark">Title</label>
                <input name="title" type="text" value="{{ old('title', $transaction->title ?? '') }}" required
                       class="input-dark" placeholder="Description"/>
                <x-input-error :messages="$errors->get('title')" class="mt-0.5 text-red-500 text-xs" />
            </div>

            <div class="flex flex-col gap-1">
                <label class="label-dark">Amount</label>
                <input name="amount" type="number" step="1" value="{{ old('amount', $transaction->amount ?? '') }}" required
                       class="input-dark text-lg font-bold" style="height: 2.75rem;" placeholder="0.00"/>
                <x-input-error :messages="$errors->get('amount')" class="mt-0.5 text-red-500 text-xs" />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1">
                    <label class="label-dark">Category</label>
                    <select name="category_id" class="select-dark">
                        <option value="">Select</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $transaction->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="label-dark">Currency</label>
                    <select name="currency" class="select-dark">
                        @foreach(['CZK','EUR','USD'] as $c)
                            <option value="{{ $c }}" {{ old('currency', $transaction->currency ?? 'CZK') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="label-dark">Note</label>
                <input name="note" type="text" value="{{ old('note', $transaction->note ?? '') }}"
                       class="input-dark" placeholder="Note..."/>
            </div>

            <div class="pb-2 pt-1">
                <button type="submit" class="btn-primary w-full text-sm">
                    {{ $buttonText }}
                </button>
            </div>

            @if(count($budgets))
                <div class="pb-2 text-xs border-t border-gray-200 dark:border-white/10 pt-3">
                    <p class="font-bold text-[#fbbf24] mb-1">Budgets:</p>
                    <ul class="space-y-0.5 t-secondary">
                        @foreach($budgets as $b)
                            <li>{{ $b->category?->name ?? 'All' }} -- {{ number_format($b->amount,2,',','.') }} {{ $b->currency }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
</div>
