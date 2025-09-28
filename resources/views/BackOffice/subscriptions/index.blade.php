@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold"><span class="text-muted fw-light">Payments /</span> Abonnements</h4>
        <a href="{{ route('subscriptions.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>Ajouter Abonnement
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('subscriptions.index') }}" class="search-form">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Rechercher un abonnement..." class="form-control">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Rechercher</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Abonnements</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Durée</th>
                        <th>Fonctionnalités</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                    <tr>
                        <td><strong>{{ $subscription->name }}</strong></td>
                        <td>{{ number_format($subscription->price, 2) }} €</td>
                        <td>{{ $subscription->duration_days }} jours</td>
                        <td>
                            <span class="badge bg-info">{{ count($subscription->features) }} fonctionnalités</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $subscription->is_active ? 'success' : 'danger' }}">
                                {{ $subscription->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('subscriptions.edit', $subscription) }}">
                                        <i class="bx bx-edit-alt me-1"></i> Modifier
                                    </a>
                                    <form action="{{ route('subscriptions.destroy', $subscription) }}" method="POST" 
                                          onsubmit="return confirm('Êtes-vous sûr ?')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bx bx-credit-card bx-lg mb-2"></i>
                            <p>Aucun abonnement trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Activer les tooltips Bootstrap
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>

@endsection