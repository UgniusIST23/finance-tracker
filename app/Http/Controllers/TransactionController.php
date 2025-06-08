<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $baseQuery = Transaction::where('user_id', Auth::id())
            ->with(['category' => fn($query) => $query->withTrashed()]);

        if ($request->filled('date_from')) {
            $baseQuery->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $baseQuery->where('date', '<=', $request->date_to);
        }

        if ($request->filled('category_id')) {
            $baseQuery->where('category_id', $request->category_id);
        }

        $transactions = (clone $baseQuery)->latest('date')->paginate($perPage);
        $allFiltered = (clone $baseQuery)->get();

        // Skaičiuojame tik tas transakcijas, kurios turi validžią kategoriją
        $income = $allFiltered->filter(fn($t) => $t->category && $t->category->type === 'income')->sum('amount');
        $expense = $allFiltered->filter(fn($t) => $t->category && $t->category->type === 'expense')->sum('amount');
        $balance = $income - $expense;

        // Rodyti tik tas kategorijas, kurios turi bent vieną aktyvią transakciją
        $categories = Category::withTrashed()
            ->where('user_id', Auth::id())
            ->whereHas('transactions', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->get();

        return view('transactions.index', compact(
            'transactions',
            'income',
            'expense',
            'balance',
            'categories'
        ));
    }

    public function create()
    {
        $categories = Category::where('user_id', Auth::id())->get();

        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:3',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transakcija pridėta sėkmingai.');
    }

    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = Category::withTrashed()
            ->where('user_id', Auth::id())
            ->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:3',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $transaction->update([
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transakcija atnaujinta.');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transakcija ištrinta.');
    }
}
