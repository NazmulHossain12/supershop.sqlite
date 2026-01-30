<!DOCTYPE html>
<html>

<head>
    <title>Cashflow Statement</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .section-header {
            background: #ecfdf5;
            padding: 5px;
            font-weight: bold;
            margin-top: 20px;
            color: #065f46;
        }

        .section-header.out {
            background: #fff1f2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th,
        td {
            padding: 6px;
            border-bottom: 1px solid #eee;
        }

        .amount {
            text-align: right;
            font-mono;
        }

        .net-cash {
            margin-top: 30px;
            padding: 15px;
            background: #f0f9ff;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            border: 1px solid #bae6fd;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Cashflow Statement</h1>
        <div>Period: {{ $start }} to {{ $end }}</div>
    </div>

    <div class="section-header">CASH INFLOWS</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Account</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inflows as $in)
                <tr>
                    <td>{{ $in->transaction->transaction_date->format('Y-m-d') }}</td>
                    <td>{{ $in->account->name }}</td>
                    <td class="amount">+{{ number_format($in->debit, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"><strong>Total Inflow</strong></td>
                <td class="amount"><strong>{{ number_format($totalInflow, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="section-header out">CASH OUTFLOWS</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Account</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($outflows as $out)
                <tr>
                    <td>{{ $out->transaction->transaction_date->format('Y-m-d') }}</td>
                    <td>{{ $out->account->name }}</td>
                    <td class="amount">-{{ number_format($out->credit, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"><strong>Total Outflow</strong></td>
                <td class="amount"><strong>{{ number_format($totalOutflow, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="net-cash">
        NET CASHFLOW FOR THE PERIOD: {{ number_format($netCashflow, 2) }}
    </div>
</body>

</html>