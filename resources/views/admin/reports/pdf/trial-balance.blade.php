<!DOCTYPE html>
<html>

<head>
    <title>Trial Balance</title>
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
            border: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        .amount {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background: #f9fafb;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Trial Balance Report</h1>
        <div>As of {{ date('M d, Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Account Name</th>
                <th>Code</th>
                <th class="amount">Debit</th>
                <th class="amount">Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $acc)
                @php $d = $acc->ledgers()->sum('debit');
                $c = $acc->ledgers()->sum('credit'); @endphp
                @if($d > 0 || $c > 0)
                    <tr>
                        <td>{{ $acc->name }}</td>
                        <td>{{ $acc->code }}</td>
                        <td class="amount">{{ $d > 0 ? number_format($d, 2) : '-' }}</td>
                        <td class="amount">{{ $c > 0 ? number_format($c, 2) : '-' }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2">GRAND TOTALS</td>
                <td class="amount">{{ number_format($totalDebits, 2) }}</td>
                <td class="amount">{{ number_format($totalCredits, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>