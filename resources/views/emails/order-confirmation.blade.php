<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #1e40af;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
            background: #f9fafb;
        }

        .order-details {
            background: white;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .item {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 0;
        }

        .total {
            font-size: 1.25em;
            font-weight: bold;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #1e40af;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 0.875em;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
        </div>

        <div class="content">
            <p>Hello {{ $order->first_name }},</p>
            <p>Thank you for your order! We've received your order and will process it shortly.</p>

            <div class="order-details">
                <h2>Order #{{ $order->order_number }}</h2>
                <p><strong>Date:</strong> {{ $order->created_at->format('F d, Y h:i A') }}</p>
                <p><strong>Payment Method:</strong> {{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</p>
                <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>

                <h3>Items Ordered:</h3>
                @foreach($order->items as $item)
                    <div class="item">
                        <strong>{{ optional($item->product)->name ?? 'Product' }}</strong><br>
                        Quantity: {{ $item->quantity }} × {{ Number::currency($item->price) }} =
                        {{ Number::currency($item->quantity * $item->price) }}
                    </div>
                @endforeach

                <div class="total">
                    Total: {{ Number::currency($order->grand_total) }}
                </div>
            </div>

            <div class="order-details">
                <h3>Shipping Address</h3>
                <p>
                    {{ $order->first_name }} {{ $order->last_name }}<br>
                    {{ $order->address }}<br>
                    {{ $order->city }}, {{ $order->state }} {{ $order->zip_code }}<br>
                    {{ $order->phone }}<br>
                    {{ $order->email }}
                </p>
            </div>

            <p>We'll send you another email when your order ships.</p>
        </div>

        <div class="footer">
            <p>Thank you for shopping with us!</p>
            <p>&copy; {{ date('Y') }} Supershop. All rights reserved.</p>
        </div>
    </div>
</body>

</html>