<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));

        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $transactions = $request->user()
            ->transactions()
            ->with('category')
            ->whereBetween('date', [$start, $end])
            ->get();

        $income = $transactions
            ->where('category.type', 'income')
            ->sum('amount');

        $expense = $transactions
            ->where('category.type', 'expense')
            ->sum('amount');

        return response()->json([
            'month' => $start->format('Y-m'),
            'total_income' => round($income, 2),
            'total_expense' => round($expense, 2),
            'balance' => round($income - $expense, 2),
        ]);
    }
}
