<!DOCTYPE html>
<html>

<head>
    <title>VAT Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #dc2626;
        }

        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .card {
            display: table-cell;
            padding: 10px;
            border: 1px solid #eee;
            text-align: center;
        }

        .card .label {
            font-size: 9px;
            uppercase;
            color: #666;
        }

        .card .value {
            font-size: 14px;
            font-bold;
            margin-top: 5px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .details-table th {
            background: #fef2f2;
        }

        .amount {
            text-align: right !important;
        }

        .section-header {
            background: #f3f4f6;
            font-weight: bold;
            padding: 5px 10px;
            margin: 15px 0 5px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>VAT Filing Report</h1>
        <div>{{ $start }} to {{ $end }}</div>
    </div>

    <div class="summary-cards">
        <div class="card">
            <div class="label">Output VAT (Sales)</div>
            <div class="value text-red-600">{{ number_format($outputVat, 2) }}</div>
        </div>
        <div class="card">
            <div class="label">Input VAT (Purchases)</div>
            <div class="value text-green-600">{{ number_format($inputVat, 2) }}</div>
        </div>
        <div class="card" style="background: #fef2f2;">
            <div class="label">Net Payable/Credit</div>
            <div class="value">{{ number_format($netVat, 2) }}</div>
        </div>
    </div>

    <div class="section-header">Sales VAT Breakdown (Output)</div>
    <table class="details-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice #</th>
                <th class="amount">VAT Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesDetails as $inv)
                <tr>
                    <td>{{ $inv->issued_at->format('Y-m-d') }}</td>
                    <td>{{ $inv->invoice_number }}</td>
                    <td class="amount">{{ number_format($inv->total_vat_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-header">Purchase VAT Breakdown (Input)</div>
    <table class="details-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>PO Ref #</th>
                <th class="amount">VAT Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseDetails as $po)
                <tr>
                    <td>{{ $po->updated_at->format('Y-m-d') }}</td>
                    <td>{{ $po->reference_no }}</td>
                    <td class="amount">{{ number_format($po->total_vat_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>