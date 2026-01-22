@props(['transaction' => null, 'action' => null, 'method' => null, 'buttonText' => 'Uložit', 'categories' => [], 'budgets' => []])

<form method="POST" action="{{ $action ?? url()->current() }}" class="bg-white p-6 rounded shadow space-y-6 text-black">
    @csrf
    @if(!empty($method) && strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Title</label>
            {{--<input name="title" type="text" value="{{ old('title', $transaction->title ?? '') }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 text-black">--}}
            <x-text-input name="title" type="text" value="{{ old('title', $transaction->title ?? '') }}" required class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />

        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Amount</label>
            <input name="amount" type="number" step="0.01" value="{{ old('amount', $transaction->amount ?? '') }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 text-black">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Currency</label>
            <select id="currency_select_{{ $attributes->get('id') ?? Str::random(6) }}" name="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 text-black">
                @foreach(['CZK','EUR','USD'] as $c)
                    <option value="{{ $c }}" {{ old('currency', $transaction->currency ?? 'CZK') === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Type</label>
            <div class="mt-1 inline-flex rounded-md shadow-sm" role="group">
                <label class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-l-md">
                    <input type="radio" name="type" value="income" {{ old('type', $transaction->type ?? '') === 'income' ? 'checked' : '' }} class="mr-2">
                    Income
                </label>
                <label class="inline-flex items-center px-4 py-2 bg-white border-t border-b border-r border-gray-200 rounded-r-md">
                    <input type="radio" name="type" value="expense" {{ old('type', $transaction->type ?? '') === 'expense' ? 'checked' : '' }} class="mr-2">
                    Expense
                </label>
            </div>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Note</label>
            <textarea name="note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 text-black">{{ old('note', $transaction->note ?? '') }}</textarea>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Category</label>
            <select name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 text-black">
                <option value="">-- none --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $transaction->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mt-2">
        <label class="block text-sm font-medium text-black mb-1">Quick convert</label>
        <div class="flex items-center gap-2 bg-white p-3 rounded shadow">
            <select id="convert_to_{{ $attributes->get('id') ?? '' }}" class="rounded border p-2 text-black w-28">
                @foreach(['CZK','EUR','USD'] as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </select>

            <button id="convert_btn_{{ $attributes->get('id') ?? '' }}" type="button" class="inline-flex items-center gap-2 px-3 py-2 bg-yellow-400 hover:bg-yellow-500 disabled:opacity-50 rounded shadow transition" disabled>
                <svg id="convert_spinner_{{ $attributes->get('id') ?? '' }}" class="hidden animate-spin h-4 w-4 text-black" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span id="convert_label_{{ $attributes->get('id') ?? '' }}">Convert</span>
            </button>

            <div id="convert_result_{{ $attributes->get('id') ?? '' }}" class="text-sm text-gray-700 min-w-[120px]"></div>
        </div>
    </div>

    <div class="pt-2">
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
            {{ $buttonText ?? 'Uložit' }}
        </button>
    </div>
    
    @if(count($budgets))
        <div class="mt-4 p-3 bg-yellow-50 text-black rounded">
            <strong>Aktivní rozpočty</strong>
            <ul class="mt-2 list-disc pl-5 text-sm">
                @foreach($budgets as $b)
                    <li>{{ $b->category?->name ?? 'All' }} — {{ number_format($b->amount,2,',',' ') }} {{ $b->currency }} / {{ $b->period }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</form>
<script>
    (function(){
        const form = document.currentScript.previousElementSibling;
        if (!form) return;

        const amountInput = form.querySelector('input[name="amount"]');
        const currencySelect = form.querySelector('select[name="currency"]');
        const convertTo = form.querySelector('select[id^="convert_to_"]');
        const convertBtn = form.querySelector('button[id^="convert_btn_"]');
        const resultEl = form.querySelector('div[id^="convert_result_"]');
        const url = '{{ route('currency.convert') }}';

        if (!amountInput || !currencySelect || !convertTo || !convertBtn) return;

        const spinner = form.querySelector('svg[id^="convert_spinner_"]');
        const label = form.querySelector('span[id^="convert_label_"]');

        function updateButtonState(){
            const hasAmount = parseFloat(amountInput.value) > 0;
            convertBtn.disabled = !hasAmount;
        }

        amountInput.addEventListener('input', updateButtonState);
        updateButtonState();

        convertBtn.addEventListener('click', function(){
            const amount = amountInput.value || 0;
            const from = currencySelect.value;
            const to = convertTo.value;
            resultEl.textContent = '';

            spinner.classList.remove('hidden');
            label.textContent = 'Converting...';
            convertBtn.disabled = true;

            fetch(url + '?amount=' + encodeURIComponent(amount) + '&from=' + encodeURIComponent(from) + '&to=' + encodeURIComponent(to), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(r => r.json()).then(json => {
                spinner.classList.add('hidden');
                label.textContent = 'Convert';
                updateButtonState();

                if (json.error) {
                    resultEl.textContent = 'Error: ' + json.error;
                    return;
                }
                const rounded = (Math.round(json.result * 100) / 100).toFixed(2);
                resultEl.textContent = rounded + ' ' + json.to;
                if (confirm('Apply converted amount ('+rounded+' '+json.to+') to the form?')) {
                    amountInput.value = rounded;
                    currencySelect.value = json.to;
                }
            }).catch(err => {
                spinner.classList.add('hidden');
                label.textContent = 'Convert';
                updateButtonState();
                resultEl.textContent = 'Error';
            });
        });
    })();
</script>