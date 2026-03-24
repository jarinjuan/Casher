<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ChartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $teamId = $user->currentTeam->id;

        $categories = $user->categories()->orderBy('name')->get();

        $labels = [];
        $data = [];
        $colors = [];

        $team = clone $user->currentTeam;

        foreach ($categories as $cat) {
            $expenses = \App\Models\Transaction::where('team_id', $teamId)->where('type', 'expense')->where('category_id', $cat->id)->get();
            $sum = 0.0;
            foreach ($expenses as $expense) {
                $amount = $expense->amount;
                if ($expense->currency !== $team->default_currency) {
                    try {
                        $amount = $team->convertToDefaultCurrency($amount, $expense->currency, $expense->created_at);
                    } catch (\Exception $e) {}
                }
                $sum += $amount;
            }
            if ($sum <= 0) continue;
            $labels[] = $cat->name;
            $data[] = (float) $sum;
            $colors[] = $cat->color ?? '#4f46e5';
        }
        $uncatExpenses = \App\Models\Transaction::where('team_id', $teamId)->where('type', 'expense')->whereNull('category_id')->get();
        $uncat = 0.0;
        foreach ($uncatExpenses as $expense) {
            $amount = $expense->amount;
            if ($expense->currency !== $team->default_currency) {
                try {
                    $amount = $team->convertToDefaultCurrency($amount, $expense->currency, $expense->created_at);
                } catch (\Exception $e) {}
            }
            $uncat += $amount;
        }
        
        if ($uncat > 0) {
            $labels[] = 'Uncategorized';
            $data[] = (float) $uncat;
            $colors[] = '#d1d5db';
        }

        return view('charts.index', [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ]);
    }
}
