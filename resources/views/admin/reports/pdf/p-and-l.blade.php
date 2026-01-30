<!DOCTYPE html>
<html>

<head>
    <title>Profit & Loss Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            color: #1e40af;
        }

        .period {
            color: #666;
            margin-top: 5px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .summary-table th,
        .summary-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .summary-table th {
            text-align: left;
            background: #f9fafb;
            color: #4b5563;
            text-transform: uppercase;
            font-size: 10px;
        }

        .summary-table .amount {
            text-align: right;
            font-weight: bold;
        }

        .net-profit {
            background: #eff6ff;
        }

        .net-profit td {
            font-size: 16px;
            color: #1e40af;
            border-top: 2px solid #1e40af;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Profit & Loss Statement</h1>
        <div class="period">{{ \Carbon\Carbon::parse($start)->format('M d, Y') }} -
            {{ \Carbon\Carbon::parse($end)->format('M d, Y') }}</div>
    </div>

    <table class="summary-table">
        <thead>
            <tr>
                <th>Category</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Sales Revenue</td>
                <td class="amount text-green-600">+ {{ number_format($totalSales, 2) }}</td>
            </tr>
            <tr>
                <td>Total Refunds/Returns</td>
                <td class="amount text-red-600">- {{ number_format($totalRefunds, 2) }}</td>
            </tr>
            <tr>
                <td>Total Operating Expenses</td>
                <td class="amount text-orange-600">- {{ number_format($totalExpenses, 2) }}</td>
            </tr>
            <tr class="net-profit">
                <td><strong>Net Profit</strong></td>
                <td class="amount"><strong>{{ number_format($netProfit, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ date('Y-m-d H:i:s') }} | Supershop Management System
    </div>
</body>

</html>