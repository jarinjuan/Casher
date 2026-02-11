<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Casher Export Report</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background: #fbbf24; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #000; }
        .header p { margin: 5px 0 0 0; color: #666; }
        .section { margin-bottom: 30px; page-break-inside: avoid; }
        .section h2 { border-bottom: 2px solid #fbbf24; padding-bottom: 10px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
        tr:nth-child(even) { background: #f9f9f9; }
        .empty { color: #999; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Casher Export Report</h1>
        <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    @if(isset($data['transactions']) && !empty($data['transactions']))
        <div class="section">
            <h2>Transactions ({{ count($data['transactions']) }})</h2>
            @if(count($data['transactions']) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Currency</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['transactions'] as $t)
                            <tr>
                                <td>{{ $t['title'] }}</td>
                                <td>{{ number_format($t['amount'], 2) }}</td>
                                <td>{{ ucfirst($t['type']) }}</td>
                                <td>{{ $t['category_name'] ?? '—' }}</td>
                                <td>{{ $t['currency'] }}</td>
                                <td>{{ $t['created_at'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="empty">No transactions to display.</p>
            @endif
        </div>
    @endif

    @if(isset($data['categories']) && !empty($data['categories']))
        <div class="section">
            <h2>Categories ({{ count($data['categories']) }})</h2>
            @if(count($data['categories']) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Monthly Budget</th>
                            <th>Currency</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['categories'] as $c)
                            <tr>
                                <td>{{ $c['name'] }}</td>
                                <td>{{ number_format($c['monthly_budget'], 2) }}</td>
                                <td>{{ $c['budget_currency'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="empty">No categories to display.</p>
            @endif
        </div>
    @endif

    @if(isset($data['investments']) && !empty($data['investments']))
        <div class="section">
            <h2>Investments ({{ count($data['investments']) }})</h2>
            @if(count($data['investments']) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Symbol</th>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Avg Price</th>
                            <th>Currency</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['investments'] as $i)
                            <tr>
                                <td>{{ ucfirst($i['type']) }}</td>
                                <td>{{ $i['symbol'] }}</td>
                                <td>{{ $i['name'] ?? '—' }}</td>
                                <td>{{ number_format($i['quantity'], 8) }}</td>
                                <td>{{ number_format($i['average_price'], 2) }}</td>
                                <td>{{ $i['currency'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="empty">No investments to display.</p>
            @endif
        </div>
    @endif
</body>
</html>
