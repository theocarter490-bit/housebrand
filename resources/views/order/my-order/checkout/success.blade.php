<!DOCTYPE html>
<html>
<head>
    <title>Checkout Success</title>
</head>
<body>
    <h1>Checkout Success</h1>
    <p>Thank you for your purchase!</p>
    <p>Order Details:</p>
    <ul>
        <li>Payment Intent ID: {{ $payment_intent_id }}</li>
        <li>Amount Total: {{ $amount_total / 100 }} {{ strtoupper($currency) }}</li>

        <li>Customer Name: {{ $customer_details['name'] }}</li>
        <li>Customer Email: {{ $customer_details['email'] }}</li>
        <li>Payment Status: {{ $payment_status }}</li>
        <li>Created: {{ $created }}</li>
    </ul>
</body>
</html>
