<!DOCTYPE html>
<html>
<head>
    <title>Invoice for Order {{ $order['code'] }}</title>
</head>
<body>
<p>Dear {{ $order['user']->name }},</p>
<p>{{$messageContent}}</p>
<p>Order Code: {{ $order['code'] }}</p>
<p>Total: {{ getPriceFormat($order->grand_total_amount) }}</p>
<p>Thank you for shopping with us!</p>

<p>Please find your invoice attached to this email.</p>

</body>
</html>
