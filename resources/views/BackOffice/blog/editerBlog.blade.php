@extends('baseB')
@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forms /</span> Modifier blog </h4>

        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Modifier le blog</h5>
                        <small class="text-muted float-end">Formulaire d'édition</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Titre -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="title">Titre</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-book"></i></span>
                                        <input
                                            type="text"
                                            name="title"
                                            class="form-control @error('title') is-invalid @enderror"
                                            id="title"
                                            placeholder="Titre du blog"
                                            value="{{ old('title', $blog->title) }}">
                                    </div>
                                    @error('title')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contenu -->
                            <div class="row mb-3">
                                <label class="col-sm-2 form-label" for="content">Contenu</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-comment"></i></span>
                                        <textarea
                                            name="content"
                                            class="form-control @error('content') is-invalid @enderror"
                                            id="content"
                                            placeholder="Écrivez le contenu du blog..."
                                            rows="5">{{ old('content', $blog->content) }}</textarea>
                                    </div>
                                    @error('content')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Catégorie -->
                            <div class="row mb-3">
                                <label class="col-sm-2 form-label" for="category_id">Catégorie</label>
                                <div class="col-sm-10">
                                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" id="category_id">
                                        <option value="">-- Sélectionner une catégorie --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ (old('category_id', $blog->category_id) == $category->id) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Image -->
                            <div class="row mb-3">
                                <label class="col-sm-2 form-label" for="image">Image</label>
                                <div class="col-sm-10">
                                    @if($blog->image)
                                        <img src="{{ asset('uploads/' . $blog->image) }}" alt="Image blog" class="rounded mb-2" width="100">
                                    @endif
                                    <input type="file" name="image" class="form-control" id="image">
                                    @error('image')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Bouton -->
                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                                    <a href="{{ route('listeBlog') }}" class="btn btn-secondary">Annuler</a>
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
