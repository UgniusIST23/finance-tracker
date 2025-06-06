<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Category;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', Auth::id())->with('category');

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $transactions = $query->get();

        $byCategory = $transactions->filter(fn($t) => $t->category)
            ->groupBy('category.name')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'type' => $first->category->type,
                    'sum' => $group->sum('amount'),
                ];
            });

        $min = $transactions->min('amount');
        $max = $transactions->max('amount');
        $avg = $transactions->avg('amount');

        $incomeTotal = $transactions->filter(fn($t) => $t->category && $t->category->type === 'income')->sum('amount');
        $expenseTotal = $transactions->filter(fn($t) => $t->category && $t->category->type === 'expense')->sum('amount');
        $balance = $incomeTotal - $expenseTotal;

        $incomeData = $transactions->filter(fn($t) => $t->category && $t->category->type === 'income')
            ->groupBy(fn($t) => $t->category->name)
            ->map(fn($group) => $group->sum('amount'));

        $expenseData = $transactions->filter(fn($t) => $t->category && $t->category->type === 'expense')
            ->groupBy(fn($t) => $t->category->name)
            ->map(fn($group) => $group->sum('amount'));

        $combinedData = $transactions->filter(fn($t) => $t->category)
            ->groupBy(fn($t) => $t->category->name)
            ->map(fn($group) => [
                'type' => $group->first()->category->type,
                'sum' => $group->sum('amount'),
            ]);

        return view('reports.index', compact(
            'byCategory',
            'min',
            'max',
            'avg',
            'incomeTotal',
            'expenseTotal',
            'balance',
            'incomeData',
            'expenseData',
            'combinedData'
        ));
    }
}
