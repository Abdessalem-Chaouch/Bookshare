@extends('baseB')

@section('title', 'Transactions des Abonnements')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gestion /</span> Transactions des Abonnements
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Historique des Transactions</h5>
            <span class="badge bg-primary">{{ $transactions->count() }} transactions</span>
        </div>
        
        <div class="card-body">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Transaction</th>
                                <th>Auteur</th>
                                <th>Plan</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Méthode</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        <code>{{ $transaction->payment_id }}</code>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                @if($transaction->user->photo_profil)
                                                    <img src="{{ asset('uploads/' . $transaction->user->photo_profil) }}" 
                                                         alt="Avatar" class="rounded-circle">
                                                @else
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $transaction->user->name }}</h6>
                                                <small class="text-muted">{{ $transaction->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $transaction->subscription->name }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</strong>
                                    </td>
                                    <td>
                                        @if($transaction->payment_status === 'completed')
                                            <span class="badge bg-success">Complété</span>
                                        @elseif($transaction->payment_status === 'pending')
                                            <span class="badge bg-warning">En attente</span>
                                        @elseif($transaction->payment_status === 'failed')
                                            <span class="badge bg-danger">Échoué</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($transaction->payment_status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ ucfirst($transaction->payment_method) }}</span>
                                    </td>
                                    <td>
                                        {{ $transaction->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bx bx-credit-card bx-lg text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune transaction trouvée</h5>
                    <p class="text-muted">Aucune transaction d'abonnement n'a été effectuée.</p>
                </div>
            @endif
        </div>
    </div>


</div>
@endsection