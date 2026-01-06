<?php

namespace App\Http\Controllers;

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

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

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

    public function update(Request $request, Category $category): RedirectResponse
    {
        if ($category->user_id !== auth()->id()) abort(403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

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
