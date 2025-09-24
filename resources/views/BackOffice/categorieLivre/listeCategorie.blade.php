@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold"><span class="text-muted fw-light">Tables /</span> Liste des Catégories</h4>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>Ajouter Catégorie
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">Catégories</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($categories as $category)
                    <tr>
                        <td>
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" 
                                     alt="{{ $category->name }}" 
                                     class="rounded" 
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <i class="bx bx-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>{{ Str::limit($category->description, 50) }}</td>
                        <td>{{ $category->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('categories.edit', $category) }}">
                                        <i class="bx bx-edit-alt me-1"></i> Modifier
                                    </a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')" 
                                          style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bx bx-category bx-lg mb-2"></i>
                                <p>Aucune catégorie trouvée</p>
                                <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                                    Ajouter la première catégorie
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection