@extends('baseF')

@section('content')
<div class="container text-center py-5">
    <div class="card shadow p-5">
        <h1 class="text-success mb-4">✅ Paiement réussi !</h1>
        <p>Merci pour votre achat. Votre paiement a été traité avec succès.</p>

        <a href="{{ url('/') }}" class="btn btn-primary mt-4">🏠 Retour à l'accueil</a>
    </div>
</div>
@endsection
