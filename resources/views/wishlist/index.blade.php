<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Wishlist') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if($wishlists->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($wishlists as $wishlist)
                                <div class="relative bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <form action="{{ route('wishlist.destroy', $wishlist) }}" method="POST" class="absolute top-2 right-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
                                        </button>
                                    </form>
                                    
                                    @if($wishlist->product)
                                        <a href="{{ route('products.show', $wishlist->product->slug) }}">
                                            <div class="aspect-square bg-gray-200 dark:bg-gray-600 rounded mb-3 overflow-hidden">
                                                @if($wishlist->product->image_url)
                                                    <img src="{{ $wishlist->product->image_url }}" alt="{{ $wishlist->product->name }}" class="w-full h-full object-cover">
                                                @endif
                                            </div>
                                            <h3 class="font-bold text-sm mb-1">{{ $wishlist->product->name }}</h3>
                                            <p class="text-primary-600 font-bold">{{ Number::currency($wishlist->product->sale_price ?? $wishlist->product->regular_price) }}</p>
                                        </a>
                                        
                                        <form action="{{ route('cart.add', $wishlist->product->id) }}" method="POST" class="mt-3">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="w-full bg-primary-600 text-white text-sm py-2 rounded hover:bg-primary-500 transition">
                                                Add to Cart
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Your wishlist is empty</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6">Save your favorite products here!</p>
                            <a href="{{ route('shop.index') }}" class="text-primary-600 font-bold hover:underline">Continue Shopping</a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
