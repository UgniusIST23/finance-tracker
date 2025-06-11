<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', Auth::id())
            ->with(['category' => function ($q) {
                $q->withTrashed();
            }]);

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $transactions = $query->get();

        $incomeTotal = $transactions
            ->filter(fn($t) => $t->category && $t->category->type === 'income')
            ->sum('amount');

        $expenseTotal = $transactions
            ->filter(fn($t) => $t->category && $t->category->type === 'expense')
            ->sum('amount');

        $balance = $incomeTotal - $expenseTotal;

        $minTransaction = (clone $query)->orderBy('amount', 'asc')->first();

        $maxTransaction = (clone $query)->orderBy('amount', 'desc')->first();

        $avgTotal = $transactions->avg('amount');

        $byCategory = $transactions
            ->groupBy(function ($t) {
                return $t->category->name ?? 'KATEGORIJA IŠTRINTA';
            })
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'type' => $first->category->type ?? 'unknown',
                    'sum' => $group->sum('amount'),
                    'trashed' => $first->category
                        ? ($first->category->trashed() ? 'soft' : null)
                        : 'hard',
                ];
            });

        $incomeData = $transactions
            ->filter(fn($t) => $t->category && $t->category->type === 'income')
            ->groupBy(fn($t) => $t->category->name ?? 'KATEGORIJA IŠTRINTA')
            ->map(fn($group) => $group->sum('amount'));

        $expenseData = $transactions
            ->filter(fn($t) => $t->category && $t->category->type === 'expense')
            ->groupBy(fn($t) => $t->category->name ?? 'KATEGORIJA IŠTRINTA')
            ->map(fn($group) => $group->sum('amount'));

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
            ->groupBy('name') 
            ->map(function ($group) {
                $first = $group->first(); 
                return [
                    'type' => $first['type'],
                    'sum' => $group->sum('sum'), 
                    'trashed' => $first['trashed'],
                ];
            });

        return view('reports.index', compact(
            'byCategory',
            'minTransaction', 
            'maxTransaction', 
            'avgTotal',      
            'incomeTotal',
            'expenseTotal',
            'balance',
            'incomeData',
            'expenseData',
            'combinedData'
        ));
    }
}
