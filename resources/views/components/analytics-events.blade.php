{{-- E-Commerce Event Tracking --}}

{{-- Add to Cart Event --}}
@if(isset($trackAddToCart) && $trackAddToCart)
    <script>
        // Google Analytics - Add to Cart
        @if(config('services.google_analytics.measurement_id'))
            gtag('event', 'add_to_cart', {
                currency: 'USD',
                value: {{ $product->sale_price ?? $product->regular_price }},
                items: [{
                    item_id: '{{ $product->id }}',
                    item_name: '{{ $product->name }}',
                    item_category: '{{ $product->category->name ?? '' }}',
                    item_brand: '{{ $product->brand->name ?? '' }}',
                    price: {{ $product->sale_price ?? $product->regular_price }},
                    quantity: 1
                }]
            });
        @endif

        // Facebook Pixel - Add to Cart
        @if(config('services.facebook_pixel.pixel_id'))
            fbq('track', 'AddToCart', {
                content_ids: ['{{ $product->id }}'],
                content_name: '{{ $product->name }}',
                content_type: 'product',
                value: {{ $product->sale_price ?? $product->regular_price }},
                currency: 'USD'
            });
        @endif
    </script>
@endif

{{-- Purchase/Checkout Event --}}
@if(isset($trackPurchase) && $trackPurchase && isset($order))
    <script>
        // Google Analytics - Purchase
        @if(config('services.google_analytics.measurement_id'))
            gtag('event', 'purchase', {
                transaction_id: '{{ $order->order_number }}',
                value: {{ $order->grand_total }},
                currency: 'USD',
                items: [
                    @foreach($order->items as $item)
                        {
                            item_id: '{{ $item->product_id }}',
                            item_name: '{{ $item->product->name ?? '' }}',
                            price: {{ $item->price }},
                            quantity: {{ $item->quantity }}
                        }{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ]
            });
        @endif

        // Facebook Pixel - Purchase
        @if(config('services.facebook_pixel.pixel_id'))
            fbq('track', 'Purchase', {
                value: {{ $order->grand_total }},
                currency: 'USD',
                content_ids: [
                    @foreach($order->items as $item)
                        '{{ $item->product_id }}'{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                content_type: 'product',
                num_items: {{ $order->items->count() }}
            });
        @endif
    </script>
@endif

{{-- View Content (Product Detail) Event --}}
@if(isset($trackViewContent) && $trackViewContent && isset($product))
    <script>
        // Google Analytics - View Item
        @if(config('services.google_analytics.measurement_id'))
            gtag('event', 'view_item', {
                currency: 'USD',
                value: {{ $product->sale_price ?? $product->regular_price }},
                items: [{
                    item_id: '{{ $product->id }}',
                    item_name: '{{ $product->name }}',
                    item_category: '{{ $product->category->name ?? '' }}',
                    item_brand: '{{ $product->brand->name ?? '' }}',
                    price: {{ $product->sale_price ?? $product->regular_price }}
                }]
            });
        @endif

        // Facebook Pixel - View Content
        @if(config('services.facebook_pixel.pixel_id'))
            fbq('track', 'ViewContent', {
                content_ids: ['{{ $product->id }}'],
                content_name: '{{ $product->name }}',
                content_category: '{{ $product->category->name ?? '' }}',
                content_type: 'product',
                value: {{ $product->sale_price ?? $product->regular_price }},
                currency: 'USD'
            });
        @endif
    </script>
@endif

{{-- Initiate Checkout Event --}}
@if(isset($trackInitiateCheckout) && $trackInitiateCheckout)
    <script>
        // Google Analytics - Begin Checkout
        @if(config('services.google_analytics.measurement_id'))
            gtag('event', 'begin_checkout', {
                currency: 'USD',
                value: {{ app(\App\Services\CartService::class)->getTotal() }}
            });
        @endif

        // Facebook Pixel - Initiate Checkout
        @if(config('services.facebook_pixel.pixel_id'))
            fbq('track', 'InitiateCheckout', {
                value: {{ app(\App\Services\CartService::class)->getTotal() }},
                currency: 'USD',
                num_items: {{ app(\App\Services\CartService::class)->getItemCount() }}
            });
        @endif
    </script>
@endif