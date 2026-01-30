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
        $transactions = Transaction::where('team_id', $currentTeam->id ?? null)
            ->latest()
            ->paginate(15);
        return view('transactions.index', compact('transactions'));
    }

    public function create(): View
    {
        $transaction = new Transaction();
        $categories = auth()->user()->categories()->get();
        $budgets = auth()->user()->budgets()->with('category')->get();
        return view('transactions.create', compact('transaction','categories','budgets'));
    }

    public function store(TransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['team_id'] = $request->user()->currentTeam->id;
        Transaction::create($data);

        return redirect()->route('transactions.index')->with('success', 'Záznam uložen.');
    }

    public function edit(Transaction $transaction): View
    {
        if ($transaction->user_id !== auth()->id() || $transaction->team_id !== auth()->user()->currentTeam->id) abort(403);
        $categories = auth()->user()->categories()->get();
        $budgets = auth()->user()->budgets()->with('category')->get();
        return view('transactions.edit', compact('transaction','categories','budgets'));
    }

    public function update(TransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== auth()->id() || $transaction->team_id !== auth()->user()->currentTeam->id) abort(403);
        $transaction->update($request->validated());

        return redirect()->route('transactions.index')->with('success', 'Záznam aktualizován.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== auth()->id() || $transaction->team_id !== auth()->user()->currentTeam->id) abort(403);
        $transaction->delete();

        return back()->with('success', 'Záznam smazán.');
    }
}
