<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $start = now()->startOfMonth()->subMonths(5);
        $monthExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', sold_at)",
            'pgsql' => "TO_CHAR(sold_at, 'YYYY-MM')",
            default => "DATE_FORMAT(sold_at, '%Y-%m')",
        };
        $salesByMonth = Sale::query()
            ->where('sold_at', '>=', $start)
            ->selectRaw("$monthExpression as month, SUM(total) as revenue")
            ->groupBy('month')
            ->pluck('revenue', 'month');
        $months = collect(range(0, 5))->map(function (int $offset) use ($start, $salesByMonth): array {
            $month = $start->copy()->addMonths($offset);

            return [
                'label' => $month->translatedFormat('M'),
                'revenue' => (float) ($salesByMonth[$month->format('Y-m')] ?? 0),
            ];
        });
        $chartMax = max(1, (float) $months->max('revenue'));

        return view('dashboard', [
            'stats' => [
                'products' => Product::query()->count(),
                'users' => User::query()->count(),
                'categories' => Category::query()->count(),
                'revenue' => (float) Sale::query()->sum('total'),
            ],
            'recentProducts' => Product::query()->with('category')->latest()->limit(5)->get(),
            'recentUsers' => User::query()->latest()->limit(5)->get(),
            'activities' => ActivityLog::query()->with('user')->latest()->limit(8)->get(),
            'lowStockProducts' => Product::query()->with('category')->where('stock_quantity', '<=', 5)->orderBy('stock_quantity')->limit(5)->get(),
            'saleableProducts' => Product::query()->where('status', 'active')->where('stock_quantity', '>', 0)->orderBy('name')->get(),
            'months' => $months,
            'chartMax' => $chartMax,
        ]);
    }
}
