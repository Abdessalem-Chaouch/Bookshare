@extends('baseF')
@section('content')

<section id="categories-page" class="bookshelf pb-5 mb-5" style="padding-top: 100px;">
    <div class="section-header align-center">
        <div class="title">
            <span>Toutes nos</span>
        </div>
        <h2 class="section-title">Catégories de Livres</h2>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <button class="prev-cat" style="position: absolute; left: 20px; top: 50%; z-index: 10; background: rgba(0,0,0,0.5); color: white; border: none; padding: 15px; border-radius: 50%; cursor: pointer;">
                    <i class="icon icon-arrow-left"></i>
                </button>
                
                <div class="categories-slider" style="overflow: hidden;">
                    <div class="categories-track" style="display: flex; transition: transform 0.3s ease;">
                        @foreach($categories as $category)
                        <div class="category-slide" style="min-width: 300px; margin: 0 15px;">
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
                                    <button type="button" class="add-to-cart" data-product-tile="view-category">
                                        Voir Catégorie
                                    </button>
                                </figure>
                                <figcaption>
                                    <h3>{{ $category->name }}</h3>
                                </figcaption>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <button class="next-cat" style="position: absolute; right: 20px; top: 50%; z-index: 10; background: rgba(0,0,0,0.5); color: white; border: none; padding: 15px; border-radius: 50%; cursor: pointer;">
                    <i class="icon icon-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<style>
.product-item figure:hover .category-overlay {
    opacity: 1 !important;
}
.product-item figure:hover img {
    transform: scale(1.05);
}
.categories-slider {
    position: relative;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.querySelector('.categories-track');
    const prevBtn = document.querySelector('.prev-cat');
    const nextBtn = document.querySelector('.next-cat');
    const slideWidth = 330; // 300px + 30px margin
    let currentPosition = 0;
    const maxSlides = {{ count($categories) }};
    const visibleSlides = Math.floor(window.innerWidth / slideWidth);
    const maxPosition = -(maxSlides - visibleSlides) * slideWidth;

    nextBtn.addEventListener('click', function() {
        if (currentPosition > maxPosition) {
            currentPosition -= slideWidth;
            track.style.transform = `translateX(${currentPosition}px)`;
        }
    });

    prevBtn.addEventListener('click', function() {
        if (currentPosition < 0) {
            currentPosition += slideWidth;
            track.style.transform = `translateX(${currentPosition}px)`;
        }
    });
});
</script>

@endsection