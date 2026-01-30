<!DOCTYPE html>
<html>

<head>
    <title>Sales Invoices Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #fff7ed;
            text-align: left;
        }

        .amount {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background: #fff7ed;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Sales Invoices Summary</h1>
        <div>Period: {{ $start }} to {{ $end }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Customer</th>
                <th>Date</th>
                <th class="amount">VAT</th>
                <th class="amount">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $inv)
                <tr>
                    <td>{{ $inv->invoice_number }}</td>
                    <td>{{ $inv->customer->name ?? 'Walk-in' }}</td>
                    <td>{{ $inv->issued_at->format('Y-m-d') }}</td>
                    <td class="amount">{{ number_format($inv->total_vat_amount, 2) }}</td>
                    <td class="amount">{{ number_format($inv->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3">REPORT TOTALS</td>
                <td class="amount">{{ number_format($invoices->sum('total_vat_amount'), 2) }}</td>
                <td class="amount">{{ number_format($invoices->sum('total_amount'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>