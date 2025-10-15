@extends('baseF')
@section('content')

<section id="category-books" class="bookshelf pb-5 mb-5" style="padding-top: 100px;">
    <div class="section-header align-center">
        <div class="title">
            <span>Books in</span>
        </div>
        <h2 class="section-title">{{ $category->name }}</h2>
        <p class="text-muted">{{ $category->description }}</p>
    </div>

    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('accueil') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('front.categories') }}">Categories</a></li>
                        <li class="breadcrumb-item active">{{ $category->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if($livres->count() > 0)
            <div class="row">
                @foreach($livres as $livre)
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="product-item">
                        <figure class="product-style">
                            <img src="{{ $livre->photo_couverture ? asset('storage/' . $livre->photo_couverture) : asset('images/product-item1.jpg') }}" 
                                 alt="{{ $livre->titre }}" class="product-item">
                            <button type="button" class="add-to-cart" onclick="window.location.href='{{ route('livres.showf', $livre->id) }}'">
                                View Book
                            </button>
                        </figure>
                        <figcaption>
                            <h3><a href="{{ route('livres.showf', $livre->id) }}">{{ $livre->titre }}</a></h3>
                            <span>by {{ $livre->user->name ?? 'Unknown Author' }}</span>
                            @if($livre->prix)
                                <div class="item-price">{{ number_format($livre->prix, 2) }} €</div>
                            @endif
                            <div class="rating">
                                @php
                                    $rating = $livre->averageRating() ?? 0;
                                    $fullStars = floor($rating);
                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $fullStars)
                                        <i class="fas fa-star text-warning"></i>
                                    @elseif($i == $fullStars + 1 && $hasHalfStar)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                                <span class="ms-1">({{ number_format($rating, 1) }})</span>
                            </div>
                        </figcaption>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="row">
                <div class="col-12 text-center py-5">
                    <div class="no-books">
                        <i class="bi bi-book" style="font-size: 3rem; color: #ccc;"></i>
                        <h4 class="mt-3">No books found in this category</h4>
                        <p class="text-muted">This category doesn't have any books yet.</p>
                        <a href="{{ route('front.categories') }}" class="btn btn-primary mt-2">
                            Browse other categories
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<style>
.breadcrumb {
    background: transparent;
    padding: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
}

.product-item {
    transition: transform 0.3s ease;
}

.product-item:hover {
    transform: translateY(-5px);
}

.rating {
    margin-top: 10px;
    font-size: 14px;
}

.item-price {
    font-weight: bold;
    color: #d2ceaf;
    font-size: 18px;
    margin-top: 5px;
}

.no-books {
    padding: 60px 20px;
}
</style>

@endsection