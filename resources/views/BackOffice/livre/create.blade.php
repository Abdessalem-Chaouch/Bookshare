@extends('baseB')
@section('content')

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Livres /</span> Ajouter Livre</h4>

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('livres.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Titre</label>
                        <input type="text" name="titre" class="form-control" required value="{{ old('titre') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Auteur</label>
                        <input type="text" name="auteur" class="form-control" value="{{ old('auteur') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ISBN</label>
                        <input type="text" name="isbn" class="form-control" value="{{ old('isbn') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catégorie</label>
                        <select name="categorie_id" class="form-select">
                            <option value="">-- Choisir une catégorie --</option>
                            @foreach($categories as $categorie)
                                <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                    {{ $categorie->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Disponibilité</label>
                        <select name="disponibilite" class="form-select" required>
                            <option value="disponible" {{ old('disponibilite')=='disponible'?'selected':'' }}>Disponible</option>
                            <option value="emprunte" {{ old('disponibilite')=='emprunte'?'selected':'' }}>Emprunté</option>
                            <option value="reserve" {{ old('disponibilite')=='reserve'?'selected':'' }}>Réservé</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control" min="0" required value="{{ old('stock') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Photo de couverture</label>
                        <input type="file" name="photo_couverture" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fichier PDF</label>
                        <input type="file" name="pdf_contenu" class="form-control" accept="application/pdf">
                    </div>

                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('livres.index') }}" class="btn btn-secondary">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
