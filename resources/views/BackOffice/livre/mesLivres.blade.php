@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Books /</span> My Books</h4>

    <div class="card">
        <h5 class="card-header">My Books</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Availability</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($livres as $livre)
                    <tr>
                        <!-- Cover -->
                        <td>
                            @if($livre->photo_couverture)
                                <img src="{{ asset('storage/'.$livre->photo_couverture) }}"
                                     alt="{{ $livre->titre }}"
                                     class="rounded"
                                     width="60">
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>

                        <!-- Info -->
                        <td>{{ $livre->titre }}</td>
                        <td>{{ $livre->categorie?->name ?? '—' }}</td>

                        <!-- Price -->
                        <td>
                            {{ $livre->prix ? number_format($livre->prix, 2, ',', ' ') . ' DT' : '—' }}
                        </td>

                        <!-- Availability -->
                        <td>
                            <span class="badge
                                {{ $livre->disponibilite == 'disponible' ? 'bg-label-success' : '' }}
                                {{ $livre->disponibilite == 'emprunte' ? 'bg-label-warning' : '' }}
                                {{ $livre->disponibilite == 'reserve' ? 'bg-label-danger' : '' }}">
                                {{ ucfirst($livre->disponibilite) }}
                            </span>
                        </td>

                        <td>{{ $livre->stock }}</td>
   <td>
            @php $avg = $livre->averageRating(); @endphp
            @if($avg)
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= round($avg))
                        <span style="color: gold;">★</span>
                    @else
                        <span style="color: #ccc;">★</span>
                    @endif
                @endfor
                <small>({{ number_format($avg,1) }})</small>
            @else
                <span class="text-muted">No rating</span>
            @endif
        </td>
                        <!-- Actions -->
                        <td>
                            <div class="d-flex">
                                <!-- Details -->
                                <a href="{{ route('livres.show', $livre->id) }}" class="btn btn-sm btn-icon me-1" title="Details">
                                    <i class="bx bx-show"></i>
                                </a>

                                <!-- Edit -->
                                <a href="#" onclick="checkSubscriptionForEdit({{ $livre->id }})" class="btn btn-sm btn-icon me-1" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </a>

                                <!-- Delete -->
                                <button onclick="checkSubscriptionForDelete({{ $livre->id }})" class="btn btn-sm btn-icon me-1" title="Delete">
                                    <i class="bx bx-trash"></i>
                                </button>

                                <!-- Download PDF -->
                                @if($livre->pdf_path)
                                    <a href="{{ route('livres.download', $livre->id) }}" class="btn btn-sm btn-icon" title="Download PDF">
                                        <i class="bx bx-download"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">You haven't added any books yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(auth()->user()->isAuteur())
<!-- Modal pour abonnement requis - Edit -->
<div class="modal fade" id="editSubscriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Abonnement requis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="bx bx-lock-alt bx-lg text-warning mb-3"></i>
                    <h6>Vous devez avoir un abonnement actif pour modifier vos livres.</h6>
                    <p class="text-muted">Choisissez un plan d'abonnement pour débloquer cette fonctionnalité.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="{{ route('author.subscriptions') }}" class="btn btn-primary">Voir les abonnements</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour abonnement requis - Delete -->
<div class="modal fade" id="deleteSubscriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Abonnement requis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="bx bx-lock-alt bx-lg text-danger mb-3"></i>
                    <h6>Vous devez avoir un abonnement actif pour supprimer vos livres.</h6>
                    <p class="text-muted">Choisissez un plan d'abonnement pour débloquer cette fonctionnalité.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="{{ route('author.subscriptions') }}" class="btn btn-primary">Voir les abonnements</a>
            </div>
        </div>
    </div>
</div>

<script>
function checkSubscriptionForEdit(livreId) {
    @if(!auth()->user()->hasActiveSubscription())
        const modal = new bootstrap.Modal(document.getElementById('editSubscriptionModal'));
        modal.show();
    @else
        window.location.href = '/livres/' + livreId + '/edit';
    @endif
}

function checkSubscriptionForDelete(livreId) {
    @if(!auth()->user()->hasActiveSubscription())
        const modal = new bootstrap.Modal(document.getElementById('deleteSubscriptionModal'));
        modal.show();
    @else
        if(confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')) {
            // Créer et soumettre le formulaire de suppression
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/livres/' + livreId;
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    @endif
}
</script>
@endif

@endsection
