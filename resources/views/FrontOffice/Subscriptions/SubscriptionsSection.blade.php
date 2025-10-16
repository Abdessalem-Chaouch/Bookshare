<section id="subscriptions-section" class="py-5" style="background: #e8e6e1;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="text-center flex-grow-1">
                <h2 class="section-title mb-2">Choose Your Subscription Plan</h2>
                <p class="text-muted mb-0">Become an author and share your books with our community</p>
            </div>
            <x-currency-selector />
        </div>

        <div class="subscription-carousel-container position-relative">
            <button class="carousel-arrow carousel-arrow-left" onclick="slideSubscriptions(-1)">
                <i class="bx bx-chevron-left"></i>
            </button>
            
            <div class="subscription-carousel">
                <div class="subscription-track" id="subscriptionTrack">
                    @foreach($subscriptions as $index => $subscription)
                    <div class="subscription-slide">
                        <div class="subscription-card {{ $index == 1 ? 'featured' : '' }}">
                            <div class="card-header">
                                <h3 class="plan-name">{{ $subscription->name }}</h3>
                                <p class="plan-description">{{ $subscription->description }}</p>
                                
                                <div class="plan-toggle">
                                    <span class="toggle-option active">Monthly</span>
                                    <span class="toggle-option">Annual</span>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="price-section">
                                    @php
                                        $currencyService = app(\App\Services\CurrencyService::class);
                                        $userCurrency = $currencyService->getUserCurrency();
                                        $convertedPrice = $currencyService->getLocalizedPrice($subscription->price, 'USD', $userCurrency);
                                        $currencyData = $currencyService->getCurrency($userCurrency);
                                    @endphp
                                    <span class="currency">{{ $currencyData['symbol'] ?? '$' }}</span>
                                    <span class="price">{{ number_format($convertedPrice, 0) }}</span>
                                    <span class="period">/month</span>
                                </div>
                                <p class="billing-info">Billed monthly</p>
                                
                                <ul class="features-list">
                                    @foreach($subscription->features as $feature)
                                    <li>
                                        <i class="bx bx-check"></i>
                                        {{ $feature }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            <div class="card-footer">
                                @auth
                                    <a href="{{ route('payment.form', $subscription) }}" class="btn-subscribe">
                                        SUBSCRIBE
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn-subscribe">
                                        SUBSCRIBE
                                    </a>
                                @endauth
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
    </div>
</section>



<style>
.subscription-carousel-container {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
}

.subscription-carousel {
    overflow: hidden;
    width: 100%;
}

.subscription-track {
    display: flex;
    transition: transform 0.5s ease;
    gap: 20px;
}

.subscription-slide {
    width: calc((100% - 40px) / 3);
    flex-shrink: 0;
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


.subscription-card {
    border-radius: 20px;
    padding: 0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.subscription-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.subscription-card {
    background: linear-gradient(145deg, #dccdb6, #9d7d42);
    color: white;
}

.card-header {
    padding: 30px 25px 20px;
    text-align: center;
}

.plan-name {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.plan-description {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-bottom: 20px;
}

.plan-toggle {
    display: flex;
    background: rgba(255,255,255,0.2);
    border-radius: 25px;
    padding: 5px;
    margin: 0 auto;
    width: fit-content;
}

.toggle-option {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.toggle-option.active {
    background: rgba(255,255,255,0.3);
    font-weight: 600;
}

.card-body {
    padding: 20px 25px;
    flex-grow: 1;
}

.price-section {
    text-align: center;
    margin-bottom: 10px;
}

.currency {
    font-size: 1.2rem;
    vertical-align: top;
    margin-top: 10px;
}

.price {
    font-size: 3rem;
    font-weight: 700;
    line-height: 1;
}

.period {
    font-size: 1rem;
    opacity: 0.8;
}

.billing-info {
    text-align: center;
    font-size: 0.8rem;
    opacity: 0.7;
    margin-bottom: 25px;
}

.features-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.features-list li {
    padding: 8px 0;
    display: flex;
    align-items: center;
    font-size: 0.9rem;
}

.features-list i {
    margin-right: 10px;
    font-size: 1.2rem;
    opacity: 0.8;
}

.card-footer {
    padding: 25px;
    text-align: center;
}

.btn-subscribe {
    background: rgba(255,255,255,0.2);
    color: inherit;
    border: 2px solid rgba(255,255,255,0.3);
    padding: 15px 30px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
    width: 100%;
    cursor: pointer;
}

.btn-subscribe:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
    color: inherit;
}

.btn-subscribe:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .subscription-slide {
        width: calc((100% - 20px) / 2);
    }
}

@media (max-width: 480px) {
    .subscription-slide {
        width: 100%;
    }
    
    .carousel-arrow {
        display: none;
    }
}
</style>

<script>
let currentSlide = 0;
const totalSlides = {{ count($subscriptions) }};
const slidesPerView = 3;
const maxSlide = Math.max(0, totalSlides - slidesPerView);

function slideSubscriptions(direction) {
    const track = document.getElementById('subscriptionTrack');
    
    currentSlide += direction;
    
    if (currentSlide < 0) {
        currentSlide = 0;
    } else if (currentSlide > maxSlide) {
        currentSlide = maxSlide;
    }
    
    const slideWidth = 100 / slidesPerView;
    const translateX = -currentSlide * slideWidth;
    track.style.transform = `translateX(${translateX}%)`;
    
    // Update arrow visibility
    updateArrows();
}

function updateArrows() {
    const leftArrow = document.querySelector('.carousel-arrow-left');
    const rightArrow = document.querySelector('.carousel-arrow-right');
    
    leftArrow.style.opacity = currentSlide === 0 ? '0.5' : '1';
    rightArrow.style.opacity = currentSlide === maxSlide ? '0.5' : '1';
}

// Initialize arrows on page load
document.addEventListener('DOMContentLoaded', function() {
    updateArrows();
});
</script>