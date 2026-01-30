<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Sales Overview
        $todaySales = Order::whereDate('created_at', today())->sum('grand_total');
        $weekSales = Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('grand_total');
        $monthSales = Order::whereMonth('created_at', now()->month)->sum('grand_total');

        // Order Counts
        $todayOrders = Order::whereDate('created_at', today())->count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();

        // Top Products (by revenue)
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity * order_items.price) as revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // Recent Orders
        $recentOrders = Order::with('user')->latest()->limit(10)->get();

        // Revenue Chart Data (Last 7 days)
        $revenueData = Order::whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');

        // Fill missing dates with 0
        $chartData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartData[$date] = $revenueData[$date] ?? 0;
        }

        // Low Stock Alerts (threshold: 10)
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)
            ->where('stock_quantity', '>', 0)
            ->get();

        $outOfStockProducts = Product::where('stock_quantity', '<=', 0)->count();

        // Supplier Liability
        $totalLiability = Supplier::sum('current_balance');

        return view('admin.dashboard', compact(
            'todaySales',
            'weekSales',
            'monthSales',
            'todayOrders',
            'totalOrders',
            'pendingOrders',
            'topProducts',
            'recentOrders',
            'chartData',
            'lowStockProducts',
            'outOfStockProducts',
            'totalLiability'
        ));
    }
}
