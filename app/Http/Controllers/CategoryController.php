<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
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

    public function destroy(Request $request, Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $deleteTransactions = $request->has('delete_transactions');

        if ($deleteTransactions) {
            $category->transactions()->delete(); // Soft delete
        }

        $category->delete();

        return redirect()->route('categories.index')->with(
            'success',
            'Kategorija ištrinta' . ($deleteTransactions ? ' kartu su transakcijomis.' : '.')
        );
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

        Transaction::onlyTrashed()
            ->where('user_id', Auth::id())
            ->where('category_id', $category->id)
            ->restore();

        return redirect()->route('categories.trashed')->with('success', 'Kategorija ir jos transakcijos atkurti.');
    }

public function forceDelete(Request $request, $id)
{
    $category = Category::onlyTrashed()
        ->where('user_id', Auth::id())
        ->findOrFail($id);

    $deleteTransactions = $request->has('delete_transactions');

    if ($deleteTransactions) {
        // Ištriname transakcijas visam laikui
        Transaction::withTrashed()
            ->where('user_id', Auth::id())
            ->where('category_id', $category->id)
            ->forceDelete();
    } else {
        // Paliekame transakcijas, bet atskiriame jas nuo ištrintos kategorijos
        Transaction::withTrashed()
            ->where('user_id', Auth::id())
            ->where('category_id', $category->id)
            ->update(['category_id' => null]);
    }

    $category->forceDelete();

    return redirect()->route('categories.trashed')->with(
        'success',
        'Kategorija ištrinta visam laikui' . ($deleteTransactions ? ' kartu su transakcijomis.' : '.')
    );
}


    public function forceDeleteWithTransactions(Request $request, $id)
    {
        $category = Category::onlyTrashed()
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        Transaction::withTrashed()
            ->where('user_id', Auth::id())
            ->where('category_id', $category->id)
            ->forceDelete();

        $category->forceDelete();

        return redirect()->route('categories.trashed')->with('success', 'Kategorija ir jos transakcijos ištrintos visam laikui.');
    }
}
