<!DOCTYPE html>
<html>

<head>
    <title>Account Ledger</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
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
            padding: 5px;
            border: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        .amount {
            text-align: right;
        }

        .debit {
            color: #059669;
        }

        .credit {
            color: #dc2626;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Account Ledger Report</h1>
        <div>Account: {{ $account->name ?? 'All Accounts' }}</div>
        <div>Period: {{ $start }} to {{ $end }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Account</th>
                <th>Description</th>
                <th class="amount">Debit</th>
                <th class="amount">Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ledgers as $l)
                <tr>
                    <td>{{ $l->transaction->transaction_date->format('Y-m-d H:i') }}</td>
                    <td>{{ $l->account->name }}</td>
                    <td>{{ $l->entry_description ?? $l->transaction->description }}</td>
                    <td class="amount debit">{{ $l->debit > 0 ? number_format($l->debit, 2) : '-' }}</td>
                    <td class="amount credit">{{ $l->credit > 0 ? number_format($l->credit, 2) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>