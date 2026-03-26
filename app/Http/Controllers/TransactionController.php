<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $currentTeam = $request->user()->currentTeam;

        if (!$currentTeam) {
            return view('transactions.index', [
                'transactions' => collect(),
                'currentTeam' => null,
                'defaultCurrency' => 'CZK',
                'currencySymbol' => 'Kč',
            ]);
        }

        $search = $request->query('search');

        $query = Transaction::where('team_id', $currentTeam->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('note', 'like', '%' . $search . '%');
            });
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();
        
        $defaultCurrency = $currentTeam->default_currency;
        $currencySymbol = $currentTeam->getCurrencySymbol();
        
        return view('transactions.index', compact('transactions', 'currentTeam', 'defaultCurrency', 'currencySymbol'));
    }

    public function create(): View
    {
        $transaction = new Transaction();
        $categories = auth()->user()->currentTeam->categories()->orderBy('name')->get();
        $budgets = auth()->user()->currentTeam->budgets()->with('category')->get();
        return view('transactions.create', compact('transaction','categories','budgets'));
    }

    public function store(TransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['team_id'] = $request->user()->currentTeam->id;
        Transaction::create($data);

        return redirect()->route('transactions.index')->with('success', __('Transaction saved.'));
    }

    public function edit(Transaction $transaction): View
    {
        $this->authorize('update', $transaction);
        $categories = auth()->user()->currentTeam->categories()->orderBy('name')->get();
        $budgets = auth()->user()->currentTeam->budgets()->with('category')->get();
        return view('transactions.edit', compact('transaction','categories','budgets'));
    }

    public function update(TransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);
        $transaction->update($request->validated());

        return redirect()->route('transactions.index')->with('success', __('Transaction updated.'));
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);
        $transaction->delete();

        return back()->with('success', __('Transaction deleted.'));
    }
}
