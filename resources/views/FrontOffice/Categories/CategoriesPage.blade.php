@extends('baseF')
@section('content')

<section id="categories-page" class="bookshelf pb-5 mb-5" style="padding-top: 100px;">
    <div class="section-header align-center">
        <div class="title">
            <span>All Our</span>
        </div>
        <h2 class="section-title">Book Categories</h2>
    </div>

    <!-- Recherche moderne -->
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="search-container">
                    <form method="GET" action="{{ route('front.categories') }}" class="search-form" id="searchForm">
                        <div class="search-input-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search for a category..." class="search-input" id="searchInput">
                        </div>
                        <div class="filters-wrapper">
                            <select name="search_type" class="filter-select">
                                <option value="name" {{ request('search_type', 'name') == 'name' ? 'selected' : '' }}>📖 Name</option>
                                <option value="description" {{ request('search_type') == 'description' ? 'selected' : '' }}>📝 Description</option>
                            </select>
                            <select name="sort" class="filter-select">
                                <option value="asc" {{ request('sort', 'asc') == 'asc' ? 'selected' : '' }}>🔤 A-Z</option>
                                <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>🔤 Z-A</option>
                            </select>
                        </div>
                    </form>
                    @if(request()->hasAny(['search', 'sort']))
                        <div class="results-count">
                            <span class="count-badge">{{ $categories->count() }} result(s)</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        @if($categories->count() > 0)
            <div class="row">
                @foreach($categories as $category)
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="product-item">
                    <figure class="product-style" style="position: relative; overflow: hidden;">
                        <img src="{{ $category->image ? asset('storage/' . $category->image) : asset('images/product-item1.jpg') }}" 
                             alt="{{ $category->name }}" class="product-item" style="width: 100%; height: 300px; object-fit: cover; transition: all 0.3s ease;">
                        <div class="category-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); color: white; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; text-align: center; padding: 20px;">
                            <div>
                                <h4 style="margin-bottom: 10px; font-size: 1.2em;">{{ $category->name }}</h4>
                                <p style="font-size: 0.9em;">{{ $category->description }}</p>
                            </div>
                        </div>
                        <button type="button" class="add-to-cart" onclick="window.location.href='{{ route('category.books', $category->id) }}'">
                            View Category
                        </button>
                    </figure>
                    <figcaption>
                        <h3>{{ $category->name }}</h3>
                    </figcaption>
                </div>
            </div>
                @endforeach
            </div>
        @else
            <div class="row">
                <div class="col-12 text-center py-5">
                    <div class="no-results">
                        <i class="bi bi-search" style="font-size: 3rem; color: #ccc;"></i>
                        <h4 class="mt-3">No categories found</h4>
                        <p class="text-muted">Try modifying your search criteria.</p>
                        <a href="{{ route('front.categories') }}" class="btn btn-primary mt-2">
                            View all categories
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<style>
.product-item figure:hover .category-overlay {
    opacity: 1 !important;
}
.product-item figure:hover img {
    transform: scale(1.05);
}

/* Design pour la recherche */
.search-container {
    background: linear-gradient(135deg, #dccdb6 0%, #9d7d42 100%);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.search-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.search-input-wrapper {
    position: relative;
    flex: 1;
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #d2ceaf;
    font-size: 18px;
}

.search-input {
    width: 100%;
    padding: 15px 15px 15px 50px;
    border: none;
    border-radius: 50px;
    font-size: 16px;
    background: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.filters-wrapper {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.filter-select {
    padding: 12px 20px;
    border: none;
    border-radius: 25px;
    background: rgba(227, 211, 161, 0.9);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 120px;
}

.filter-select:hover {
    background: rgb(240, 230, 192);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.filter-select:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
}

.results-count {
    text-align: center;
    margin-top: 15px;
}

.count-badge {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    backdrop-filter: blur(10px);
}

@media (min-width: 768px) {
    .search-form {
        flex-direction: row;
        align-items: center;
    }
    
    .search-input-wrapper {
        flex: 2;
    }
    
    .filters-wrapper {
        flex: 1;
        justify-content: flex-end;
    }
}
</style>

<script>
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('searchForm').submit();
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