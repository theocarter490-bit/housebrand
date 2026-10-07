<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribe</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>

<body>
    <h1>Subscribe to a Plan</h1>

    <form id="subscription-form" method="POST" action="{{ route('user.subscription') }}">
        @csrf
        <label for="plan">Choose a plan:</label>
        <select id="plan" name="plan">
            <option value="basic-plan-id">Basic Plan - $9.99/month</option>
            <option value="premium-plan-id">Premium Plan - $19.99/month</option>
        </select>

        <div id="card-element"></div>
        <button type="submit">Subscribe</button>
    </form>

    <script>

        var stripe = Stripe('{{ env('STRIPE_KEY') }}'); // Your Stripe public key
        var elements = stripe.elements();
        var card = elements.create('card');
        card.mount('#card-element');

        var form = document.getElementById('subscription-form');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            // Create a payment method token
            const {
                paymentMethod,
                error
            } = await stripe.createPaymentMethod({
                type: 'card',
                card: card,
            });

            if (error) {
                console.error('Error:', error);
            } else {
                // Add the PaymentMethod id to the form
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripePaymentMethodId');
                hiddenInput.setAttribute('value', paymentMethod.id);
                form.appendChild(hiddenInput);
                form.submit();
            }
        });
    </script>
</body>

</html>
