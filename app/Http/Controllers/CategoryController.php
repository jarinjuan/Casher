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
        $categories = $request->user()->categories()->orderBy('name')->get();
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
            $data['budget_currency'] = 'CZK';
        }

        $request->user()->categories()->create($data);

        return back()->with('success', 'Kategorie vytvořena.');
    }

    public function edit(Category $category): View
    {
        if ($category->user_id !== auth()->id()) abort(403);
        return view('categories.edit', compact('category'));
    }

    public function show(Category $category): View
    {
        if ($category->user_id !== auth()->id()) abort(403);

        $transactions = $category->transactions()->where('type', 'expense')->latest()->paginate(20);
        return view('categories.show', compact('category', 'transactions'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        if ($category->user_id !== auth()->id()) abort(403);

        $data = $request->validated();

        if (!isset($data['budget_currency'])) {
            $data['budget_currency'] = 'CZK';
        }

        $category->update($data);
        return redirect()->route('categories.index')->with('success', 'Kategorie aktualizována.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->user_id !== auth()->id()) abort(403);
        $category->delete();
        return back()->with('success', 'Kategorie smazána.');
    }
}
