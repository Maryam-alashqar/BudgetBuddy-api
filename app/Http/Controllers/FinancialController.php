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
        $userId = auth()->id();

        $totalExpenses = Expense::where('user_id', $userId)->sum('expenses_amount');

        // الأقسام الثابتة في التطبيق
        $allCategories = ['tax', 'loans', 'need', 'want', 'primary_bill'];

        // إجمالي المصروفات لكل قسم من الموجودة
        $categories = Expense::selectRaw('category, SUM(expenses_amount) as total')
            ->where('user_id', $userId)
            ->groupBy('category')
            ->get()
            ->keyBy('category'); // نخزن حسب اسم القسم

        // تجهيز البيانات بالنسب المئوية
        $data = collect($allCategories)->map(function ($category) use ($categories, $totalExpenses) {
            $total = $categories[$category]->total ?? 0;

            return [
                'category' => $category,
                'percentage' => $totalExpenses > 0
                    ? round(($total / $totalExpenses) * 100, 2)
                    : 0
            ];
        });

        return response()->json($data);
    }

}
