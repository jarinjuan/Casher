@props(['transaction' => null, 'action' => null, 'method' => null, 'buttonText' => 'Add Transaction', 'categories' => [], 'budgets' => []])

<!-- Modal Overlay -->
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-2">
    <!-- Modal Container -->
    <div class="relative w-full max-w-[480px] bg-slate-800 rounded-lg shadow-2xl overflow-hidden flex flex-col border border-slate-700">
        <!-- Close Button -->
        <a href="{{ route('transactions.index') }}" class="absolute top-3 right-3 text-slate-400 hover:text-white transition-colors z-10 text-sm">
            close
        </a>

        <!-- Header -->
        <div class="px-4 pt-3 pb-0.5">
            <h1 class="text-white text-sm font-extrabold tracking-wider leading-tight uppercase">
                {{ $buttonText === 'Save' ? 'Edit' : 'Add Transaction' }}
            </h1>
        </div>

        <!-- Form Body -->
        <form method="POST" action="{{ $action ?? url()->current() }}" class="px-4 py-1 flex flex-col gap-1.5 overflow-y-auto max-h-[calc(100vh-120px)]">
            @csrf
            @if(!empty($method) && strtoupper($method) !== 'POST')
                @method($method)
            @endif

            <!-- Transaction Type Toggle -->
            <div class="flex">
                <div class="flex h-9 flex-1 items-center justify-center rounded-md bg-slate-700 p-0.5 border border-slate-600 gap-0.5">
                    <label class="flex cursor-pointer h-full grow items-center justify-center overflow-hidden rounded px-1 has-[:checked]:bg-yellow-400 has-[:checked]:text-slate-900 text-slate-400 text-xs font-bold transition-all">
                        <span class="truncate">INCOME</span>
                        <input type="radio" name="type" value="income" {{ old('type', $transaction->type ?? '') === 'income' ? 'checked' : '' }} class="invisible w-0"/>
                    </label>
                    <label class="flex cursor-pointer h-full grow items-center justify-center overflow-hidden rounded px-1 has-[:checked]:bg-yellow-400 has-[:checked]:text-slate-900 text-slate-400 text-xs font-bold transition-all">
                        <span class="truncate">EXPENSE</span>
                        <input type="radio" name="type" value="expense" {{ old('type', $transaction->type ?? 'expense') === 'expense' ? 'checked' : '' }} class="invisible w-0"/>
                    </label>
                </div>
            </div>

            <!-- Amount Input -->
            <div class="flex flex-col gap-0">
                <label class="text-slate-400 text-xs font-bold uppercase tracking-widest">Amount</label>
                <div class="relative group">
                    <input name="amount" type="number" step="0.01" value="{{ old('amount', $transaction->amount ?? '') }}" required
                           class="w-full bg-slate-700 border border-slate-600 rounded h-10 px-2 text-lg font-bold text-white placeholder:text-slate-500 focus:ring-1 focus:ring-primary focus:border-transparent transition-all" placeholder="0.00"/>
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">
                        {{ old('currency', $transaction->currency ?? 'CZK') }}
                    </div>
                </div>
                <x-input-error :messages="$errors->get('amount')" class="mt-0.5 text-red-400 text-xs" />
            </div>

            <!-- Grid for Category and Currency -->
            <div class="grid grid-cols-2 gap-1.5">
                <!-- Category Dropdown -->
                <div class="flex flex-col gap-0">
                    <label class="text-slate-400 text-xs font-bold uppercase tracking-widest">Category</label>
                    <select name="category_id" class="w-full bg-slate-700 border border-slate-600 rounded h-9 px-2 text-xs text-white appearance-none focus:ring-1 focus:ring-primary focus:border-transparent cursor-pointer transition-all">
                        <option value="">Select</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $transaction->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Currency Dropdown -->
                <div class="flex flex-col gap-0">
                    <label class="text-slate-400 text-xs font-bold uppercase tracking-widest">Currency</label>
                    <select name="currency" class="w-full bg-slate-700 border border-slate-600 rounded h-9 px-2 text-xs text-white appearance-none focus:ring-1 focus:ring-primary focus:border-transparent cursor-pointer transition-all">
                        @foreach(['CZK','EUR','USD'] as $c)
                            <option value="{{ $c }}" {{ old('currency', $transaction->currency ?? 'CZK') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Title Input -->
            <div class="flex flex-col gap-0">
                <label class="text-slate-400 text-xs font-bold uppercase tracking-widest">Title</label>
                <input name="title" type="text" value="{{ old('title', $transaction->title ?? '') }}" required
                       class="w-full bg-slate-700 border border-slate-600 rounded h-8 px-2 text-xs text-white placeholder:text-slate-500 focus:ring-1 focus:ring-primary focus:border-transparent transition-all" placeholder="Description"/>
                <x-input-error :messages="$errors->get('title')" class="mt-0.5 text-red-400 text-xs" />
            </div>

            <!-- Note Input -->
            <div class="flex flex-col gap-0">
                <label class="text-slate-400 text-xs font-bold uppercase tracking-widest">Note</label>
                <input name="note" type="text" value="{{ old('note', $transaction->note ?? '') }}"
                       class="w-full bg-slate-700 border border-slate-600 rounded h-8 px-2 text-xs text-white placeholder:text-slate-500 focus:ring-1 focus:ring-primary focus:border-transparent transition-all" placeholder="Note..."/>
            </div>

            <!-- Footer Action Button -->
            <div class="pb-1 pt-0.5">
                <button type="submit" class="w-full bg-primary hover:bg-yellow-400 text-slate-900 font-bold py-1.5 rounded transition-colors flex items-center justify-center gap-1 shadow-lg shadow-primary/20">
                    <span class="text-xs">{{ $buttonText }}</span>
                </button>
            </div>

            @if(count($budgets))
                <div class="px-0 pb-0 text-slate-300 text-xs border-t border-slate-700 mt-0.5 pt-0.5">
                    <p class="font-bold text-yellow-400 mb-0.5 text-xs">Budgets:</p>
                    <ul class="space-y-0 text-slate-400 text-xs">
                        @foreach($budgets as $b)
                            <li class="text-xs">{{ $b->category?->name ?? 'All' }} — {{ number_format($b->amount,2,',','.') }} {{ $b->currency }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>
</div>

<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .bg-primary {
        background-color: #f4d125;
    }
</style>
