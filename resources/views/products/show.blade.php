<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Product Image -->
                        <div class="w-full md:w-1/2">
                            <div class="aspect-square bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-500">No Image Available
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="w-full md:w-1/2 space-y-6">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $product->name }}
                                </h1>
                                <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($product->brand)
                                        <span
                                            class="bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">{{ $product->brand->name }}</span>
                                    @endif
                                    @if($product->category)
                                        <span>Category: {{ $product->category->name }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-end gap-4">
                                @if($product->sale_price)
                                    <div>
                                        <p class="text-sm text-gray-500 line-through">Reg:
                                            {{ Number::currency($product->regular_price) }}
                                        </p>
                                        <p class="text-4xl font-bold text-primary-600">
                                            {{ Number::currency($product->sale_price) }}
                                        </p>
                                    </div>
                                @else
                                    <p class="text-4xl font-bold text-gray-900 dark:text-white">
                                        {{ Number::currency($product->regular_price) }}
                                    </p>
                                @endif
                            </div>

                            <div>
                                <h3 class="font-bold mb-2">Description</h3>
                                <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300">
                                    {{ $product->description }}
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex gap-4">
                                    @csrf
                                    <div class="w-24">
                                        <label for="quantity" class="sr-only">Quantity</label>
                                        <input type="number" name="quantity" id="quantity" value="1" min="1"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-center">
                                    </div>
                                    <button type="submit"
                                        class="flex-1 bg-primary-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-primary-500 transition shadow-lg flex justify-center items-center gap-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        Add to Cart
                                    </button>
                                </form>
                            </div>

                            <div class="text-sm text-gray-500">
                                SKU: {{ $product->sku ?? 'N/A' }} | Stock:
                                {{ $product->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                            </div>
                        </div>
                    </div>

                    <!-- Reviews Section -->
                    <div class="mt-16 border-t border-gray-200 dark:border-gray-700 pt-10">
                        <h2 class="text-2xl font-bold mb-6">Customer Reviews</h2>

                        <!-- Review List -->
                        <div class="space-y-6 mb-10">
                            @forelse($product->reviews as $review)
                                <div class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $review->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'fill-current' : 'text-gray-300 dark:text-gray-600' }}"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300">{{ $review->comment }}</p>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 italic">No reviews yet. Be the first to review
                                    this product!</p>
                            @endforelse
                        </div>

                        <!-- Add Review Form -->
                        @auth
                            <div
                                class="bg-gray-50 dark:bg-gray-700/30 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-bold mb-4">Write a Review</h3>
                                <form action="{{ route('products.reviews.store', $product) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating</label>
                                        <div class="flex gap-4">
                                            @for($i = 5; $i >= 1; $i--)
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="rating" value="{{ $i }}" class="sr-only peer"
                                                        required>
                                                    <span
                                                        class="text-gray-300 dark:text-gray-600 peer-checked:text-yellow-400 hover:text-yellow-400 text-2xl transition-colors">★</span>
                                                    <span class="text-xs block text-center mt-1">{{ $i }}</span>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="comment"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your
                                            Review</label>
                                        <textarea name="comment" id="comment" rows="4"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Share your thoughts..."></textarea>
                                    </div>
                                    <button type="submit"
                                        class="bg-gray-900 dark:bg-primary-600 text-white font-bold py-2 px-6 rounded-full hover:bg-gray-800 dark:hover:bg-primary-500 transition">Submit
                                        Review</button>
                                </form>
                            </div>
                        @else
                            <div
                                class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg text-center border border-gray-200 dark:border-gray-700">
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Please <a href="{{ route('login') }}"
                                        class="text-primary-600 font-bold hover:underline">log in</a> to write a review.</p>
                            </div>
                        @endauth
                    </div>

                    <!-- Product Recommendations -->
                    @if($recommendations->count() > 0)
                        <div class="mt-16 border-t border-gray-200 dark:border-gray-700 pt-10">
                            <h2 class="text-2xl font-bold mb-6">You May Also Like</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                                @foreach($recommendations as $recommended)
                                    <x-product-card :product="$recommended" />
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Analytics: Track Product View --}}
    <x-analytics-events :trackViewContent="true" :product="$product" />
</x-app-layout>