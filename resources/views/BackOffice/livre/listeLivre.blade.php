@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Livres /</span> Liste des livres</h4>

    <div class="card">
        <h5 class="card-header">Tous les livres</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Couverture</th>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Disponibilité</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($livres as $livre)
                    <tr>
                        <!-- Image -->
                        <td>
                            @if($livre->photo_couverture)
                                <img src="{{ asset('storage/'.$livre->photo_couverture) }}"
                                     alt="{{ $livre->titre }}"
                                     class="rounded"
                                     width="60">
                            @else
                                <span class="text-muted">Aucune</span>
                            @endif
                        </td>

                        <!-- Infos -->
                        <td>{{ $livre->titre }}</td>
                        <td>{{ $livre->auteur }}</td>
                        <td>{{ $livre->categorie?->name ?? '—' }}</td>

                        <!-- Prix -->
                        <td>
                            {{ $livre->prix ? number_format($livre->prix, 2, ',', ' ') . ' DT' : '—' }}
                        </td>

                        <!-- Disponibilité -->
                        <td>
                            <span class="badge
                                {{ $livre->disponibilite == 'disponible' ? 'bg-label-success' : '' }}
                                {{ $livre->disponibilite == 'emprunte' ? 'bg-label-warning' : '' }}
                                {{ $livre->disponibilite == 'reserve' ? 'bg-label-danger' : '' }}">
                                {{ ucfirst($livre->disponibilite) }}
                            </span>
                        </td>

                        <td>{{ $livre->stock }}</td>

                        <!-- Actions -->
                        <td>
                            <div class="d-flex">
                                <!-- Détails -->
                                <a href="{{ route('livres.show', $livre->id) }}" class="btn btn-sm btn-icon me-1" title="Détails">
                                    <i class="bx bx-show"></i>
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('livres.edit', $livre->id) }}" class="btn btn-sm btn-icon me-1" title="Modifier">
                                    <i class="bx bx-edit-alt"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('livres.destroy', $livre->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon me-1" title="Supprimer">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>

                                <!-- Télécharger PDF -->
                                @if($livre->pdf_path)
                                    <a href="{{ route('livres.download', $livre->id) }}" class="btn btn-sm btn-icon" title="Télécharger PDF">
                                        <i class="bx bx-download"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

@endsection
