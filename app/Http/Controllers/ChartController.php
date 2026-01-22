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

        $categories = $user->categories()->orderBy('name')->get();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($categories as $cat) {
            $sum = $cat->transactions()->where('type', 'expense')->sum('amount');
            if ($sum <= 0) continue;
            $labels[] = $cat->name;
            $data[] = (float) $sum;
            $colors[] = $cat->color ?? '#4f46e5';
        }

        // uncategorized expenses
        $uncat = $user->transactions()->where('type', 'expense')->whereNull('category_id')->sum('amount');
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
