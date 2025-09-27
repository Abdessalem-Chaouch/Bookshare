@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Livres /</span> Détails</h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5>{{ $livre->titre }}</h5>
        </div>
        <div class="card-body row">
            <!-- Couverture -->
            <div class="col-md-3 text-center">
                @if($livre->photo_couverture)
                    <img src="{{ asset('storage/'.$livre->photo_couverture) }}" alt="Couverture" class="img-fluid rounded mb-3">
                @else
                    <span class="text-muted">Aucune couverture</span>
                @endif
            </div>

            <!-- Infos -->
            <div class="col-md-9">
                <p><strong>Auteur :</strong> {{ $livre->auteur }}</p>
                <p><strong>Catégorie :</strong> {{ $livre->categorie?->nom ?? '—' }}</p>
                <p><strong>Description :</strong> {{ $livre->description ?? '—' }}</p>
                <p><strong>ISBN :</strong> {{ $livre->isbn ?? '—' }}</p>
                <p><strong>Disponibilité :</strong> {{ ucfirst($livre->disponibilite) }}</p>
                <p><strong>Stock :</strong> {{ $livre->stock }}</p>
                <p><strong>Prix :</strong> {{ $livre->prix ? number_format($livre->prix, 2, ',', ' ') . ' DT' : 'Non spécifié' }}</p>
                <p><strong>Date d'ajout :</strong> {{ $livre->date_ajout }}</p>

                <!-- Contenu PDF -->
                @if($livre->pdf_contenu)
                    <div class="mt-4">
                        <strong>Contenu PDF :</strong><br>

                        <!-- Boutons -->
                        <a href="{{ route('livres.download', $livre->id) }}" class="btn btn-primary btn-sm mb-3">
                            📥 Télécharger le PDF
                        </a>

                        <!-- Aperçu intégré -->
                        <div class="border rounded shadow">
                            <embed src="{{ route('livres.viewpdf', $livre->id) }}" type="application/pdf" width="100%" height="600px">
                        </div>
                    </div>
                @else
                    <p><em>Aucun PDF disponible pour ce livre.</em></p>
                @endif

                <div class="mt-3">
                    <a href="{{ route('livres.index') }}" class="btn btn-secondary btn-sm">Retour à la liste</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
