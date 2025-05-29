<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::where('user_id', Auth::id());

        if ($request->has('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        $perPage = $request->get('per_page', 10);
        $categories = $query->latest()->paginate($perPage)->withQueryString();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
        ]);

        Category::create([
            'name' => $request->name,
            'type' => $request->type,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategorija sukurta sėkmingai.');
    }

    public function edit(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
        ]);

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategorija atnaujinta sėkmingai.');
    }

    public function destroy(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategorija ištrinta (soft delete).');
    }

    public function trashed(Request $request)
    {
        $query = Category::onlyTrashed()->where('user_id', Auth::id());

        if ($request->has('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        $perPage = $request->get('per_page', 10);
        $categories = $query->latest('deleted_at')->paginate($perPage)->withQueryString();

        return view('categories.trashed', compact('categories'));
    }

    public function restore($id)
    {
        $category = Category::onlyTrashed()
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $category->restore();

        return redirect()->route('categories.trashed')->with('success', 'Kategorija atkurta.');
    }

    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $category->forceDelete();

        return redirect()->route('categories.trashed')->with('success', 'Kategorija galutinai ištrinta.');
    }
}
