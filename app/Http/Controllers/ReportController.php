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

        return view('reports.index', compact('byCategory', 'min', 'max', 'avg'));
    }
}
