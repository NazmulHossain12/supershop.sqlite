<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->where('status', true);

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->has('category') && $request->category != '') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Price Filter
        if ($request->has('min_price') && $request->min_price != '') {
            $query->where('sale_price', '>=', $request->min_price)
                ->orWhere(function ($q) use ($request) {
                    $q->whereNull('sale_price')
                        ->where('regular_price', '>=', $request->min_price);
                });
        }

        if ($request->has('max_price') && $request->max_price != '') {
            $query->where(function ($q) use ($request) {
                $q->where('sale_price', '<=', $request->max_price)
                    ->orWhere(function ($q) use ($request) {
                        $q->whereNull('sale_price')
                            ->where('regular_price', '<=', $request->max_price);
                    });
            });
        }

        // Sort
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    // This is complex with sale_price vs regular_price logic in standard SQL
                    // Simplification: Sort by regular_price for now, or use a computed column
                    $query->orderBy('regular_price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('regular_price', 'desc');
                    break;
                case 'newest':
                    $query->latest();
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->get();

        return view('shop.index', compact('products', 'categories'));
    }
}
