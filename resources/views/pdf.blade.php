<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sales Report</title>
    <style>
        h4 {
            margin: 0;
        }
        .w-full {
            width: 100%;
        }
        .w-half {
            width: 50%;
        }
        .margin-top {
            margin-top: 1.25rem;
        }
        .footer {
            font-size: 0.875rem;
            padding: 1rem;
            background-color: rgb(241 245 249);
        }
        table {
            width: 100%;
            border-spacing: 0;
        }
        table.products {
            font-size: 0.875rem;
        }
        table.products tr {
            background-color: rgb(96 165 250);
        }
        table.products th {
            color: #ffffff;
            padding: 0.5rem;
        }
        table tr.items {
            background-color: rgb(241 245 249);
        }
        table tr.items td {
            padding: 0.5rem;
        }
        .total {
            text-align: right;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <table class="w-full">
        <tr>
            <td class="w-half">
                <!-- Logo -->
                <h2>Sales Report</h2>
            </td>
            <td class="w-half">
                <h4>Date: {{ \Carbon\Carbon::now()->toDateString() }}</h4>
            </td>
        </tr>
    </table>
 
    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <div><h4>From:</h4><div>{{ $from }}</div></div>
                </td>
                <td class="w-half">
                    <div><h4>To:</h4><div>{{ $to }}</div></div>
                </td>
            </tr>
        </table>
    </div>
 
    <div class="margin-top">
        <h4>Customer Sales</h4>
        <table class="products">
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Amount</th>
            </tr>
            @foreach($guest as $key => $data)
                @if(is_array($data))
                    <tr class="items">
                        <td>{{ $data['date'] }}</td>
                        <td>{{ $data['name'] }}</td>
                        <td>{{ $data['amount'] }}</td>
                    </tr>
                @endif
            @endforeach
        </table>
    </div>

    <div class="total">
        Total: Php {{ $guest['total'] }}.00
    </div>

    <div class="margin-top">
        <h4>Walk-ins Sales</h4>
        <table class="products">
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Membership</th>
                <th>Fee</th>
            </tr>
            @foreach($member as $key => $data)
                @if(is_array($data))
                    <tr class="items">
                        <td>{{ $data['date'] }}</td>
                        <td>{{ $data['name'] }}</td>
                        <td>{{ $data['membership'] }}</td>
                        <td>{{ $data['amount'] }}</td>
                    </tr>
                @endif
            @endforeach
        </table>
    </div>
 
    <div class="total">
        Total: Php {{ $member['total'] }}.00
    </div>
 
    <div class="footer margin-top">
        <div>&copy; HeatDrops Fitness Center</div>
    </div>
</body>
</html>