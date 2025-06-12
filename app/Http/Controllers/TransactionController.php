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
            if ($request->category_id == -1) {
                $baseQuery->where(function($query) {
                    $query->whereNull('category_id')
                          ->orWhereDoesntHave('category', function($q) {
                              $q->withTrashed();
                          });
                });
            } else {
                $baseQuery->where('category_id', $request->category_id);
            }
        }

        // Tikriname, ar pasirinkta "all" (Visi)
        if ($perPage === 'all') {
            $transactions = $baseQuery->latest('date')->get();
            $selectedPerPage = 'all'; // Nustatome kintamąjį, kuris bus perduotas į view
        } else {
            $perPage = (int)$perPage; // Konvertuojame į integer, jei tai skaičius
            $transactions = $baseQuery->latest('date')->paginate($perPage)->withQueryString();
            $selectedPerPage = $perPage; // Nustatome kintamąjį su skaičiumi
        }
        
        $income = (clone $baseQuery)
            ->whereHas('category', fn($q) => $q->where('type', 'income')->withTrashed())
            ->sum('amount');

        $expense = (clone $baseQuery)
            ->whereHas('category', fn($q) => $q->where('type', 'expense')->withTrashed())
            ->sum('amount');

        $balance = $income - $expense;

        $categories = Category::where('user_id', Auth::id())
            ->withTrashed()
            ->get();

        return view('transactions.index', compact(
            'transactions',
            'income',
            'expense',
            'balance',
            'categories',
            'selectedPerPage' // Perduodame selectedPerPage kintamąjį į view
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
        $categories = Category::where('user_id', Auth::id())
            ->withTrashed()
            ->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
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
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transakcija ištrinta.');
    }
}
