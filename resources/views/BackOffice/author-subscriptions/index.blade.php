@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">My Subscriptions</h4>
        <a href="{{ route('payment.history') }}" class="btn btn-outline-primary">
            <i class="bx bx-history me-1"></i>Payment History
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($currentSubscription)
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Current Subscription</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>{{ $currentSubscription->subscription->name }}</h6>
                    <p>{{ $currentSubscription->subscription->description }}</p>
                    <p><strong>Price:</strong> {{ number_format($currentSubscription->subscription->price, 2) }} €</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Expires on:</strong> {{ $currentSubscription->expires_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Days remaining:</strong> {{ floor($currentSubscription->expires_at->diffInDays(now())) }} days</p>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning">
        <i class="bx bx-info-circle me-2"></i>
        You don't have an active subscription. Choose a plan below to start adding books.
    </div>
    @endif

    <div class="row">
        @foreach($subscriptions as $subscription)
        <div class="col-md-4 mb-4">
            <div class="card {{ $currentSubscription && $currentSubscription->subscription_id == $subscription->id ? 'border-success' : '' }}">
                <div class="card-header text-center">
                    <h5>{{ $subscription->name }}</h5>
                    <h3 class="text-primary">{{ number_format($subscription->price, 2) }} €</h3>
                    <small class="text-muted">{{ $subscription->duration_days }} days</small>
                </div>
                <div class="card-body">
                    <p>{{ $subscription->description }}</p>
                    <ul class="list-unstyled">
                        @foreach($subscription->features as $feature)
                        <li><i class="bx bx-check text-success me-2"></i>{{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer text-center">
                    @if($currentSubscription && $currentSubscription->subscription_id == $subscription->id)
                        <span class="badge bg-success">Current Subscription</span>
                    @else
                        <a href="{{ route('payment.form', $subscription) }}" class="btn btn-primary">
                            <i class="bx bx-credit-card me-1"></i>Subscribe
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection