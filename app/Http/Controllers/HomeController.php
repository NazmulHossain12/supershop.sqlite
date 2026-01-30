<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application landing page.
     */
    public function index()
    {
        // Fetch real featured products with relations
        $featuredProducts = \App\Models\Product::with('category')
            ->where('featured', true)
            ->where('status', true)
            ->take(8)
            ->get();

        // Pass to view
        return view('home', compact('featuredProducts'));
    }
}
