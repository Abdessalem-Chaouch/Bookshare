<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Subscription Payment - LibroLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <style>
        body { 
            background: #ffffff;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        .payment-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .payment-card { 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
            padding: 3rem; 
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.2);
            transition: transform 0.3s ease;
        }
        .col-md-4 .payment-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .col-md-4 .payment-card h5,
        .col-md-4 .payment-card h6,
        .col-md-4 .payment-card span,
        .col-md-4 .payment-card strong {
            color: white;
        }
        .col-md-4 .payment-card hr {
            border-color: rgba(255,255,255,0.3);
        }
        .payment-card:hover { transform: translateY(-5px); }
        .stripe-card { 
            padding: 1.5rem; 
            border: 2px solid #e2e8f0; 
            border-radius: 15px; 
            background: #ffffff;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .stripe-card:focus-within { 
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .btn-payment { 
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white; 
            padding: 16px 32px; 
            border: none; 
            border-radius: 12px; 
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        .btn-payment:hover { 
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        }
        .btn-payment:disabled { 
            background: #9ca3af; 
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        h2 { 
            color: #1f2937;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
        }
        h5 { 
            color: #1f2937;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .btn-outline-secondary {
            border: 2px solid #e2e8f0;
            color: #6b7280;
            background: white;
            border-radius: 12px;
            padding: 16px 24px;
            transition: all 0.3s ease;
        }
        .btn-outline-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }
        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            border-radius: 12px;
            padding: 1rem 1.5rem;
        }
    </style>
</head>
<body>
<div class="payment-container">
    <h2 class="text-center mb-4">Subscription Payment</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <!-- Order Summary -->
        <div class="col-md-4">
            <div class="payment-card">
                <h5><i class="bx bx-receipt me-2"></i>Order Summary</h5>
                <hr>
                <h6>{{ $subscription->name }}</h6>
                <p class="text-muted">{{ $subscription->description }}</p>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Duration:</span>
                    <span>{{ $subscription->duration_days }} days</span>
                </div>
                <div class="d-flex justify-content-between">
                    <strong>Total:</strong>
                    <strong>${{ number_format($subscription->price, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="col-md-8">
            <div class="payment-card">
                <h5><i class="bx bx-credit-card me-2"></i>Payment Information</h5>
                <hr>
                
                <form id="payment-form">
                    @csrf
                    <div id="card-element" class="stripe-card mb-3">
                        <!-- Stripe Elements will create form elements here -->
                    </div>
                    <div id="card-errors" role="alert" class="text-danger mb-3"></div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('author.subscriptions') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back
                        </a>
                        <button type="submit" class="btn-payment" id="submit-button">
                            <i class="bx bx-credit-card me-1"></i>Pay ${{ number_format($subscription->price, 2) }}
                        </button>
                    </div>
                </form>
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
        submitButton.innerHTML = '<i class="bx bx-credit-card me-1"></i>Pay ${{ number_format($subscription->price, 2) }}';
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
                submitButton.innerHTML = '<i class="bx bx-credit-card me-1"></i>Pay ${{ number_format($subscription->price, 2) }}';
                submitButton.disabled = false;
            }
        }).catch(error => {
            console.error('Error:', error);
            document.getElementById('card-errors').textContent = 'Network error. Please try again.';
            submitButton.innerHTML = '<i class="bx bx-credit-card me-1"></i>Pay ${{ number_format($subscription->price, 2) }}';
            submitButton.disabled = false;
        });
    }
});
</script>

<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
</body>
</html>