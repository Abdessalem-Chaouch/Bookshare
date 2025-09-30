@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold"><span class="text-muted fw-light">Tables /</span> Categories List</h4>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>Add Category
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Recherche avancée -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('categories.index') }}" class="search-form" id="searchForm">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <div class="search-input-wrapper position-relative">
                            <i class="bx bx-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search for a category..." 
                                   class="form-control ps-5" id="searchInput">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="search_type" class="form-select">
                            <option value="name" {{ request('search_type', 'name') == 'name' ? 'selected' : '' }}>📖 Name</option>
                            <option value="description" {{ request('search_type') == 'description' ? 'selected' : '' }}>📝 Description</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="sort" class="form-select">
                            <option value="asc" {{ request('sort', 'asc') == 'asc' ? 'selected' : '' }}> A-Z</option>
                            <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}> Z-A</option>
                        </select>
                    </div>
                </div>
                @if(request()->hasAny(['search', 'sort']))
                    <div class="mt-3 text-center">
                        <span class="badge bg-primary">{{ $categories->count() }} result(s)</span>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Categories</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Creation Date</th>
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
                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this category?')" 
                                          style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bx bx-trash me-1"></i> Delete
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
                                <p>No categories found</p>
                                <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                                    Add first category
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

<script>
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        if (this.value === '') {
            window.location.href = '{{ route("categories.index") }}';
        } else {
            document.getElementById('searchForm').submit();
        }
    }, 500);
});

document.querySelector('select[name="sort"]').addEventListener('change', function() {
    document.getElementById('searchForm').submit();
});

document.querySelector('select[name="search_type"]').addEventListener('change', function() {
    document.getElementById('searchForm').submit();
});
</script>

@endsection