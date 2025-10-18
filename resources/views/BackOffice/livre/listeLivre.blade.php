@extends('baseB')

@extends('baseB')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Books /</span> Book List</h4>
<div class="d-flex justify-content-between mb-3">
    <!-- Barre de recherche -->
    <div class="input-group w-50">
        <input type="text" id="search" class="form-control" placeholder="Rechercher un livre par titre...">
        <span class="input-group-text"><i class="bx bx-search"></i></span>
    </div>

    <!-- Tri -->
    <div class="w-25">
        <select id="sort" class="form-select">
            <option value="titre-asc">Titre (A → Z)</option>
            <option value="titre-desc">Titre (Z → A)</option>
            <option value="prix-asc">Prix croissant</option>
            <option value="prix-desc">Prix décroissant</option>
            <option value="stock-asc">Stock faible → élevé</option>
            <option value="stock-desc">Stock élevé → faible</option>
        </select>
    </div>
</div>
    <!-- Tableau -->
    <div class="card">
        <h5 class="card-header">All Books</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Availability</th>
                        <th>Stock</th>
                        <th>Rating</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="livreData" class="table-border-bottom-0">
                    @foreach($livres as $livre)
                    <tr>
                        <td>
                            @if($livre->photo_couverture)
                                <img src="{{ asset('storage/'.$livre->photo_couverture) }}" width="60" class="rounded" alt="">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $livre->titre }}</td>
                        <td>{{ $livre->auteur?->name ?? '—' }}</td>
                        <td>{{ $livre->categorie?->name ?? '—' }}</td>
                        <td>{{ $livre->prix ? number_format($livre->prix, 2, ',', ' ') . ' DT' : '—' }}</td>
                        <td>
                            <span class="badge
                                {{ $livre->disponibilite == 'disponible' ? 'bg-label-success' : '' }}
                                {{ $livre->disponibilite == 'emprunte' ? 'bg-label-warning' : '' }}
                                {{ $livre->disponibilite == 'reserve' ? 'bg-label-danger' : '' }}">
                                {{ ucfirst($livre->disponibilite) }}
                            </span>
                        </td>
                        <td>{{ $livre->stock }}</td>
                        <td> @php $avg = $livre->averageRating(); @endphp @if($avg) @for($i = 1; $i <= 5; $i++) @if($i <= round($avg)) <span style="color: gold;">★</span> @else <span style="color: #ccc;">★</span> @endif @endfor <small>({{ number_format($avg,1) }})</small> @else <span class="text-muted">No rating</span> @endif </td>
                        <td>
                            <a href="{{ route('livres.show', $livre->id) }}" class="btn btn-sm btn-icon me-1"><i class="bx bx-show"></i></a>
                            @if(!auth()->user()->isAuteur())
                                <a href="{{ route('livres.edit', $livre->id) }}" class="btn btn-sm btn-icon me-1"><i class="bx bx-edit-alt"></i></a>
                            <form action="{{ route('livres.destroy', $livre->id) }}" method="POST" class="d-inline"> @csrf @method('DELETE') <button type="submit" class="btn btn-sm btn-icon me-1" title="Delete"> <i class="bx bx-trash"></i> </button> </form>
                                @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- jQuery (vérifie qu'il n'existe pas une autre inclusion ailleurs qui pourrait casser) -->
<script>
$(function(){

    function debounce(fn, delay) {
        let timer = null;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(context, args), delay);
        };
    }

    // Fonction pour mettre à jour le tableau
    function renderRows(livres) {
        const $tbody = $('#livreData');
        $tbody.empty();

        if (!livres || livres.length === 0) {
            $tbody.append('<tr><td colspan="8" class="text-center text-muted">Aucun livre trouvé</td></tr>');
            return;
        }

        livres.forEach(livre => {
            const img = livre.photo_couverture
                ? `<img src="/storage/${livre.photo_couverture}" width="60" class="rounded" alt="">`
                : '<span class="text-muted">—</span>';

            const badgeClass = livre.disponibilite === 'disponible'
                ? 'bg-label-success'
                : livre.disponibilite === 'emprunte'
                    ? 'bg-label-warning'
                    : 'bg-label-danger';

            const prix = (livre.prix !== null && livre.prix !== undefined) ? parseFloat(livre.prix).toFixed(2) + ' DT' : '—';
            const categorie = livre.categorie ?? '—';

            const row = `
                <tr>
                    <td>${img}</td>
                    <td>${livre.titre ?? '—'}</td>
                    <td>${livre.auteur ?? '—'}</td>
                    <td>${categorie}</td>
                    <td>${prix}</td>
                    <td><span class="badge ${badgeClass}">${livre.disponibilite ?? '—'}</span></td>
                    <td>${livre.stock ?? 0}</td>
                    <td>
                        <a href="/livres/${livre.id}" class="btn btn-sm btn-icon me-1"><i class="bx bx-show"></i></a>
                        <a href="/livres/${livre.id}/edit" class="btn btn-sm btn-icon me-1"><i class="bx bx-edit-alt"></i></a>
                    </td>
                </tr>
            `;
            $tbody.append(row);
        });
    }

    // Fonction AJAX pour recherche uniquement
    function searchBooks() {
        const query = $('#search').val().trim();
        $.ajax({
            url: "{{ route('livres.search') }}",
            method: 'GET',
            data: { query: query },
            dataType: 'json',
            success: function(resp) {
                renderRows(resp.livres || []);
            },
            error: function() {
                $('#livreData').html('<tr><td colspan="8" class="text-center text-danger">Erreur serveur</td></tr>');
            }
        });
    }

    // Fonction AJAX pour tri uniquement
    function sortBooks() {
        const sortVal = $('#sort').val(); // ex: titre-asc
        const [column, order] = sortVal.split('-');
        $.ajax({
            url: "{{ route('livres.sort') }}", // tu peux créer une route séparée si nécessaire
            method: 'GET',
            data: { column, order },
            dataType: 'json',
            success: function(resp) {
                renderRows(resp.livres || []);
            },
            error: function() {
                $('#livreData').html('<tr><td colspan="8" class="text-center text-danger">Erreur serveur</td></tr>');
            }
        });
    }

    // Déclencheurs
    $('#search').on('keyup', debounce(searchBooks, 300));
    $('#sort').on('change', sortBooks);

});
</script>


@endsection
