@extends('baseF')

@section('content')
<div class="container py-5">
    <h2>Checkout</h2>
    
    <p>Total à payer : <strong>{{ $montantDT }} DT</strong></p>
    <p>(≈ <span id="total_amount">{{ $montantUSD }}</span> USD)</p>

    <div id="paypal-button-container"></div>
</div>

<script src="https://www.paypal.com/sdk/js?client-id=AS99U1ODPuHJqFH2bosbR598YLVjGsj-fx_wzZnpd0xTGB84iTbkx4isISo8hxWr-QpPIuTanDyhONTf&currency=USD"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
paypal.Buttons({
    createOrder: function(data, actions) {
        const amount = "{{ $montantUSD }}";
        return actions.order.create({
            purchase_units: [{
                amount: { value: amount, currency_code: 'USD' } // conversion TND -> USD
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            const transaction = details.purchase_units[0].payments.captures[0];
            $.ajax({
                url: "{{ route('paypal.process') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    transaction_id: transaction.id,
                    status: transaction.status,
                    amount: transaction.amount.value,
                    currency: transaction.amount.currency_code
                },
                success: function(response) {
                    if(response == 1){
                        alert("Payment Successful. Transaction ID: " + transaction.id);
                        location.reload();
                    } else {
                        alert("Failed to record payment");
                    }
                }
            })
        });
    }
}).render('#paypal-button-container');

</script>
@endsection
