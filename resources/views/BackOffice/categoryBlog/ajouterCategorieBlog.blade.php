@extends('baseB')
@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forms /</span> Ajouter Categorie Blog</h4>

        <!-- Basic Layout & Basic with Icons -->
        <div class="row">
            <!-- Basic with Icons -->
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Nouvelle Categorie Blog</h5>
                        <small class="text-muted float-end">Formulaire de création</small>
                    </div>
                    <div class="card-body">

                        <!-- Formulaire -->
                        <form action="{{ route('categoryBlog.store') }}" method="POST">
                            @csrf

                            <!-- Nom -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Nom</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-book"></i></span>
                                        <input
                                            type="text"
                                            name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Nom de la categorie"
                                            value="{{ old('name') }}">
                                    </div>
                                    @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="row mb-3">
                                <label class="col-sm-2 form-label">Description</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-comment"></i></span>
                                        <textarea
                                            name="description"
                                            class="form-control @error('description') is-invalid @enderror"
                                            placeholder="Écrivez la description..."
                                            rows="5">{{ old('description') }}</textarea>
                                    </div>
                                    @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Bouton -->
                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    <a href="{{ route('categoryBlog.index') }}" class="btn btn-secondary">Annuler</a>
                                </div>
                            </div>

                        </form>
                        <!-- /Formulaire -->

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
    <div class="content-backdrop fade"></div>
</div>

@endsection
