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

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: bold;
            margin: 10px 0;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-processing {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .order-details {
            background: white;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
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
            <h1>Order Status Update</h1>
        </div>

        <div class="content">
            <p>Hello {{ $order->first_name }},</p>
            <p>The status of your order has been updated.</p>

            <div class="order-details">
                <h2>Order #{{ $order->order_number }}</h2>
                <p><strong>Previous Status:</strong> <span
                        class="status-badge status-{{ $oldStatus }}">{{ ucfirst($oldStatus) }}</span></p>
                <p><strong>New Status:</strong> <span
                        class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></p>

                @if($order->status === 'processing')
                    <p>Great news! Your order is now being processed and will be shipped soon.</p>
                @elseif($order->status === 'completed')
                    <p>Your order has been completed and delivered. We hope you enjoy your purchase!</p>
                @elseif($order->status === 'cancelled')
                    <p>Your order has been cancelled. If you have any questions, please contact our support team.</p>
                @endif

                <p><strong>Order Total:</strong> {{ Number::currency($order->grand_total) }}</p>
            </div>

            <p>If you have any questions about your order, please don't hesitate to contact us.</p>
        </div>

        <div class="footer">
            <p>Thank you for shopping with us!</p>
            <p>&copy; {{ date('Y') }} Supershop. All rights reserved.</p>
        </div>
    </div>
</body>

</html>