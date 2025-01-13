
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Notification</title>
</head>
<body>
    <h1>Order Notification</h1>
    
    <p>Dear {{$order->user->name}},</p>

    <p>Your order with Number {{$order['order_id']}} has been created successfully.</p>

   

    <table>
        <thead>
            <tr>
                <th>Menu</th>
                <th>Price</th>
                <th>Count</th>
            

            </tr>
        </thead>
        <tbody>
            @foreach ($order['orderMenus'] as $item)
                <tr>
                    <td>{{ $item->menu->product->name }}</td>
                    <td>{{ $item->menu->price }}</td>
                    <td>{{ $item['count'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p>Total price: {{ $order['total_price'] }}</p>

    <p>Thank you for your order!</p>
</body>
</html>