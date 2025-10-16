@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">My Subscriptions</h4>
            <p class="text-muted mb-0">Manage your subscription plans</p>
        </div>
        <a href="{{ route('payment.history') }}" class="btn btn-outline-primary">
            📊 Payment History
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($currentSubscription)
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="fs-4 me-2">👑</span>
                        <h5 class="mb-0 text-white">Current Subscription</h5>
                    </div>
                    <h3 class="mb-1 text-white">{{ $currentSubscription->subscription->name }}</h3>
                    <p class="mb-0 opacity-75">{{ $currentSubscription->subscription->description }}</p>
                </div>
                <div class="text-end">
                    <div class="text-white">
                        <small class="opacity-75">Expires on</small>
                        <div class="fw-bold">{{ $currentSubscription->expires_at->format('d/m/Y') }}</div>
                        <small class="opacity-75">{{ floor($currentSubscription->expires_at->diffInDays(now())) }} days left</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info border-0 shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <span class="fs-4 me-3">ℹ️</span>
            <div>
                <h6 class="mb-1">No Active Subscription</h6>
                <p class="mb-0">Choose a plan below to start adding books and unlock all features.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        @foreach($subscriptions as $index => $subscription)
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm position-relative {{ $currentSubscription && $currentSubscription->subscription_id == $subscription->id ? 'border-primary' : '' }}" 
                 style="transition: transform 0.3s ease, box-shadow 0.3s ease;">

                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 80px; height: 80px;">
                            <span class="fs-1">{{ $index == 0 ? '⭐' : ($index == 1 ? '👑' : '💎') }}</span>
                        </div>
                        <h4 class="fw-bold mb-1">{{ $subscription->name }}</h4>
                        <p class="text-muted mb-3">{{ $subscription->description }}</p>
                        
                        <div class="mb-3">
                            <span class="display-4 fw-bold text-primary">${{ number_format($subscription->price, 0) }}</span>
                            <span class="text-muted">/ {{ $subscription->duration_days }} days</span>
                        </div>
                    </div>

                    <ul class="list-unstyled text-start mb-4">
                        @foreach($subscription->features as $feature)
                        <li class="d-flex align-items-center mb-2">
                            <span class="text-success me-2 fs-5">✅</span>
                            <span>{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-footer bg-transparent border-0 p-4 pt-0">
                    @if($currentSubscription && $currentSubscription->subscription_id == $subscription->id)
                        <button class="btn btn-success w-100 py-3 fw-bold" disabled>
                            ✅ Current Plan
                        </button>
                    @else
                        <a href="{{ route('payment.form', $subscription) }}" 
                           class="btn btn-outline-primary w-100 py-3 fw-bold">
                            💳 Choose Plan
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.card.border-primary {
    border: 2px solid var(--bs-primary) !important;
}
</style>

@endsection