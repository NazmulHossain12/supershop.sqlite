<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Shop') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-8">

                <!-- Sidebar Filters -->
                <div class="w-full md:w-1/4 space-y-8">

                    <!-- Search Mobile (Visible only on small screens) -->
                    <div class="md:hidden">
                        <form action="{{ route('shop.index') }}" method="GET">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search products..."
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </form>
                    </div>

                    <!-- Categories -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="font-bold text-lg mb-4 text-gray-900 dark:text-white">Categories</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('shop.index', array_merge(request()->query(), ['category' => null])) }}"
                                    class="block px-2 py-1 rounded {{ !request('category') ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300 font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-primary-600' }}">
                                    All Categories
                                </a>
                            </li>
                            @foreach($categories as $category)
                                <li>
                                    <a href="{{ route('shop.index', array_merge(request()->query(), ['category' => $category->slug])) }}"
                                        class="flex justify-between items-center px-2 py-1 rounded {{ request('category') == $category->slug ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300 font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-primary-600' }}">
                                        <span>{{ $category->name }}</span>
                                        <span
                                            class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ $category->products_count }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Price Filter -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="font-bold text-lg mb-4 text-gray-900 dark:text-white">Price Range</h3>
                        <form action="{{ route('shop.index') }}" method="GET">
                            <!-- Preserve other query params -->
                            @foreach(request()->except(['min_price', 'max_price', 'page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach

                            <div class="flex items-center gap-2 mb-4">
                                <input type="number" name="min_price" value="{{ request('min_price') }}"
                                    placeholder="Min"
                                    class="w-1/2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <span class="text-gray-500">-</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}"
                                    placeholder="Max"
                                    class="w-1/2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                            <button type="submit"
                                class="w-full bg-gray-900 dark:bg-gray-700 text-white py-2 rounded-md text-sm font-semibold hover:bg-gray-800 dark:hover:bg-gray-600 transition">Apply</button>
                        </form>
                    </div>

                </div>

                <!-- Main Content -->
                <div class="w-full md:w-3/4">

                    <!-- Toolbar -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <p class="text-gray-600 dark:text-gray-400">
                            Showing <span
                                class="font-bold text-gray-900 dark:text-white">{{ $products->firstItem() ?? 0 }}</span>
                            - <span
                                class="font-bold text-gray-900 dark:text-white">{{ $products->lastItem() ?? 0 }}</span>
                            of <span class="font-bold text-gray-900 dark:text-white">{{ $products->total() }}</span>
                            results
                        </p>

                        <div class="flex items-center gap-2">
                            <label for="sort" class="text-sm text-gray-600 dark:text-gray-400">Sort by:</label>
                            <form action="{{ route('shop.index') }}" method="GET" id="sortForm">
                                @foreach(request()->except(['sort', 'page']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <select name="sort" id="sort" onchange="document.getElementById('sortForm').submit()"
                                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-1 pl-2 pr-8">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest
                                    </option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                        Price: Low to High</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                        Price: High to Low</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    @if($products->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-12 text-center shadow-sm">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No products found</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6">Try adjusting your search or filters to find
                                what you're looking for.</p>
                            <a href="{{ route('shop.index') }}" class="text-primary-600 font-bold hover:underline">Clear all
                                filters</a>
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
</x-app-layout>