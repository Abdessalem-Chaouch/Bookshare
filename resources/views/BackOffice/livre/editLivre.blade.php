@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Livres /</span> Éditer Livre</h4>

    {{-- Messages d'erreurs globaux --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('livres.update', $livre->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Titre --}}
                <div class="mb-3">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre"
                           class="form-control @error('titre') is-invalid @enderror"
                           required value="{{ old('titre', $livre->titre) }}">
                    @error('titre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Auteur --}}
                <div class="mb-3">
                    <label class="form-label">Auteur</label>
                    <input type="text" name="auteur"
                           class="form-control @error('auteur') is-invalid @enderror"
                           value="{{ old('auteur', $livre->auteur) }}">
                    @error('auteur')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $livre->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ISBN --}}
                <div class="mb-3">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn"
                           class="form-control @error('isbn') is-invalid @enderror"
                           value="{{ old('isbn', $livre->isbn) }}">
                    @error('isbn')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Catégorie --}}
                <div class="mb-3">
                    <label class="form-label">Catégorie</label>
                    <select name="categorie_id" class="form-select @error('categorie_id') is-invalid @enderror">
                        <option value="">-- Choisir une catégorie --</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}" {{ old('categorie_id', $livre->categorie_id) == $categorie->id ? 'selected' : '' }}>
                                {{ $categorie->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Prix --}}
                <div class="mb-3">
                    <label class="form-label">Prix (DT)</label>
                    <input type="number" step="0.01" min="0" name="prix"
                           class="form-control @error('prix') is-invalid @enderror"
                           value="{{ old('prix', $livre->prix) }}">
                    @error('prix')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Disponibilité --}}
                <div class="mb-3">
                    <label class="form-label">Disponibilité</label>
                    <select name="disponibilite" class="form-select @error('disponibilite') is-invalid @enderror" required>
                        <option value="disponible" {{ old('disponibilite', $livre->disponibilite)=='disponible'?'selected':'' }}>Disponible</option>
                        <option value="emprunte" {{ old('disponibilite', $livre->disponibilite)=='emprunte'?'selected':'' }}>Emprunté</option>
                        <option value="reserve" {{ old('disponibilite', $livre->disponibilite)=='reserve'?'selected':'' }}>Réservé</option>
                    </select>
                    @error('disponibilite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Stock --}}
                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" min="0" required
                           class="form-control @error('stock') is-invalid @enderror"
                           value="{{ old('stock', $livre->stock) }}">
                    @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Photo de couverture --}}
                <div class="mb-3">
                    <label class="form-label">Photo de couverture</label>
                    <input type="file" name="photo_couverture"
                           class="form-control @error('photo_couverture') is-invalid @enderror" accept="image/*">
                    @error('photo_couverture')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($livre->photo_couverture)
                        <img src="{{ asset('storage/'.$livre->photo_couverture) }}" width="100" class="mt-2 rounded">
                    @endif
                </div>

                {{-- PDF --}}
                <div class="mb-3">
                    <label class="form-label">Fichier PDF</label>
                    <input type="file" name="pdf_contenu"
                           class="form-control @error('pdf_contenu') is-invalid @enderror" accept="application/pdf">
                    @error('pdf_contenu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($livre->pdf_contenu && Storage::disk('public')->exists($livre->pdf_contenu))
                        <div class="mt-2">
                            <a href="{{ route('livres.download', $livre->id) }}" class="btn btn-sm btn-primary">📥 Télécharger le PDF actuel</a>
                        </div>
                    @endif
                </div>

                {{-- Boutons --}}
                <button type="submit" class="btn btn-success">Mettre à jour</button>
                <a href="{{ route('livres.index') }}" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>
</div>

@endsection
