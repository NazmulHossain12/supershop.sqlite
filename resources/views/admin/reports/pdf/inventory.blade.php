<!DOCTYPE html>
<html>

<head>
    <title>Inventory Valuation Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #4338ca;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td {
            padding: 6px;
            border: 1px solid #eee;
            text-align: left;
        }

        .details-table th {
            background: #f3f4f6;
            font-weight: bold;
        }

        .amount {
            text-align: right;
        }

        .total-row {
            background: #f9fafb;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Inventory Valuation Report</h1>
        <div>As of {{ date('M d, Y H:i') }}</div>
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Category</th>
                <th class="amount">Qty</th>
                <th class="amount">Avg Cost</th>
                <th class="amount">Stock Value</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotalValue = 0; @endphp
            @foreach($products as $p)
                @php $val = $p->stock_quantity * $p->cost_price;
                $grandTotalValue += $val; @endphp
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->category->name ?? '-' }}</td>
                    <td class="amount">{{ $p->stock_quantity }}</td>
                    <td class="amount">{{ number_format($p->cost_price, 2) }}</td>
                    <td class="amount">{{ number_format($val, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;">Total Inventory Value (At Cost):</td>
                <td class="amount">{{ number_format($grandTotalValue, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>