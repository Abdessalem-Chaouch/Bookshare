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
                                <span class="display-4 fw-bold text-primary">${{ number_format($currentSubscription->subscription->price, 0) }}</span>
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
                                    🔄 Changer
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="confirmUnsubscribe()">
                                    ❌ Se désabonner
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Afficher tous les plans si pas d'abonnement -->
        <div class="row g-4">
            @foreach($subscriptions as $index => $subscription)
            <div class="col-lg-4 col-md-6">
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
                        <a href="{{ route('payment.form', $subscription) }}" 
                           class="btn btn-outline-primary w-100 py-3 fw-bold">
                            💳 Choose Plan
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
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

<!-- Modal pour changer d'abonnement -->
<div class="modal fade" id="changeSubscriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer d'abonnement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="ai-recommendation" class="alert alert-info mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-brain" style="font-size: 24px; margin-right: 10px;"></i>
                        <div>
                            <strong>🤖 Recommandation AI</strong>
                            <div id="recommendation-text">Analyse de votre profil en cours...</div>
                        </div>
                    </div>
                </div>
                <p>Voulez-vous changer votre abonnement actuel ?</p>
                <p class="text-muted">Votre abonnement actuel sera désactivé et vous pourrez choisir un nouveau plan immédiatement.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('author.subscriptions.change') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">Changer d'abonnement</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour se désabonner -->
<div class="modal fade" id="unsubscribeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Se désabonner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="ai-recommendation-unsub" class="alert alert-warning mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-brain" style="font-size: 24px; margin-right: 10px;"></i>
                        <div>
                            <strong>🤖 Recommandation AI</strong>
                            <div id="recommendation-text-unsub">Analyse de votre profil en cours...</div>
                        </div>
                    </div>
                </div>
                <p>Voulez-vous vraiment vous désabonner ?</p>
                <p class="text-muted">Votre abonnement sera désactivé et vous perdrez l'accès aux fonctionnalités premium.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('author.subscriptions.unsubscribe') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">Se désabonner</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmChangeSubscription() {
    loadRecommendation('recommendation-text');
    const modal = new bootstrap.Modal(document.getElementById('changeSubscriptionModal'));
    modal.show();
}

function confirmUnsubscribe() {
    loadRecommendation('recommendation-text-unsub');
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
                    <div><strong>Historique:</strong> ${Math.round(data.data.current_usage.emprunts_mois)} emprunts de livres/mois</div>
                    <div><strong>Budget livres:</strong> ${Math.round(data.data.current_usage.budget_livres)}€/mois</div>
                    <div><strong>Recommandé:</strong> ${data.data.recommendation}</div>
                    <div style="color: #0066cc; font-weight: bold;">${data.data.action}</div>
                `;
            }
        })
        .catch(() => {
            document.getElementById(targetId).innerHTML = 'Impossible de charger la recommandation';
        });
}
</script>

@endsection