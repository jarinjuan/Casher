<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
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
        $transactions = $request->user()->transactions()->latest()->paginate(15);
        return view('transactions.index', compact('transactions'));
    }

    public function create(): View
    {
        $transaction = new Transaction();
        return view('transactions.create', compact('transaction'));
    }

    public function store(TransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        Transaction::create($data);

        return redirect()->route('transactions.index')->with('success', 'Záznam uložen.');
    }

    public function edit(Transaction $transaction): View
    {
        if ($transaction->user_id !== auth()->id()) abort(403);
        return view('transactions.edit', compact('transaction'));
    }

    public function update(TransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== auth()->id()) abort(403);
        $transaction->update($request->validated());

        return redirect()->route('transactions.index')->with('success', 'Záznam aktualizován.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== auth()->id()) abort(403);
        $transaction->delete();

        return back()->with('success', 'Záznam smazán.');
    }
}
