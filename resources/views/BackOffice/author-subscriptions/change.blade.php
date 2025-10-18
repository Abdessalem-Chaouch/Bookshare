@extends('layouts.simple')

@section('content')
<div class="min-vh-100 bg-light">
    <div class="container py-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('author.subscriptions') }}" class="btn btn-outline-primary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Back to subscriptions
            </a>
            <x-currency-selector />
        </div>
        <div class="text-center mb-5">
            <h1 class="text-dark fw-bold mb-2">Change Subscription</h1>
            <p class="text-muted">Select the plan that best fits your needs</p>
        </div>

        @if($currentSubscription)
        <div class="alert alert-light border-0 shadow-sm mb-5 rounded-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle p-2 me-3">
                    <i class="bx bx-crown text-white"></i>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold">Current Subscription</h6>
                    <p class="mb-0 text-muted">
                        <strong>{{ $currentSubscription->subscription->name }}</strong> - 
                        Expires on {{ $currentSubscription->expires_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Plans -->
        <div class="row g-4 justify-content-center">
            @foreach($subscriptions as $index => $subscription)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-lg rounded-4 position-relative overflow-hidden
                    {{ $currentSubscription && $currentSubscription->subscription_id == $subscription->id ? 'border-warning' : '' }}"
                    style="transition: all 0.3s ease;">
                    


                    @if($currentSubscription && $currentSubscription->subscription_id == $subscription->id)
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-success rounded-pill px-3 py-2">
                            <i class="bx bx-check me-1"></i>Current
                        </span>
                    </div>
                    @endif

                    <div class="card-body p-4 text-center">
                        <div class="mb-4">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 80px; height: 80px;">
                                <span class="fs-1">{{ $index == 0 ? '📚' : ($index == 1 ? '👑' : '💎') }}</span>
                            </div>
                            <h4 class="fw-bold mb-2">{{ $subscription->name }}</h4>
                            <p class="text-muted mb-3">{{ $subscription->description }}</p>
                            
                            <div class="mb-4">
                                <span class="display-4 fw-bold" style="color: {{ $index == 0 ? '#28a745' : ($index == 1 ? '#007bff' : '#6f42c1') }};">
                                    @php
                                        $currencyService = app(\App\Services\CurrencyService::class);
                                        echo $currencyService->format($subscription->display_price ?? $subscription->price, $subscription->display_currency ?? 'USD');
                                    @endphp
                                </span>
                                <div class="text-muted">/ {{ $subscription->duration_days }} days</div>
                            </div>
                        </div>

                        <ul class="list-unstyled text-start mb-4">
                            @if($subscription->features)
                                @foreach(is_array($subscription->features) ? $subscription->features : json_decode($subscription->features) as $feature)
                                <li class="d-flex align-items-center mb-3">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 20px; height: 20px; min-width: 20px;">
                                        <i class="bx bx-check text-white" style="font-size: 12px;"></i>
                                    </div>
                                    <span>{{ $feature }}</span>
                                </li>
                                @endforeach
                            @endif
                        </ul>

                        @if($currentSubscription && $currentSubscription->subscription_id == $subscription->id)
                        <button class="btn btn-success w-100 py-3 rounded-3 fw-bold" disabled>
                            <i class="bx bx-check me-2"></i>Current Plan
                        </button>
                        @else
                        <form action="{{ route('author.subscriptions.process-change', $subscription) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="btn w-100 py-3 rounded-3 fw-bold"
                                    style="background: {{ $index == 0 ? 'linear-gradient(45deg, #28a745, #20c997)' : ($index == 1 ? 'linear-gradient(45deg, #007bff, #6610f2)' : 'linear-gradient(45deg, #6f42c1, #e83e8c)') }}; border: none; color: white;"
                                    onclick="return confirm('Are you sure you want to change to this plan?')">
                                <i class="bx bx-refresh me-2"></i>Change to this plan
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('author.subscriptions') }}" class="btn btn-secondary btn-lg rounded-3 px-4">
                <i class="bx bx-arrow-back me-2"></i>Cancel and return
            </a>
        </div>
    </div>
</div>

<style>
.card:hover {
    transform: translateY(-10px) !important;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.alert {
    backdrop-filter: blur(10px);
}
</style>
@endsection