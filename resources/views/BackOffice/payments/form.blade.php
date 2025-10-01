@extends('baseF')
@section('content')
<link href="{{ asset('css/payment.css') }}" rel="stylesheet">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
    <h4 class="fw-bold py-3 mb-4">Subscription Payment</h4>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Résumé de l'abonnement -->
        <div class="col-md-4">
            <div class="card payment-summary">
                <div class="card-header">
                    <h5><i class="bx bx-receipt me-2"></i>Order Summary</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $subscription->name }}</h6>
                    <p class="text-muted">{{ $subscription->description }}</p>
                    <ul class="list-unstyled feature-list">
                        @foreach($subscription->features as $feature)
                        <li><i class="bx bx-check me-2"></i>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Duration:</span>
                        <span>{{ $subscription->duration_days }} days</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong>{{ number_format($subscription->price, 2) }} €</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire de paiement -->
        <div class="col-md-8">
            <div class="card payment-form">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5><i class="bx bx-credit-card me-2"></i>Payment Information</h5>
                        <div class="secure-badge">
                            <i class="bx bx-shield"></i>Secure Payment
                        </div>
                    </div>
                    <div class="payment-methods">
                        <span class="payment-method">💳 Visa</span>
                        <span class="payment-method">💳 Mastercard</span>
                        <span class="payment-method">💳 Amex</span>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('payment.process', $subscription) }}" method="POST" id="payment-form">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="cardholder_name" class="form-label">CARDHOLDER NAME</label>
                            <input type="text" class="form-control @error('cardholder_name') is-invalid @enderror" 
                                   id="cardholder_name" name="cardholder_name" value="{{ old('cardholder_name') }}" required>
                            @error('cardholder_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="card_number" class="form-label">CARD NUMBER</label>
                            <div class="card-input">
                                <input type="text" class="form-control @error('card_number') is-invalid @enderror" 
                                       id="card_number" name="card_number" placeholder="1234 5678 9012 3456" 
                                       maxlength="19" required>
                            </div>
                            @error('card_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expiry_date" class="form-label">EXPIRY DATE</label>
                                    <input type="text" class="form-control @error('expiry_date') is-invalid @enderror" 
                                           id="expiry_date" name="expiry_date" placeholder="MM/YY" 
                                           maxlength="5" required>
                                    @error('expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control @error('cvv') is-invalid @enderror" 
                                           id="cvv" name="cvv" placeholder="123" maxlength="3" required>
                                    @error('cvv')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>



                        <div class="d-flex justify-content-between">
                            <a href="{{ route('author.subscriptions') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Back
                            </a>
                            <button type="submit" class="btn btn-primary btn-payment" id="pay-button">
                                <i class="bx bx-credit-card me-1"></i>Pay {{ number_format($subscription->price, 2) }} €
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Formatage automatique du numéro de carte
    const cardNumberInput = document.getElementById('card_number');
    cardNumberInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    });

    // Formatage de la date d'expiration
    const expiryInput = document.getElementById('expiry_date');
    expiryInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value;
    });

    // Validation CVV (chiffres uniquement)
    const cvvInput = document.getElementById('cvv');
    cvvInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });

    // Animation du bouton de paiement
    const payButton = document.getElementById('pay-button');
    const form = document.getElementById('payment-form');
    
    form.addEventListener('submit', function() {
        payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        payButton.disabled = true;
    });
});
</script>

</div>

<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
</body>
</html>