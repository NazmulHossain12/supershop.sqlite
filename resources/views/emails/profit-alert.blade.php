<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .alert-header {
            background: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            margin: 25px 0;
        }

        .stat-card {
            background: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            width: 45%;
        }

        .stat-label {
            font-size: 0.8em;
            color: #666;
            uppercase;
        }

        .stat-value {
            font-size: 1.4em;
            font-weight: bold;
            margin-top: 5px;
        }

        .drop-highlight {
            color: #dc2626;
            font-weight: bold;
            font-size: 1.2em;
            text-align: center;
            margin: 20px 0;
        }

        .product-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .product-list th,
        .product-list td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .product-list th {
            background: #f3f4f6;
        }

        .footer {
            font-size: 0.8em;
            color: #999;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="alert-header">
            <h2 style="margin:0;">Profit Alert</h2>
        </div>

        <p>Hello Admin,</p>
        <p>A significant drop in net profit has been detected for the previous week compared to the week before.</p>

        <div class="drop-highlight">
            Net Profit dropped by {{ number_format($dropPercent, 1) }}%
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-label">Last Week Profit</div>
                <div class="stat-value">{{ number_format($lastWeekProfit, 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Week Before Profit</div>
                <div class="stat-value">{{ number_format($prevWeekProfit, 2) }}</div>
            </div>
        </div>

        <h3>Top 5 Products with Sales Drop</h3>
        <p>These products saw the most significant decrease in sales volume compared to the previous week:</p>

        <table class="product-list">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Last Week Qty</th>
                    <th>Prev Week Qty</th>
                    <th>Change</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topDrops as $drop)
                    <tr>
                        <td>{{ $drop['name'] }}</td>
                        <td>{{ $drop['last_qty'] }}</td>
                        <td>{{ $drop['prev_qty'] }}</td>
                        <td style="color:#dc2626;">-{{ $drop['drop_qty'] }} units</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            This is an automated alert from the Supershop Management System.
        </div>
    </div>
</body>

</html>