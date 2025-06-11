<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Str; // Importuojame Str pagalbinę klasę

class ReportController extends Controller
{
    /**
     * Display a financial report based on user transactions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Base query for transactions for the authenticated user,
        // eager-loading the category, including soft-deleted ones.
        $query = Transaction::where('user_id', Auth::id())
            ->with(['category' => function ($q) {
                $q->withTrashed();
            }]);

        // Apply date filtering if 'date_from' is provided.
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        // Apply date filtering if 'date_to' is provided.
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Fetch all transactions that match the filters.
        // This collection will be used for various calculations and data preparations.
        $transactions = $query->get();

        // --- Sumariniai duomenys ---

        // Calculate total income based on filtered transactions.
        $incomeTotal = $transactions
            ->filter(fn($t) => $t->category && $t->category->type === 'income')
            ->sum('amount');

        // Calculate total expense based on filtered transactions.
        $expenseTotal = $transactions
            ->filter(fn($t) => $t->category && $t->category->type === 'expense')
            ->sum('amount');

        // Calculate the overall balance.
        $balance = $incomeTotal - $expenseTotal;

        // --- Analizės duomenys (Min, Max, Avg) ---

        // Find the transaction with the minimum amount.
        // Eager load the category for display in the Blade view.
        // Order by amount ascending and take the first one.
        $minTransaction = (clone $query)->orderBy('amount', 'asc')->first();

        // Find the transaction with the maximum amount.
        // Eager load the category for display in the Blade view.
        // Order by amount descending and take the first one.
        $maxTransaction = (clone $query)->orderBy('amount', 'desc')->first();

        // Calculate the average transaction amount.
        $avgTotal = $transactions->avg('amount');


        // --- Duomenys pagal kategorijas (lentelė ir grafikai) ---

        // Group transactions by category name (or 'KATEGORIJA IŠTRINTA' if category is null).
        // Map the groups to include type, sum, and trashed status for display.
        $byCategory = $transactions
            ->groupBy(function ($t) {
                return $t->category->name ?? 'KATEGORIJA IŠTRINTA';
            })
            ->map(function ($group) {
                $first = $group->first(); // Get the first transaction in the group to determine type and trashed status
                return [
                    'type' => $first->category->type ?? 'unknown',
                    'sum' => $group->sum('amount'),
                    'trashed' => $first->category
                        ? ($first->category->trashed() ? 'soft' : null) // Soft-deleted category
                        : 'hard', // Category completely deleted
                ];
            });

        // Prepare data for the Income Chart (grouped by category and summed).
        $incomeData = $transactions
            ->filter(fn($t) => $t->category && $t->category->type === 'income')
            ->groupBy(fn($t) => $t->category->name ?? 'KATEGORIJA IŠTRINTA')
            ->map(fn($group) => $group->sum('amount'));

        // Prepare data for the Expense Chart (grouped by category and summed).
        $expenseData = $transactions
            ->filter(fn($t) => $t->category && $t->category->type === 'expense')
            ->groupBy(fn($t) => $t->category->name ?? 'KATEGORIJA IŠTRINTA')
            ->map(fn($group) => $group->sum('amount'));

        // Prepare data for the Combined Pie Chart.
        // This maps each transaction to a combined structure, then groups and sums by category.
        $combinedData = $transactions
            ->map(function ($t) {
                if (!$t->category) {
                    return [
                        'name' => 'KATEGORIJA IŠTRINTA',
                        'type' => 'unknown',
                        'sum' => $t->amount,
                        'trashed' => 'hard',
                    ];
                }

                return [
                    'name' => $t->category->name,
                    'type' => $t->category->type,
                    'sum' => $t->amount,
                    'trashed' => $t->category->trashed() ? 'soft' : null,
                ];
            })
            ->groupBy('name') // Group by the category name (or 'KATEGORIJA IŠTRINTA')
            ->map(function ($group) {
                $first = $group->first(); // Get the first item to retain type and trashed status for the group
                return [
                    'type' => $first['type'],
                    'sum' => $group->sum('sum'), // Sum up the individual transaction sums
                    'trashed' => $first['trashed'],
                ];
            });

        // Return the view with all prepared data.
        return view('reports.index', compact(
            'byCategory',
            'minTransaction', // Now passing the full Transaction object
            'maxTransaction', // Now passing the full Transaction object
            'avgTotal',       // Renamed from 'avg' to 'avgTotal' for consistency
            'incomeTotal',
            'expenseTotal',
            'balance',
            'incomeData',
            'expenseData',
            'combinedData'
        ));
    }
}
