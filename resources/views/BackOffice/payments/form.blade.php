<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="text-center mb-5">
        <h3 class="fw-bold text-primary mb-2">💳 Subscription Payment</h3>
        <p class="text-muted">Complete your subscription to unlock all features</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row justify-content-center">
        <!-- Order Summary -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body p-4">
                    <h5 class="text-white mb-3"><i class="bx bx-receipt me-2"></i>Order Summary</h5>
                    <div class="mb-3">
                        <h4 class="text-white mb-1">{{ $subscription->name }}</h4>
                        <p class="text-white-50 mb-0">{{ $subscription->description }}</p>
                    </div>
                    <hr class="border-white-50">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white-75">Duration:</span>
                        <span class="text-white fw-semibold">{{ $subscription->duration_days }} days</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong class="text-white">Total:</strong>
                        <strong class="text-white fs-5">
                            @php
                                $currencyService = app(\App\Services\CurrencyService::class);
                                echo $currencyService->format($subscription->display_price ?? $subscription->price, session()->get('currency') ?? $subscription->display_currency ?? 'USD');
                            @endphp
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="bx bx-credit-card me-2 text-primary"></i>Payment Information</h5>
                    
                    <form id="payment-form">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Card Details</label>
                            <div id="card-element" class="form-control p-3" style="border: 2px solid #e2e8f0; border-radius: 12px; min-height: 50px;">
                                <!-- Stripe Elements will create form elements here -->
                            </div>
                        </div>
                        <div id="card-errors" role="alert" class="text-danger mb-3"></div>

                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <a href="{{ route('author.subscriptions') }}" class="btn btn-outline-secondary px-4 py-2">
                                <i class="bx bx-arrow-back me-1"></i>Back
                            </a>
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" id="submit-button" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                                <i class="bx bx-credit-card me-1"></i>Pay 
                                @php
                                    $currencyService = app(\App\Services\CurrencyService::class);
                                    echo $currencyService->format($subscription->display_price ?? $subscription->price, $subscription->display_currency ?? 'USD');
                                @endphp
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ env("STRIPE_PUBLISHABLE_KEY") }}');
const elements = stripe.elements();

const cardElement = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: '#424770',
            fontFamily: 'Public Sans, sans-serif',
            '::placeholder': {
                color: '#aab7c4',
            },
        },
        invalid: {
            color: '#9e2146',
        },
    },
});

cardElement.mount('#card-element');

cardElement.on('change', ({error}) => {
    const displayError = document.getElementById('card-errors');
    if (error) {
        displayError.textContent = error.message;
    } else {
        displayError.textContent = '';
    }
});

const form = document.getElementById('payment-form');
const submitButton = document.getElementById('submit-button');

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    submitButton.disabled = true;

    const {paymentMethod, error} = await stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
    });

    if (error) {
        document.getElementById('card-errors').textContent = error.message;
        submitButton.innerHTML = '<i class="bx bx-credit-card me-1"></i>Pay {{ $currencyService->format($subscription->display_price ?? $subscription->price, $subscription->display_currency ?? "USD") }}';
        submitButton.disabled = false;
    } else {
        fetch('{{ route("payment.process", $subscription) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                payment_method_id: paymentMethod.id
            })
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                document.getElementById('card-errors').textContent = data.error || 'Payment failed. Please try again.';
                submitButton.innerHTML = '<i class="bx bx-credit-card me-1"></i>Pay {{ $currencyService->format($subscription->display_price ?? $subscription->price, $subscription->display_currency ?? "USD") }}';
                submitButton.disabled = false;
            }
        }).catch(error => {
            console.error('Error:', error);
            document.getElementById('card-errors').textContent = 'Network error. Please try again.';
            submitButton.innerHTML = '<i class="bx bx-credit-card me-1"></i>Pay {{ $currencyService->format($subscription->display_price ?? $subscription->price, $subscription->display_currency ?? "USD") }}';
            submitButton.disabled = false;
        });
    }
});
</script>

<style>
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}
#card-element:focus-within {
    border-color: #667eea !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}
.text-white-75 {
    color: rgba(255, 255, 255, 0.75) !important;
}
.text-white-50 {
    color: rgba(255, 255, 255, 0.5) !important;
}
.border-white-50 {
    border-color: rgba(255, 255, 255, 0.5) !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>