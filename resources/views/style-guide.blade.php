<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Design System & Style Guide') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">

            <!-- Typography Section -->
            <section class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-2">Typography (Outfit)</h3>
                <div class="space-y-4">
                    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white">Heading 1: The Quick Brown Fox
                    </h1>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Heading 2: Jumps Over The Lazy Dog</h2>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">Heading 3: Should feel modern and
                        premium</h3>
                    <p class="text-base text-gray-600 dark:text-gray-400 max-w-2xl leading-relaxed">
                        Body Text: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor
                        incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
                        ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">
                        Small Text: Used for metadata, captions, or helper text.
                    </p>
                </div>
            </section>

            <!-- Colors Section -->
            <section class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-2">Color Palette</h3>

                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Primary (Brand Blue)</h4>
                <div class="grid grid-cols-2 md:grid-cols-11 gap-2 mb-6">
                    @foreach([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950] as $shade)
                        <div class="space-y-1">
                            <div class="h-12 w-full rounded-lg shadow-sm bg-primary-{{ $shade }}"></div>
                            <div class="text-xs text-center text-gray-500">{{ $shade }}</div>
                        </div>
                    @endforeach
                </div>

                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Secondary (Accent Gold)</h4>
                <div class="grid grid-cols-2 md:grid-cols-11 gap-2">
                    @foreach([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950] as $shade)
                        <div class="space-y-1">
                            <div class="h-12 w-full rounded-lg shadow-sm bg-secondary-{{ $shade }}"></div>
                            <div class="text-xs text-center text-gray-500">{{ $shade }}</div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Buttons & Badges -->
            <section class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-2">Interactive Elements
                </h3>

                <div class="flex flex-wrap gap-4 items-center mb-8">
                    <x-primary-button>Primary Action</x-primary-button>
                    <button
                        class="px-6 py-2.5 rounded-full border border-gray-300 dark:border-gray-600 font-semibold text-xs uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 transition">Secondary
                        Action</button>
                    <button class="text-primary-600 hover:text-primary-700 font-medium underline">Text Link</button>
                </div>

                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Badges</h4>
                <div class="flex flex-wrap gap-2">
                    <x-badge type="primary" label="New Arrival" />
                    <x-badge type="success" label="In Stock" />
                    <x-badge type="warning" label="Low Stock" />
                    <x-badge type="danger" label="Out of Stock" />
                    <x-badge type="info" label="Pre-order" />
                </div>
            </section>

            <!-- Components Preview -->
            <section class="bg-gray-100 dark:bg-gray-900 p-6 rounded-xl">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Component Examples</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Example Product Card -->
                    @php
                        $dummyProduct = (object) [
                            'name' => 'Premium Wireless Headphones',
                            'category' => (object) ['name' => 'Electronics'],
                            'regular_price' => 299.00,
                            'sale_price' => 249.00,
                            'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&auto=format&fit=crop&q=60',
                            'featured' => true,
                        ];

                        $dummyProduct2 = (object) [
                            'name' => 'Minimalist Watch',
                            'category' => (object) ['name' => 'Accessories'],
                            'regular_price' => 120.00,
                            'sale_price' => null,
                            'image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&auto=format&fit=crop&q=60',
                            'featured' => false,
                        ];
                    @endphp

                    <x-product-card :product="$dummyProduct" />
                    <x-product-card :product="$dummyProduct2" />
                </div>
            </section>

        </div>
    </div>
</x-app-layout>