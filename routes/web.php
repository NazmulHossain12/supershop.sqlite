<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [App\Http\Controllers\ShopController::class, 'index'])->name('shop.index');
Route::get('/products/{product:slug}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
Route::post('/products/{product}/reviews', [App\Http\Controllers\ProductController::class, 'storeReview'])->name('products.reviews.store')->middleware('auth');

// Cart Routes
Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{product}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');

// Wishlist Routes
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [App\Http\Controllers\WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{wishlist}', [App\Http\Controllers\WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

// Coupon Routes
Route::post('/coupon/apply', [App\Http\Controllers\CouponController::class, 'apply'])->name('coupon.apply');
Route::delete('/coupon/remove', [App\Http\Controllers\CouponController::class, 'remove'])->name('coupon.remove');

// Checkout Routes
Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');

// Order History Routes
Route::resource('orders', App\Http\Controllers\OrderController::class)->only(['index', 'show'])->middleware(['auth']);

Route::get('/style-guide', function () {
    return view('style-guide');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/rewards', App\Livewire\Customer\MyRewards::class)->name('rewards');
});

Route::get('/locale/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'bn', 'id', 'ar'])) {
        session()->put('locale', $lang);
    }
    return back();
})->name('locale.switch');

require __DIR__ . '/auth.php';

// Admin Routes
Route::middleware(['auth', 'role:Super Admin|Store Manager'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/pos', App\Livewire\Admin\PosTerminal::class)->name('admin.pos');
    Route::get('/marketing', [App\Http\Controllers\Admin\MarketingController::class, 'index'])->name('admin.marketing.index');
    Route::get('/marketing/campaigns', [App\Http\Controllers\Admin\MarketingController::class, 'campaigns'])->name('admin.marketing.campaigns');
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->names('admin.categories');
    Route::resource('brands', App\Http\Controllers\Admin\BrandController::class)->names('admin.brands');
    Route::resource('suppliers', App\Http\Controllers\Admin\SupplierController::class)->names('admin.suppliers');
    // Route::resource('products', App\Http\Controllers\Admin\ProductController::class)->names('admin.products');
    Route::get('/products/print-labels', [App\Http\Controllers\Admin\BarcodeController::class, 'printLabels'])->name('admin.products.print-labels');
    Route::delete('products/{product}/images/{image}', [App\Http\Controllers\Admin\ProductController::class, 'destroyImage'])->name('admin.products.images.destroy');
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update'])->names('admin.orders');
    Route::resource('purchase-orders', App\Http\Controllers\Admin\PurchaseOrderController::class)->names('admin.purchase-orders');
    Route::patch('purchase-orders/{purchase_order}/status', [App\Http\Controllers\Admin\PurchaseOrderController::class, 'updateStatus'])->name('admin.purchase-orders.update-status');
    Route::post('purchase-orders/{purchase_order}/payments', [App\Http\Controllers\Admin\PurchaseOrderController::class, 'addPayment'])->name('admin.purchase-orders.add-payment');
    Route::resource('coupons', App\Http\Controllers\Admin\CouponController::class)->names('admin.coupons');
    // Reports & Analytics
    // Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/p-and-l/download', [App\Http\Controllers\Admin\ReportController::class, 'downloadPandL'])->name('admin.reports.p-and-l.download');

    Route::get('/reports/balance-sheet', [App\Http\Controllers\Admin\ReportController::class, 'balanceSheet'])->name('admin.reports.balance-sheet');
    Route::get('/reports/balance-sheet/download', [App\Http\Controllers\Admin\ReportController::class, 'downloadBalanceSheet'])->name('admin.reports.balance-sheet.download');

    Route::get('/reports/trial-balance', [App\Http\Controllers\Admin\ReportController::class, 'trialBalance'])->name('admin.reports.trial-balance');
    Route::get('/reports/trial-balance/download', [App\Http\Controllers\Admin\ReportController::class, 'downloadTrialBalance'])->name('admin.reports.trial-balance.download');

    Route::get('/reports/cashflow', [App\Http\Controllers\Admin\ReportController::class, 'cashflow'])->name('admin.reports.cashflow');
    Route::get('/reports/cashflow/download', [App\Http\Controllers\Admin\ReportController::class, 'downloadCashflow'])->name('admin.reports.cashflow.download');

    Route::get('/reports/ledger', [App\Http\Controllers\Admin\ReportController::class, 'ledger'])->name('admin.reports.ledger');
    Route::get('/reports/ledger/download', [App\Http\Controllers\Admin\ReportController::class, 'downloadLedger'])->name('admin.reports.ledger.download');

    Route::get('/reports/invoices', [App\Http\Controllers\Admin\ReportController::class, 'invoices'])->name('admin.reports.invoices');
    Route::get('/reports/invoices/download', [App\Http\Controllers\Admin\ReportController::class, 'downloadInvoices'])->name('admin.reports.invoices.download');

    Route::get('/reports/vat', [App\Http\Controllers\Admin\ReportController::class, 'vatReport'])->name('admin.reports.vat');
    Route::get('/reports/vat/download', [App\Http\Controllers\Admin\ReportController::class, 'downloadVat'])->name('admin.reports.vat.download');

    Route::get('/reports/inventory', [App\Http\Controllers\Admin\ReportController::class, 'inventory'])->name('admin.reports.inventory');
    Route::get('/reports/inventory/download', [App\Http\Controllers\Admin\ReportController::class, 'downloadInventory'])->name('admin.reports.inventory.download');

    Route::get('/accounting/vat', [App\Http\Controllers\Admin\ReportController::class, 'vatReport'])->name('admin.accounting.vat');

    // Barcode Routes
    Route::get('/products/resolve-barcode/{barcode}', [App\Http\Controllers\Admin\BarcodeController::class, 'resolve'])->name('admin.products.resolve-barcode');
    Route::get('/products/barcode-lookup/{barcode}', [App\Http\Controllers\Admin\BarcodeController::class, 'lookupAjax'])->name('admin.products.barcode-lookup');
});
