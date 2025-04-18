<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Job;
use Carbon\Carbon;

class FinancialController extends Controller
{
    public function getFinancialReport()
    {


        // last 6 months
        $months = collect(range(0, 5))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        })->reverse(); // ببدأ من الأقدم

        $data = $months->map(function ($month) {

            $user = auth()->user();
            // total income for the month
            $totalIncome =Job::where('user_id', $user->id)
            ->where('created_at', 'like', "$month%")->sum('salary_amount');
            // total expenses for the month
            $totalExpenses =Expense::where('user_id', $user->id)
            ->where('created_at', 'like', "$month%")->sum('expenses_amount');

            return [
                'month' => Carbon::parse($month)->format('M'), // Convert "2025-03" to "Mar"
                'income' => $totalIncome,
                'expenses' => $totalExpenses,
            ];
        });

        return response()->json($data);
    }


    public function getExpensesPercentage()
    {
        // استرجاع إجمالي المصروفات
        $totalExpenses = Expense::sum('expenses_amount');

        // جلب المصروفات مجمّعة حسب الفئة
        $categories = Expense::selectRaw('category, SUM(expenses_amount) as total')
            ->groupBy('category')
            ->get();

        // حساب النسب لكل فئة
        $data = $categories->map(function ($item) use ($totalExpenses) {
            return [
                'category' => $item->category,
                'percentage' => round(($item->total / $totalExpenses) * 100, 2)
            ];
        });

        return response()->json($data);
    }

}
