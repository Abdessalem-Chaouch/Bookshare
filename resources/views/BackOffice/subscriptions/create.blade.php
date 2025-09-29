@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Payments /</span> Ajouter Abonnement</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Nouvel Abonnement</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('subscriptions.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="name">Nom</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 form-label" for="description">Description</label>
                    <div class="col-sm-10">
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="price">Prix (€)</label>
                    <div class="col-sm-10">
                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                               id="price" name="price" value="{{ old('price') }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="duration_days">Durée (jours)</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control @error('duration_days') is-invalid @enderror" 
                               id="duration_days" name="duration_days" value="{{ old('duration_days') }}" required>
                        @error('duration_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 form-label">Fonctionnalités</label>
                    <div class="col-sm-10">
                        <div id="features-container">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="features[]" placeholder="Fonctionnalité">
                                <button type="button" class="btn btn-outline-danger remove-feature" disabled>
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-feature">
                            <i class="bx bx-plus"></i> Ajouter une fonctionnalité
                        </button>
                        @error('features')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-10 offset-sm-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Abonnement actif</label>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-end">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">Créer</button>
                        <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary">Annuler</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('add-feature').addEventListener('click', function() {
    const container = document.getElementById('features-container');
    const newFeature = document.createElement('div');
    newFeature.className = 'input-group mb-2';
    newFeature.innerHTML = `
        <input type="text" class="form-control" name="features[]" placeholder="Fonctionnalité">
        <button type="button" class="btn btn-outline-danger remove-feature">
            <i class="bx bx-trash"></i>
        </button>
    `;
    container.appendChild(newFeature);
    updateRemoveButtons();
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-feature')) {
        e.target.closest('.input-group').remove();
        updateRemoveButtons();
    }
});

function updateRemoveButtons() {
    const buttons = document.querySelectorAll('.remove-feature');
    buttons.forEach((btn, index) => {
        btn.disabled = buttons.length === 1;
    });
}
</script>

@endsection