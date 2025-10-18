@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">My Subscriptions</h4>
            <p class="text-muted mb-0">Manage your subscription plans</p>
        </div>
        <div class="d-flex gap-2">
            <x-currency-selector />
            <a href="{{ route('payment.history') }}" class="btn btn-outline-primary">
                📊 Payment History
            </a>
        </div>
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
    
    <div id="ai-recommendation" class="ai-recommendation-card mb-4">
        <div class="ai-header">
            <div class="ai-icon-wrapper">
                <i class="bx bx-brain ai-icon"></i>
            </div>
            <div class="ai-title">
                <span class="ai-badge">🤖 AI Recommendation</span>
            </div>
        </div>
        <div class="ai-content" id="recommendation-text">
            <div class="loading-animation">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="ms-2">Analyzing your profile...</span>
            </div>
        </div>
    </div>
    @endif

    @if($currentSubscription)
        <!-- Afficher seulement l'abonnement actuel -->
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card h-100 border-primary shadow-lg" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div class="card-body p-4 text-center">
                        <div class="mb-4">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 80px; height: 80px;">
                                <span class="fs-1 text-white">👑</span>
                            </div>
                            <h4 class="fw-bold mb-1">{{ $currentSubscription->subscription->name }}</h4>
                            <p class="text-muted mb-3">{{ $currentSubscription->subscription->description }}</p>
                            
                            <div class="mb-3">
                                <span class="display-4 fw-bold text-primary">
                                    @php
                                        $currencyService = app(\App\Services\CurrencyService::class);
                                        $userCurrency = $currencyService->getUserCurrency();
                                        $convertedPrice = $currencyService->getLocalizedPrice($currentSubscription->subscription->price, 'USD', $userCurrency);
                                        echo $currencyService->format($convertedPrice, $userCurrency);
                                    @endphp
                                </span>
                                <span class="text-muted">/ {{ $currentSubscription->subscription->duration_days }} days</span>
                            </div>
                        </div>

                        <ul class="list-unstyled text-start mb-4">
                            @foreach($currentSubscription->subscription->features as $feature)
                            <li class="d-flex align-items-center mb-2">
                                <span class="text-success me-2 fs-5">✅</span>
                                <span>{{ $feature }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <div class="d-grid gap-2">
                            <button class="btn btn-success py-2 fw-bold" disabled>
                                ✅ Current Plan
                            </button>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-warning btn-sm" onclick="confirmChangeSubscription()">
                                    🔄 Change
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="confirmUnsubscribe()">
                                    ❌ Unsubscribe
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Carousel pour les plans d'abonnement -->
        <div class="subscription-carousel-container position-relative">
            <button class="carousel-arrow carousel-arrow-left" onclick="slideSubscriptions(-1)">
                <i class="bx bx-chevron-left"></i>
            </button>
            
            <div class="subscription-carousel overflow-hidden">
                <div class="subscription-track d-flex" id="subscriptionTrack" style="transition: transform 0.5s ease;">
                    @foreach($subscriptions as $index => $subscription)
                    <div class="subscription-slide flex-shrink-0" style="width: calc(100% / 3); padding: 0 10px;">
                        <div class="card h-100 border-0 shadow-sm position-relative" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            <div class="card-body p-4 text-center">
                                <div class="mb-4">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                         style="width: 80px; height: 80px;">
                                        <span class="fs-1">{{ $index == 0 ? '⭐' : ($index == 1 ? '👑' : '💎') }}</span>
                                    </div>
                                    <h4 class="fw-bold mb-1">{{ $subscription->name }}</h4>
                                    <p class="text-muted mb-3">{{ $subscription->description }}</p>
                                    
                                    <div class="mb-3">
                                        <span class="display-4 fw-bold text-primary">
                                            @php
                                                $currencyService = app(\App\Services\CurrencyService::class);
                                                echo $currencyService->format($subscription->display_price ?? $subscription->price, $subscription->display_currency ?? 'USD');
                                            @endphp
                                        </span>
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
                                <a href="{{ route('payment.form', $subscription) }}" 
                                   class="btn btn-outline-primary w-100 py-3 fw-bold">
                                    💳 Choose Plan
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <button class="carousel-arrow carousel-arrow-right" onclick="slideSubscriptions(1)">
                <i class="bx bx-chevron-right"></i>
            </button>
        </div>
    @endif
</div>

<style>
/* AI Recommendation Card Styles */
.ai-recommendation-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
    color: white;
    position: relative;
    overflow: hidden;
}

.ai-recommendation-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 3s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.ai-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    position: relative;
    z-index: 1;
}

.ai-icon-wrapper {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    backdrop-filter: blur(10px);
}

.ai-icon {
    font-size: 28px;
    color: white;
    animation: brain-pulse 2s ease-in-out infinite;
}

@keyframes brain-pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.ai-badge {
    background: rgba(255, 255, 255, 0.25);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.ai-content {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    padding: 16px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
    z-index: 1;
    min-height: 60px;
}

.loading-animation {
    display: flex;
    align-items: center;
}

.dot {
    width: 8px;
    height: 8px;
    background: white;
    border-radius: 50%;
    margin: 0 3px;
    animation: bounce 1.4s infinite ease-in-out both;
}

.dot:nth-child(1) { animation-delay: -0.32s; }
.dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes bounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}

/* Recommendation Highlight Styles */
.recommendation-highlight {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0.15) 100%);
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(255, 255, 255, 0.1);
}

.recommendation-highlight::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #fff, #f0f0f0, #fff);
    border-radius: 16px;
    z-index: -1;
    opacity: 0.3;
    animation: glow 3s ease-in-out infinite;
}

@keyframes glow {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.6; }
}

.recommendation-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    margin-bottom: 8px;
    opacity: 0.9;
    background: rgba(255, 255, 255, 0.2);
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
}

.recommendation-plan {
    font-size: 32px;
    font-weight: 800;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    letter-spacing: 0.5px;
    animation: pulse-text 2s ease-in-out infinite;
}

@keyframes pulse-text {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.card.border-primary {
    border: 2px solid var(--bs-primary) !important;
}

.subscription-carousel-container {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
}

.carousel-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.7);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 10;
}

.carousel-arrow:hover {
    background: rgba(0, 0, 0, 0.9);
}

.carousel-arrow-left {
    left: -25px;
}

.carousel-arrow-right {
    right: -25px;
}

@media (max-width: 768px) {
    .subscription-slide {
        width: calc(100% / 2) !important;
    }
}

@media (max-width: 480px) {
    .subscription-slide {
        width: 100% !important;
    }
    
    .carousel-arrow {
        display: none;
    }
}
</style>

<!-- Change Subscription Modal -->
<div class="modal fade" id="changeSubscriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Subscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="ai-recommendation" class="ai-recommendation-card mb-4">
                    <div class="ai-header">
                        <div class="ai-icon-wrapper">
                            <i class="bx bx-brain ai-icon"></i>
                        </div>
                        <div class="ai-title">
                            <span class="ai-badge">🤖 AI Recommendation</span>
                        </div>
                    </div>
                    <div class="ai-content" id="recommendation-text">
                        <div class="loading-animation">
                            <span class="dot"></span>
                            <span class="dot"></span>
                            <span class="dot"></span>
                            <span class="ms-2">Analyzing your profile...</span>
                        </div>
                    </div>
                </div>
                <p class="mb-2"><strong>Do you want to change your current subscription?</strong></p>
                <p class="text-muted small">Your current subscription will be deactivated and you can choose a new plan immediately.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="{{ route('author.subscriptions.change') }}" class="btn btn-warning">Change Subscription</a>
            </div>
        </div>
    </div>
</div>

<!-- Unsubscribe Modal -->
<div class="modal fade" id="unsubscribeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Unsubscribe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Do you really want to unsubscribe?</p>
                <p class="text-muted">Your subscription will be deactivated and you will lose access to premium features.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('author.subscriptions.unsubscribe') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">Unsubscribe</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentSlide = 0;
const totalSlides = {{ count($subscriptions) }};
const slidesPerView = 3;
const maxSlide = Math.max(0, totalSlides - slidesPerView);

function slideSubscriptions(direction) {
    const track = document.getElementById('subscriptionTrack');
    if (!track) return;
    
    currentSlide += direction;
    
    if (currentSlide < 0) {
        currentSlide = 0;
    } else if (currentSlide > maxSlide) {
        currentSlide = maxSlide;
    }
    
    const slideWidth = 100 / slidesPerView;
    const translateX = -currentSlide * slideWidth;
    track.style.transform = `translateX(${translateX}%)`;
    
    updateArrows();
}

function updateArrows() {
    const leftArrow = document.querySelector('.carousel-arrow-left');
    const rightArrow = document.querySelector('.carousel-arrow-right');
    
    if (leftArrow) leftArrow.style.opacity = currentSlide === 0 ? '0.5' : '1';
    if (rightArrow) rightArrow.style.opacity = currentSlide === maxSlide ? '0.5' : '1';
}

// Load recommendation on page load if no subscription
@if(!$currentSubscription)
document.addEventListener('DOMContentLoaded', function() {
    loadRecommendation('recommendation-text');
    updateArrows();
});
@else
document.addEventListener('DOMContentLoaded', function() {
    updateArrows();
});
@endif

function confirmChangeSubscription() {
    loadRecommendation('recommendation-text');
    const modal = new bootstrap.Modal(document.getElementById('changeSubscriptionModal'));
    modal.show();
}

function confirmUnsubscribe() {
    const modal = new bootstrap.Modal(document.getElementById('unsubscribeModal'));
    modal.show();
}

function loadRecommendation(targetId) {
    fetch('/recommendation')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const recommendationText = document.getElementById(targetId);
                recommendationText.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="small opacity-75 mb-1">History</div>
                            <div class="fw-bold">${Math.round(data.data.current_usage.emprunts_mois)} borrows/month</div>
                        </div>
                        <div class="text-end">
                            <div class="small opacity-75 mb-1">Budget</div>
                            <div class="fw-bold">${Math.round(data.data.current_usage.budget_livres)}€/month</div>
                        </div>
                    </div>
                    <div class="recommendation-highlight mb-3">
                        <div class="recommendation-label">Recommended</div>
                        <div class="recommendation-plan">${data.data.recommendation}</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bx bx-rocket me-2" style="font-size: 20px;"></i>
                        <span class="fw-semibold">${data.data.action}</span>
                    </div>
                `;
            }
        })
        .catch(() => {
            document.getElementById(targetId).innerHTML = '<div class="text-center"><i class="bx bx-error-circle me-2"></i>Unable to load recommendation</div>';
        });
}
</script>

@endsection