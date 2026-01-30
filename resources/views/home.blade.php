<x-app-layout>
    <!-- Hero Section -->
    <div class="relative bg-gray-900 overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1600&auto=format&fit=crop&q=80"
                alt="Hero Background" class="w-full h-full object-cover opacity-40">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white tracking-tight mb-6 animate-slide-up">
                Discover <span class="text-primary-400">Premium</span><br>Quality Products
            </h1>
            <p class="text-xl text-gray-300 mb-8 max-w-2xl animate-fade-in" style="animation-delay: 0.2s">
                Shop the latest trends in electronics, fashion, and home essentials. Experience the best in class
                shopping with Supershop.
            </p>
            <div class="flex gap-4 animate-fade-in" style="animation-delay: 0.4s">
                <x-primary-button class="!text-sm !py-3 !px-8">
                    Shop Now
                </x-primary-button>
                <a href="#"
                    class="inline-flex items-center px-8 py-3 rounded-full border border-white text-white font-semibold text-sm uppercase tracking-widest hover:bg-white hover:text-gray-900 transition-colors">
                    View Brands
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-12 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-full text-primary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white">Free Shipping</h4>
                        <p class="text-xs text-gray-500">On all orders over $50</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-full text-primary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white">24/7 Support</h4>
                        <p class="text-xs text-gray-500">Get help when you need it</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-full text-primary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white">100% Payment Secure</h4>
                        <p class="text-xs text-gray-500">We ensure secure payment</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-full text-primary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white">Money Back Guarantee</h4>
                        <p class="text-xs text-gray-500">Return within 30 days</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Featured Products</h2>
                    <p class="text-gray-500 mt-2">Check out our new arrivals and top selling items</p>
                </div>
                <a href="#" class="text-primary-600 hover:text-primary-700 font-semibold flex items-center gap-1 group">
                    View All
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </div>

    <!-- Newsletter -->
    <div class="py-16 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-primary-600 rounded-3xl p-10 md:p-16 text-center relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-extrabold text-white mb-4">Subscribe to our Newsletter</h2>
                    <p class="text-primary-100 mb-8 max-w-2xl mx-auto">Get 20% off your first order and stay updated
                        with our latest collections and exclusive offers.</p>

                    <form class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto">
                        <input type="email" placeholder="Enter your email address"
                            class="flex-grow px-6 py-3 rounded-full border-none focus:ring-2 focus:ring-primary-300 text-gray-900">
                        <button type="submit"
                            class="bg-gray-900 text-white px-8 py-3 rounded-full font-bold hover:bg-gray-800 transition-colors">
                            Subscribe
                        </button>
                    </form>
                </div>

                <!-- Decorative Circles -->
                <div
                    class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-primary-500 rounded-full opacity-50 blur-2xl">
                </div>
                <div
                    class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 w-80 h-80 bg-primary-700 rounded-full opacity-50 blur-2xl">
                </div>
            </div>
        </div>
    </div>
</x-app-layout>