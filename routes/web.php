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
});

require __DIR__ . '/auth.php';

// Admin Routes
Route::middleware(['auth', 'role:Super Admin|Store Manager'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/marketing', [App\Http\Controllers\Admin\MarketingController::class, 'index'])->name('admin.marketing.index');
    Route::get('/marketing/campaigns', [App\Http\Controllers\Admin\MarketingController::class, 'campaigns'])->name('admin.marketing.campaigns');
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->names('admin.categories');
    Route::resource('brands', App\Http\Controllers\Admin\BrandController::class)->names('admin.brands');
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class)->names('admin.products');
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update'])->names('admin.orders');
    Route::resource('coupons', App\Http\Controllers\Admin\CouponController::class)->names('admin.coupons');
});
