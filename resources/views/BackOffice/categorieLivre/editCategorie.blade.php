@extends('baseB')
@section('content')

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forms/</span> Modifier Catégorie</h4>

        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Modifier la Catégorie</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="name">Nom</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-category"></i></span>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $category->name) }}" required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 form-label" for="description">Description</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-comment"></i></span>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description">{{ old('description', $category->description) }}</textarea>
                                    </div>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @if($category->image)
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Image actuelle</label>
                                <div class="col-sm-10">
                                    <img src="{{ asset('storage/' . $category->image) }}" 
                                         alt="{{ $category->name }}" 
                                         class="rounded" 
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="image">Nouvelle Image</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    <div class="form-text">Laissez vide pour conserver l'image actuelle</div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Modifier</button>
                                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Annuler</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-backdrop fade"></div>
</div>

@endsection