@extends('baseB')

@section('title', 'Abonnements des Auteurs')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gestion /</span> Abonnements des Auteurs
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Abonnements</h5>
            <div class="d-flex gap-2">
                <button id="aiAnalysisBtn" class="btn btn-primary btn-sm">
                    <i class="bx bx-brain"></i> 📊 Analyse IA
                </button>
                <span class="badge bg-primary">{{ $authorSubscriptions->count() }} abonnements</span>
            </div>
        </div>
        
        <div class="card-body">
            @if($authorSubscriptions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Auteur</th>
                                <th>Email</th>
                                <th>Plan</th>
                                <th>Prix</th>
                                <th>Date de début</th>
                                <th>Date d'expiration</th>
                                <th>Statut</th>
                                <th>Durée</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($authorSubscriptions as $subscription)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                @if($subscription->user->photo_profil)
                                                    <img src="{{ asset('uploads/' . $subscription->user->photo_profil) }}" 
                                                         alt="Avatar" class="rounded-circle">
                                                @else
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($subscription->user->name, 0, 1)) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $subscription->user->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $subscription->user->email }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $subscription->subscription->name }}</span>
                                    </td>
                                    <td>{{ number_format($subscription->subscription->price, 2) }} €</td>
                                    <td>{{ $subscription->starts_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $subscription->expires_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($subscription->isActive())
                                            <span class="badge bg-success">Actif</span>
                                        @elseif($subscription->isExpired())
                                            <span class="badge bg-danger">Expiré</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $duration = $subscription->starts_at->diffInDays($subscription->expires_at);
                                        @endphp
                                        {{ $duration }} jours
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.author-subscriptions.destroy', $subscription->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet abonnement ?')">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bx bx-package bx-lg text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun abonnement trouvé</h5>
                    <p class="text-muted">Aucun auteur ne s'est encore abonné à un plan.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal pour l'analyse IA -->
    <div class="modal fade" id="aiAnalysisModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title">
                        <i class="bx bx-brain me-2"></i>
                        Analyse IA des Abonnements
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="aiAnalysisContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Génération en cours...</span>
                            </div>
                            <h6 class="text-primary">Analyse IA en cours...</h6>
                            <p class="text-muted mb-0">Traitement des données d'abonnements</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.getElementById('aiAnalysisBtn').addEventListener('click', function() {
    const modal = new bootstrap.Modal(document.getElementById('aiAnalysisModal'));
    modal.show();
    
    fetch('{{ route("admin.author-subscriptions.ai-analysis") }}?t=' + Date.now())
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let content = '';
                
                // Header avec type d'analyse
                const isML = data.source === 'machine_learning';
                const confidenceBadge = data.confidence === 'TRÈS ÉLEVÉE' ? 'success' : 
                                       data.confidence === 'ÉLEVÉE' ? 'primary' : 
                                       data.confidence === 'MOYENNE' ? 'warning' : 'secondary';
                
                content += `
                    <div class="alert alert-primary border-0 shadow-sm mb-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bx bx-brain fs-3 text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="alert-heading mb-1">Analyse Machine Learning</h6>
                                <span class="badge bg-${confidenceBadge}">Confiance: ${data.confidence}</span>
                            </div>
                        </div>
                    </div>
                `;
                
                // Prédictions ML en cartes modernes
                if (data.ml_predictions) {
                    content += `
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-sm bg-label-danger me-2">
                                                <i class="bx bx-trending-down"></i>
                                            </div>
                                            <h6 class="card-title mb-0">Prédiction Churn</h6>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">Taux actuel</span>
                                                <strong class="text-danger">${data.ml_predictions.churn_prediction.current_churn_rate}%</strong>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-danger" style="width: ${data.ml_predictions.churn_prediction.current_churn_rate}%"></div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Prédit: ${data.ml_predictions.churn_prediction.predicted_churn_rate}%</span>
                                            <span class="badge bg-${data.ml_predictions.churn_prediction.risk_level === 'FAIBLE' ? 'success' : data.ml_predictions.churn_prediction.risk_level === 'MODÉRÉ' ? 'warning' : 'danger'}">
                                                ${data.ml_predictions.churn_prediction.risk_level}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-sm bg-label-success me-2">
                                                <i class="bx bx-trending-up"></i>
                                            </div>
                                            <h6 class="card-title mb-0">Prévision Croissance</h6>
                                        </div>
                                        <div class="row g-2 text-center">
                                            <div class="col-6">
                                                <div class="border rounded p-2">
                                                    <div class="text-success fw-bold">${data.ml_predictions.growth_forecast.current_month || 0}</div>
                                                    <small class="text-muted">Ce mois</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="border rounded p-2">
                                                    <div class="text-primary fw-bold">${data.ml_predictions.growth_forecast.next_month}</div>
                                                    <small class="text-muted">Prochain</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-center">
                                            <span class="badge bg-${data.ml_predictions.growth_forecast.growth_rate >= 0 ? 'success' : 'danger'} fs-6">
                                                ${data.ml_predictions.growth_forecast.growth_rate >= 0 ? '↗' : '↘'} ${data.ml_predictions.growth_forecast.growth_rate}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                // Analyse principale avec style amélioré
                content += `
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h6 class="card-title mb-0">
                                <i class="bx bx-analyse text-primary me-2"></i>
                                Analyse Détaillée
                            </h6>
                        </div>
                        <div class="card-body pt-2">
                            <div class="analysis-content" style="white-space: pre-line; line-height: 1.6; font-size: 0.95em;">${data.analysis}</div>
                        </div>
                    </div>
                `;
                
                // Recommandations ML
                if (data.recommendations && data.recommendations.length > 0) {
                    content += `
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">🎯 Recommandations ML</h6>
                                <ul class="list-unstyled mb-0">
                    `;
                    data.recommendations.forEach(rec => {
                        content += `<li class="mb-2"><i class="bx bx-check-circle text-primary"></i> ${rec}</li>`;
                    });
                    content += `
                                </ul>
                            </div>
                        </div>
                    `;
                }
                
                // Stats de base avec design moderne
                content += `
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <div class="row g-3 text-center">
                                <div class="col-3">
                                    <div class="d-flex flex-column">
                                        <span class="text-primary fw-bold fs-4">${data.stats.total}</span>
                                        <small class="text-muted">Total</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="d-flex flex-column">
                                        <span class="text-success fw-bold fs-4">${data.stats.active}</span>
                                        <small class="text-muted">Actifs</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="d-flex flex-column">
                                        <span class="text-info fw-bold fs-4">${data.stats.this_month}</span>
                                        <small class="text-muted">Ce mois</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="d-flex flex-column">
                                        <span class="text-warning fw-bold fs-4">${data.stats.conversion_rate}%</span>
                                        <small class="text-muted">Taux</small>
                                    </div>
                                </div>
                            </div>
                            ${data.timestamp ? `<div class="text-center mt-3 pt-3 border-top"><small class="text-muted"><i class="bx bx-time-five me-1"></i>Dernière mise à jour: ${data.timestamp}</small></div>` : ''}
                        </div>
                    </div>
                `;
                
                document.getElementById('aiAnalysisContent').innerHTML = content;
            } else {
                document.getElementById('aiAnalysisContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bx bx-error"></i> Erreur lors de l'analyse ML: ${data.message || 'Erreur inconnue'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('aiAnalysisContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bx bx-error"></i> Erreur de connexion. Vérifiez que votre serveur et base de données sont actifs.
                </div>
            `;
        });
});
</script>

@endsection