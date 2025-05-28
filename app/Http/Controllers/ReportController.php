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

        // Grupavimas pagal kategoriją
        $byCategory = $transactions->groupBy('category.name')->map(function ($group) {
            return [
                'type' => $group->first()->category->type,
                'sum' => $group->sum('amount'),
            ];
        });

        // Analizė
        $min = $transactions->min('amount');
        $max = $transactions->max('amount');
        $avg = $transactions->avg('amount');

        // Pajamos, išlaidos ir balansas
        $incomeTotal = $transactions->filter(fn($t) => $t->category->type === 'income')->sum('amount');
        $expenseTotal = $transactions->filter(fn($t) => $t->category->type === 'expense')->sum('amount');
        $balance = $incomeTotal - $expenseTotal;

        // Pajamų grafiko duomenys
        $incomeData = $transactions->filter(fn($t) => $t->category->type === 'income')
            ->groupBy(fn($t) => $t->category->name)
            ->map(fn($group) => $group->sum('amount'));

        // Išlaidų grafiko duomenys
        $expenseData = $transactions->filter(fn($t) => $t->category->type === 'expense')
            ->groupBy(fn($t) => $t->category->name)
            ->map(fn($group) => $group->sum('amount'));

        // Bendras grafikas (pajamos ir išlaidos vienoje vietoje)
        $combinedData = $transactions->groupBy(fn($t) => $t->category->name)
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
