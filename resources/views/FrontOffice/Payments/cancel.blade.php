@extends('baseF')

@section('content')
<div class="container text-center py-5">
    <div class="card shadow p-5">
        <h1 class="text-danger mb-4">❌ Paiement annulé</h1>
        <p>Votre paiement a été annulé. Vous pouvez réessayer ou revenir à l'accueil.</p>

        <a href="{{ url('/') }}" class="btn btn-secondary mt-4">🏠 Retour à l'accueil</a>
    </div>
</div>
@endsection
