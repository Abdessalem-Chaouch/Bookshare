@extends('baseB')

@section('title', 'Abonnements des Auteurs')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gestion /</span> Abonnements des Auteurs
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Abonnements</h5>
            <span class="badge bg-primary">{{ $authorSubscriptions->count() }} abonnements</span>
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

    <!-- Statistiques -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-2">
                        <span class="avatar-initial rounded-circle bg-label-success">
                            <i class="bx bx-check-circle bx-sm"></i>
                        </span>
                    </div>
                    <span class="d-block mb-1 text-nowrap">Abonnements Actifs</span>
                    <h3 class="card-title mb-0">{{ $authorSubscriptions->where('is_active', true)->filter(function($sub) { return $sub->expires_at > now(); })->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-2">
                        <span class="avatar-initial rounded-circle bg-label-danger">
                            <i class="bx bx-x-circle bx-sm"></i>
                        </span>
                    </div>
                    <span class="d-block mb-1 text-nowrap">Abonnements Expirés</span>
                    <h3 class="card-title mb-0">{{ $authorSubscriptions->filter(function($sub) { return $sub->expires_at < now(); })->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-2">
                        <span class="avatar-initial rounded-circle bg-label-info">
                            <i class="bx bx-user bx-sm"></i>
                        </span>
                    </div>
                    <span class="d-block mb-1 text-nowrap">Total Auteurs</span>
                    <h3 class="card-title mb-0">{{ $authorSubscriptions->unique('user_id')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-2">
                        <span class="avatar-initial rounded-circle bg-label-warning">
                            <i class="bx bx-euro bx-sm"></i>
                        </span>
                    </div>
                    <span class="d-block mb-1 text-nowrap">Revenus Total</span>
                    <h3 class="card-title mb-0">{{ number_format($authorSubscriptions->sum(function($sub) { return $sub->subscription->price; }), 2) }} €</h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection