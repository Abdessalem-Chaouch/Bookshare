<section id="subscriptions-section" class="py-5" style="background: #e8e6e1;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title mb-3">Choisissez Votre Plan d'Abonnement</h2>
            <p class="text-muted">Devenez auteur et partagez vos livres avec notre communauté</p>
        </div>

        <div class="row justify-content-center">
            @foreach($subscriptions as $index => $subscription)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="subscription-card {{ $index == 1 ? 'featured' : '' }}" data-plan="{{ strtolower($subscription->name) }}">
                    <div class="card-header">
                        <h3 class="plan-name">{{ $subscription->name }}</h3>
                        <p class="plan-description">{{ $subscription->description }}</p>
                        
                        <div class="plan-toggle">
                            <span class="toggle-option active">Mensuel</span>
                            <span class="toggle-option">Annuel</span>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="price-section">
                            <span class="currency">€</span>
                            <span class="price">{{ number_format($subscription->price, 0) }}</span>
                            <span class="period">/mois</span>
                        </div>
                        <p class="billing-info">Facturé mensuellement</p>
                        
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
                                S'ABONNER
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-subscribe">
                                S'ABONNER
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
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

.subscription-card[data-plan="basique"] {
    background: linear-gradient(145deg, #f5f2ee, #a88253);
    color: white;
}

.subscription-card[data-plan="premium"] {
    background: linear-gradient(145deg, #f0e7d6, #6a5331);
    color: white;
}

.subscription-card[data-plan="Étudiant"] {
    background: linear-gradient(145deg, #f3e7c9, #a39673) !important;
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
</style>