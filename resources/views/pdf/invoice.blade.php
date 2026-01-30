<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            color: #333;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            line-height: 24px;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
            font-size: 18px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <span style="color: #6366f1;">SUPERSHOP</span>
                            </td>
                            <td>
                                Invoice #: {{ $order->order_number }}<br>
                                Created: {{ $order->created_at->format('M d, Y') }}<br>
                                Status: <span
                                    class="badge {{ $order->is_paid ? 'badge-success' : '' }}">{{ $order->is_paid ? 'PAID' : 'UNPAID' }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>Billed To:</strong><br>
                                {{ $order->first_name }} {{ $order->last_name }}<br>
                                {{ $order->email }}<br>
                                {{ $order->address }}, {{ $order->city }}<br>
                                {{ $order->state }} {{ $order->zip_code }}
                            </td>
                            <td>
                                <strong>From:</strong><br>
                                Supershop HQ<br>
                                123 Ecommerce Street<br>
                                Tech City, TC 99999<br>
                                support@supershop.com
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Item</td>
                <td style="text-align: center;">Qty</td>
                <td style="text-align: right;">Price</td>
                <td style="text-align: right;">VAT</td>
                <td style="text-align: right;">Total</td>
            </tr>
            @foreach($order->items as $item)
                <tr class="item {{ $loop->last ? 'last' : '' }}">
                    <td>{{ $item->product->name }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->vat_amount, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td colspan="4"></td>
                <td style="text-align: right;">
                    <div style="margin-top: 10px;">
                        <span style="color: #666; font-size: 12px;">Net Subtotal:</span>
                        {{ number_format($order->grand_total - $order->items->sum('vat_amount'), 2) }}<br>
                        <span style="color: #666; font-size: 12px;">Total VAT:</span>
                        {{ number_format($order->items->sum('vat_amount'), 2) }}<br>
                        <strong style="font-size: 18px;">Total: {{ number_format($order->grand_total, 2) }}</strong>
                    </div>
                </td>
            </tr>
        </table>
        <div style="margin-top: 50px; text-align: center; color: #999; font-size: 12px;">
            Thank you for your business! This is a computer-generated invoice.
        </div>
    </div>
</body>

</html>