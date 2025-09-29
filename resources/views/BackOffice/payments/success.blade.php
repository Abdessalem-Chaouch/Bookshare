@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="bx bx-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-success mb-3">Paiement réussi !</h3>
                    <p class="text-muted mb-4">
                        Votre abonnement a été activé avec succès. Vous pouvez maintenant profiter de tous les avantages de votre plan.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('author.subscriptions') }}" class="btn btn-primary">
                            <i class="bx bx-user me-1"></i>Mes abonnements
                        </a>
                        <a href="{{ route('dashboardAuteur') }}" class="btn btn-outline-primary">
                            <i class="bx bx-home me-1"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection