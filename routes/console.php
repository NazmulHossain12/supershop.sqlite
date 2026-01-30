use Illuminate\Support\Facades\Schedule;
use App\Models\Transaction;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProfitAlert;

Schedule::command('profit:alert')->weeklyOn(1, '08:00');

Artisan::command('profit:alert', function () {
$lastWeekStart = now()->subWeek()->startOfWeek();
$lastWeekEnd = now()->subWeek()->endOfWeek();

$prevWeekStart = now()->subWeeks(2)->startOfWeek();
$prevWeekEnd = now()->subWeeks(2)->endOfWeek();

// Calculate Profits
$lastWeekProfit = Transaction::whereBetween('transaction_date', [$lastWeekStart, $lastWeekEnd])
->selectRaw('SUM(CASE WHEN type="sale" THEN amount ELSE -amount END) as profit')
->value('profit') ?? 0;

$prevWeekProfit = Transaction::whereBetween('transaction_date', [$prevWeekStart, $prevWeekEnd])
->selectRaw('SUM(CASE WHEN type="sale" THEN amount ELSE -amount END) as profit')
->value('profit') ?? 0;

if ($prevWeekProfit > 0) {
$dropPercent = (($prevWeekProfit - $lastWeekProfit) / $prevWeekProfit) * 100;

if ($dropPercent >= 20) {
// Find Top 5 Sales Drops
// Sum quantities by product for both weeks (only Completed/Paid orders)
$lastWeekSales = OrderItem::whereHas('order', function($q) use ($lastWeekStart, $lastWeekEnd) {
$q->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
->whereNotIn('status', ['Cancelled', 'Decline']);
})
->groupBy('product_id')
->selectRaw('product_id, SUM(quantity) as total_qty')
->pluck('total_qty', 'product_id');

$prevWeekSales = OrderItem::whereHas('order', function($q) use ($prevWeekStart, $prevWeekEnd) {
$q->whereBetween('created_at', [$prevWeekStart, $prevWeekEnd])
->whereNotIn('status', ['Cancelled', 'Decline']);
})
->groupBy('product_id')
->selectRaw('product_id, SUM(quantity) as total_qty')
->pluck('total_qty', 'product_id');

$drops = [];
foreach ($prevWeekSales as $productId => $prevQty) {
$lastQty = $lastWeekSales[$productId] ?? 0;
if ($lastQty < $prevQty) { $drops[]=[ 'product_id'=> $productId,
    'prev_qty' => $prevQty,
    'last_qty' => $lastQty,
    'drop_qty' => $prevQty - $lastQty
    ];
    }
    }

    usort($drops, fn($a, $b) => $b['drop_qty'] <=> $a['drop_qty']);
        $topDropsData = array_slice($drops, 0, 5);

        $topDrops = [];
        foreach($topDropsData as $d) {
        $product = \App\Models\Product::find($d['product_id']);
        $topDrops[] = array_merge($d, ['name' => $product->name ?? 'Unknown']);
        }

        Mail::to(config('mail.from.address'))->send(new ProfitAlert($lastWeekProfit, $prevWeekProfit, $dropPercent,
        $topDrops));
        $this->info('Profit alert sent successfully.');
        } else {
        $this->info('Profit drop was ' . round($dropPercent, 2) . '%, no alert sent.');
        }
        }
        })->purpose('Simulate or run manual profit drop check');