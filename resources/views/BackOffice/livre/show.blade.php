@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Books /</span> Details</h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5>{{ $livre->titre }}</h5>
        </div>
        <div class="card-body row">
            <!-- Cover -->
            <div class="col-md-3 text-center">
                @if($livre->photo_couverture)
                    <img src="{{ asset('storage/'.$livre->photo_couverture) }}" alt="Cover" class="img-fluid rounded mb-3">
                @else
                    <span class="text-muted">No cover available</span>
                @endif
            </div>

            <!-- Info -->
            <div class="col-md-9">
              <p><strong>Author:</strong> {{ $livre->user ? $livre->user->name : 'Auteur inconnu' }}</p>

                <p><strong>Category:</strong> {{ $livre->categorie?->name ?? '—' }}</p>
                <p><strong>Description:</strong> {{ $livre->description ?? '—' }}</p>
                <p><strong>ISBN:</strong> {{ $livre->isbn ?? '—' }}</p>
                <p><strong>Availability:</strong> {{ ucfirst($livre->disponibilite) }}</p>
                <p><strong>Stock:</strong> {{ $livre->stock }}</p>
                <p><strong>Price:</strong> {{ $livre->prix ? number_format($livre->prix, 2, ',', ' ') . ' DT' : 'Not specified' }}</p>
                <p><strong>Date Added:</strong> {{ $livre->date_ajout }}</p>

                <!-- PDF Content -->
                @if($livre->pdf_contenu)
                    <div class="mt-4">
                        <strong>PDF Content:</strong><br>

                        <!-- Buttons -->
                        <a href="{{ route('livres.download', $livre->id) }}" class="btn btn-primary btn-sm mb-3">
                            📥 Download PDF
                        </a>

                        <!-- Embedded Preview -->
                        <div class="border rounded shadow">
                            <embed src="{{ route('livres.viewpdf', $livre->id) }}" type="application/pdf" width="100%" height="600px">
                        </div>
                    </div>
                @else
                    <p><em>No PDF available for this book.</em></p>
                @endif

                <div class="mt-3">
                    <a href="{{ route('livres.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
