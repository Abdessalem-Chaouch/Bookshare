@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Historique des paiements</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Mes transactions</h5>
            <span class="badge bg-primary">{{ $payments->count() }} transaction(s)</span>
        </div>
        <div class="card-body">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Abonnement</th>
                                <th>Montant</th>
                                <th>Méthode</th>
                                <th>Statut</th>
                                <th>Transaction ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong>{{ $payment->subscription->name }}</strong><br>
                                    <small class="text-muted">{{ $payment->subscription->duration_days }} jours</small>
                                </td>
                                <td>
                                    <strong>{{ number_format($payment->amount, 2) }} €</strong>
                                </td>
                                <td>
                                    <i class="bx bx-credit-card me-1"></i>
                                    Carte ****{{ $payment->payment_data['card_last_four'] ?? '****' }}
                                </td>
                                <td>
                                    @switch($payment->status)
                                        @case('completed')
                                            <span class="badge bg-success">Réussi</span>
                                            @break
                                        @case('failed')
                                            <span class="badge bg-danger">Échoué</span>
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning">En attente</span>
                                            @break
                                        @case('refunded')
                                            <span class="badge bg-info">Remboursé</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $payment->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <code>{{ $payment->transaction_id }}</code>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bx bx-receipt" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="text-muted mt-2">Aucune transaction trouvée</p>
                    <a href="{{ route('author.subscriptions') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>Souscrire un abonnement
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection