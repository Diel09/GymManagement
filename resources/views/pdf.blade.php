<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HeatDrops Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        h2, h4 {
            margin: 0;
        }
        .header {
            text-align: center;
            padding: 1.5rem 0;
            border-bottom: 2px solid #cccccc;
        }
        .logo {
            max-width: 100px;
        }
        .content {
            padding: 1rem 2rem;
        }
        .section-title {
            margin-top: 2rem;
            margin-bottom: 0.5rem;
            color: #444444;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 0.75rem;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 1rem;
            font-size: 1rem;
        }
        .footer {
            font-size: 0.875rem;
            text-align: center;
            padding: 1rem;
            background-color: #f4f4f4;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('img/logo.jpg') }}" alt="HeatDrops Logo" class="logo">
        <h2>HeatDrops Fitness Center</h2>
        <h4>Sales Report</h4>
    </div>

    <div class="content">
        <table>
            <tr>
                <td><strong>Date Generated:</strong> {{ \Carbon\Carbon::now()->format('F j, Y') }}</td>
            </tr>
        </table>

        <div class="section-title">Report Period</div>
        <table>
            <tr>
                <td><strong>From:</strong> {{ \Carbon\Carbon::parse($from)->format('F j, Y') }}</td>
                <td><strong>To:</strong> {{ \Carbon\Carbon::parse($to)->format('F j, Y') }}</td>
            </tr>
        </table>

        <div class="section-title">Walk-in Sales</div>
        <table>
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Amount</th>
            </tr>
            @foreach($guest as $data)
                @if(is_array($data))
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($data['date'])->format('F j, Y') }}</td>
                        <td>{{ $data['name'] }}</td>
                        <td>Php {{ number_format($data['amount'], 2) }}</td>
                    </tr>
                @endif
            @endforeach
        </table>
        <div class="total">Total Walk-in Sales: Php {{ number_format($guest['total'], 2) }}</div>

        <div class="section-title">Member Sales</div>
        <table>
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Membership</th>
                <th>Fee</th>
            </tr>
            @foreach($member as $data)
                @if(is_array($data))
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($data['date'])->format('F j, Y') }}</td>
                        <td>{{ $data['name'] }}</td>
                        <td>{{ $data['membership'] }}</td>
                        <td>Php {{ number_format($data['amount'], 2) }}</td>
                    </tr>
                @endif
            @endforeach
        </table>
        <div class="total">Total Member Sales: Php {{ number_format($member['total'], 2) }}</div>

        <div class="section-title">Expenses</div>
        <table>
            <tr>
                <th>Date</th>
                <th>Item</th>
                <th>Price</th>
            </tr>
            @foreach($expense as $data)
                @if(is_array($data))
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($data['date_spend'])->format('F j, Y') }}</td>
                        <td>{{ $data['title'] }}</td>
                        <td>Php {{ number_format($data['price'], 2) }}</td>
                    </tr>
                @endif
            @endforeach
        </table>
        <div class="total">Total Expenses: Php {{ number_format($expense['total'], 2) }}</div>

        <div class="section-title">Summary</div>
        <table>
            <tr>
                <th>Category</th>
                <th>Amount</th>
            </tr>
            <tr>
                <td>Total Walk-in Sales</td>
                <td>Php {{ number_format($guest['total'], 2) }}</td>
            </tr>
            <tr>
                <td>Total Member Sales</td>
                <td>Php {{ number_format($member['total'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Gross Total</strong></td>
                <td><strong>Php {{ number_format($guest['total'] + $member['total'], 2) }}</strong></td>
            </tr>
            <tr>
                <td>Total Expenses</td>
                <td>Php {{ number_format($expense['total'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Net Income</strong></td>
                <td><strong>Php {{ number_format(($guest['total'] + $member['total']) - $expense['total'], 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} HeatDrops Fitness Center. All rights reserved.
    </div>
</body>
</html>
