<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Invoice</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif;">

<!-- Header -->
<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#eceff4; padding:20px 0; margin-bottom:20px;">
    <tr>
        <td align="center">
            <table width="85%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td align="left">
                        <img src="{{ getFilePath(administratorSetting()->logo) }}" alt="logo" style="max-width:120px; height:auto;">
                    </td>
                    <td align="right" style="font-size:24px; font-weight:bold;">INVOICE</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Info Section -->
<table width="85%" align="center" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:20px;">
    <tr>
        <td valign="top" style="font-size:14px;">
            <p><strong>Shop:</strong> {{ administratorSetting()->shop_name }}</p>
            <p><strong>Phone:</strong> {{ administratorSetting()->phone }}</p>
            <p><strong>Email:</strong> {{ administratorSetting()->email }}</p>
            <p><strong>Address:</strong> {{ administratorSetting()->location }}</p>
        </td>
        <td valign="top" align="right" style="font-size:14px;">
            <p><strong>Invoice ID:</strong> #{{ @$invoice->id }}</p>
            <p><strong>Invoice To:</strong></p>
            <p>{{ $invoice->customer_name }}</p>
            <p>{{ $invoice->customer_email }}</p>
        </td>
    </tr>
</table>

<!-- Item Table -->
<table width="85%" align="center" cellspacing="0" cellpadding="0" border="1" style="border-collapse:collapse; border-color:#4e4e4e;">
    <thead>
    <tr style="background:#f2f2f2;">
        <th align="left" style="padding:10px;">Item</th>
        <th align="left" style="padding:10px;">Created Date</th>
        <th align="left" style="padding:10px;">Expire Date</th>
        <th align="left" style="padding:10px;">Price</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($invoice->lines->data as $item)
        <tr>
            <td style="padding:10px;">{{ optional($item)->description }}</td>
            <td style="padding:10px;">{{ gmdate('Y-m-d', optional($item)->period->start) }}</td>
            <td style="padding:10px;">{{ gmdate('Y-m-d', optional($item)->period->end) }}</td>
            <td style="padding:10px;">{{ getPriceFormat(optional($item)->price->unit_amount / 100) }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="2" style="padding:10px;">Thanks for your business</td>
        <td align="right" style="padding:10px;"><strong>Total:</strong></td>
        <td style="padding:10px;">{{ getPriceFormat($invoice->amount_paid / 100) }}</td>
    </tr>
    </tbody>
</table>

<!-- Note -->
<table width="85%" align="center" cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
    <tr>
        <td>
            <p><strong>Note:</strong> Thank you for choosing us! We appreciate your business and can’t wait to serve you again.</p>
        </td>
    </tr>
</table>

</body>
</html>
