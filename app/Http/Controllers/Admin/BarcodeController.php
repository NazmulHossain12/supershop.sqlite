<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function resolve($barcode)
    {
        $product = Product::where('barcode', $barcode)
            ->orWhere('sku', $barcode)
            ->first();

        if ($product) {
            // Redirect to Filament resource edit page
            // Assuming the structure is /admin/products/{record}/edit
            return redirect("/admin/products/{$product->id}/edit");
        }

        return redirect('/admin/products')->with('error', "Product with barcode {$barcode} not found.");
    }

    public function lookupAjax($barcode)
    {
        $product = Product::with(['supplier', 'brand', 'category'])
            ->where('barcode', $barcode)
            ->orWhere('sku', $barcode)
            ->first();

        if ($product) {
            return response()->json([
                'success' => true,
                'product' => [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => number_format($product->regular_price, 2),
                    'stock' => $product->stock_quantity,
                    'supplier' => $product->supplier?->name ?? 'N/A',
                    'brand' => $product->brand?->name ?? 'N/A',
                    'category' => $product->category?->name ?? 'N/A',
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found'
        ]);
    }

    public function printLabels()
    {
        $products = Product::where('stock_quantity', '<=', 0)
            ->orWhere('created_at', '>=', now()->subDays(7))
            ->get();

        if ($products->isEmpty()) {
            return back()->with('info', 'No products found matching the criteria (zero stock or new).');
        }

        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('admin.barcode.labels', compact('products'))
            ->setOption('page-size', 'A4')
            ->setOption('margin-bottom', 0)
            ->setOption('margin-top', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0);

        return $pdf->download('shelf-labels-' . date('Y-m-d') . '.pdf');
    }
}
