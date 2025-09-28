@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Mes Abonnements</h4>

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
            <h5 class="mb-0">Abonnement Actuel</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>{{ $currentSubscription->subscription->name }}</h6>
                    <p>{{ $currentSubscription->subscription->description }}</p>
                    <p><strong>Prix:</strong> {{ number_format($currentSubscription->subscription->price, 2) }} €</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Expire le:</strong> {{ $currentSubscription->expires_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Jours restants:</strong> {{ $currentSubscription->expires_at->diffInDays(now()) }} jours</p>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning">
        <i class="bx bx-info-circle me-2"></i>
        Vous n'avez pas d'abonnement actif. Choisissez un plan ci-dessous pour commencer à ajouter des livres.
    </div>
    @endif

    <div class="row">
        @foreach($subscriptions as $subscription)
        <div class="col-md-4 mb-4">
            <div class="card {{ $currentSubscription && $currentSubscription->subscription_id == $subscription->id ? 'border-success' : '' }}">
                <div class="card-header text-center">
                    <h5>{{ $subscription->name }}</h5>
                    <h3 class="text-primary">{{ number_format($subscription->price, 2) }} €</h3>
                    <small class="text-muted">{{ $subscription->duration_days }} jours</small>
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
                        <span class="badge bg-success">Abonnement Actuel</span>
                    @else
                        <form action="{{ route('author.subscribe', $subscription) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary" 
                                    onclick="return confirm('Confirmer l\'abonnement à {{ $subscription->name }} ?')">
                                S'abonner
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection