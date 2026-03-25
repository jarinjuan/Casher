<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $categories = $request->user()->currentTeam->categories()->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return redirect()->route('categories.index');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (!isset($data['budget_currency'])) {
            $data['budget_currency'] = $request->user()->currentTeam->default_currency ?? 'CZK';
        }

        $data['user_id'] = $request->user()->id;
        $data['team_id'] = $request->user()->currentTeam->id;
        
        Category::create($data);

        return back()->with('success', __('Category created.'));
    }

    public function edit(Category $category): View
    {
        $this->authorize('update', $category);
        return view('categories.edit', compact('category'));
    }

    public function show(Category $category): View
    {
        $this->authorize('view', $category);

        $transactions = $category->transactions()->where('type', 'expense')->latest()->paginate(20);
        $currentTeam = auth()->user()->currentTeam;
        $defaultCurrency = $currentTeam->default_currency;
        $currencySymbol = $currentTeam->getCurrencySymbol();

        return view('categories.show', compact('category', 'transactions', 'currentTeam', 'defaultCurrency', 'currencySymbol'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $data = $request->validated();

        if (!isset($data['budget_currency'])) {
            $data['budget_currency'] = $request->user()->currentTeam->default_currency ?? 'CZK';
        }

        $category->update($data);
        return redirect()->route('categories.index')->with('success', __('Category updated.'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);
        $category->delete();
        return back()->with('success', __('Category deleted.'));
    }
}
