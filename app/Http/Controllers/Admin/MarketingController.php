<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\MarketingMetric;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        // Active Campaigns
        $campaigns = Campaign::where('is_active', true)
            ->orderBy('revenue', 'desc')
            ->get();

        // Overall Metrics
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])->sum('grand_total');
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Traffic Sources (simulated from orders)
        $trafficSources = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('COUNT(*) as count, SUM(grand_total) as revenue'))
            ->groupBy('status')
            ->get();

        // Conversion Funnel (from transactions)
        $visitors = Order::whereBetween('created_at', [$startDate, $endDate])->count() * 10; // Simulated
        $addToCarts = Order::whereBetween('created_at', [$startDate, $endDate])->count() * 3;
        $checkouts = Order::whereBetween('created_at', [$startDate, $endDate])->count() * 1.5;
        $purchases = $totalOrders;

        // Daily Revenue (last 14 days)
        $dailyRevenue = Order::whereBetween('created_at', [now()->subDays(13)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('revenue', 'date');

        // Fill missing dates
        $revenueData = collect();
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $revenueData[$date] = $dailyRevenue[$date] ?? 0;
        }

        // Top Products by Revenue
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('products.name', DB::raw('SUM(order_items.quantity * order_items.price) as revenue'), DB::raw('SUM(order_items.quantity) as units_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // Calculate conversion rate
        $conversionRate = $visitors > 0 ? ($purchases / $visitors) * 100 : 0;

        return view('admin.marketing.index', compact(
            'campaigns',
            'totalRevenue',
            'totalOrders',
            'avgOrderValue',
            'revenueData',
            'topProducts',
            'startDate',
            'endDate',
            'visitors',
            'addToCarts',
            'checkouts',
            'purchases',
            'conversionRate'
        ));
    }

    public function campaigns()
    {
        $campaigns = Campaign::latest()->paginate(20);
        return view('admin.marketing.campaigns', compact('campaigns'));
    }
}
