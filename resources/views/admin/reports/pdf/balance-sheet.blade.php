<!DOCTYPE html>
<html>

<head>
    <title>Balance Sheet</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .section-title {
            background: #f3f4f6;
            padding: 5px;
            font-weight: bold;
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }

        .amount {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background: #f9fafb;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Balance Sheet</h1>
        <div>As of {{ date('M d, Y') }}</div>
    </div>

    <div class="section-title">ASSETS</div>
    <table>
        @foreach($assets as $acc)
            <tr>
                <td>{{ $acc->name }}</td>
                <td class="amount">{{ number_format($acc->balance, 2) }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td>Total Assets</td>
            <td class="amount">{{ number_format($totalAssets, 2) }}</td>
        </tr>
    </table>

    <div class="section-title">LIABILITIES</div>
    <table>
        @foreach($liabilities as $acc)
            <tr>
                <td>{{ $acc->name }}</td>
                <td class="amount">{{ number_format($acc->balance, 2) }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td>Total Liabilities</td>
            <td class="amount">{{ number_format($totalLiabilities, 2) }}</td>
        </tr>
    </table>

    <div class="section-title">EQUITY</div>
    <table>
        @foreach($equity as $acc)
            <tr>
                <td>{{ $acc->name }}</td>
                <td class="amount">{{ number_format($acc->balance, 2) }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td>Total Equity</td>
            <td class="amount">{{ number_format($totalEquity, 2) }}</td>
        </tr>
    </table>

    <div
        style="margin-top: 30px; text-align: center; font-weight: bold; border-top: 2px solid #333; padding-top: 10px;">
        Accounting Equation Check: {{ number_format($totalAssets, 2) }} =
        {{ number_format($totalLiabilities + $totalEquity, 2) }}
    </div>
</body>

</html>