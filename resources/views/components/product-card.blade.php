@props(['product'])

<div
    class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden h-full flex flex-col animate-slide-up">
    <!-- Image Section -->
    <div class="relative aspect-square overflow-hidden bg-gray-50 dark:bg-gray-900">
        <!-- Badge Overlay -->
        <div class="absolute top-3 left-3 z-10 flex flex-col gap-2">
            @if($product->sale_price)
                <x-badge type="danger" label="SALE" />
            @endif
            @if($product->featured)
                <x-badge type="warning" label="HOT" />
            @endif
        </div>

        <!-- Product Image -->
        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
            class="object-cover w-full h-full transform group-hover:scale-110 transition-transform duration-500">

        <!-- Quick Action Overlay -->
        <div
            class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full bg-white text-gray-900 font-bold py-3 rounded-xl shadow-lg hover:bg-primary-50 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Add to Cart
                </button>
            </form>
        </div>
    </div>

    <!-- Content Section -->
    <div class="p-4 flex flex-col flex-grow">
        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider font-semibold">
            {{ $product->category->name ?? 'Category' }}
        </div>
        <h3
            class="text-lg font-bold text-gray-900 dark:text-white mb-2 leading-tight group-hover:text-primary-600 transition-colors">
            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
        </h3>

        <div class="mt-auto pt-2 flex items-center justify-between border-t border-gray-100 dark:border-gray-700">
            <div class="flex flex-col">
                @if($product->sale_price)
                    <span class="text-xs text-gray-400 line-through">{{ Number::currency($product->regular_price) }}</span>
                    <span
                        class="text-lg font-extrabold text-primary-600">{{ Number::currency($product->sale_price) }}</span>
                @else
                    <span
                        class="text-lg font-extrabold text-gray-900 dark:text-white">{{ Number::currency($product->regular_price) }}</span>
                @endif
            </div>

            <div class="text-yellow-400 flex text-sm">
                @for($i = 0; $i < 5; $i++)
                    <svg class="w-4 h-4 {{ $i < 4 ? 'fill-current' : 'text-gray-300 dark:text-gray-600' }}"
                        viewBox="0 0 20 20">
                        <path
                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                    </svg>
                @endfor
            </div>
        </div>
    </div>
</div>