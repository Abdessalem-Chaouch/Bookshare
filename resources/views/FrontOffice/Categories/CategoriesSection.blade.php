<section id="categories-section" class="bookshelf pb-5 mb-5">
    <div class="section-header align-center">
        <div class="title">
            <span>Discover Our</span>
        </div>
        <h2 class="section-title">Book Categories</h2>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12" style="position: relative;">
                
                <div class="home-categories-slider" style="overflow: hidden; position: relative;">
                    <button class="prev-home-cat" style="position: absolute; left: 10px; top: 50%; z-index: 10; background: rgba(0,0,0,0.7); color: white; border: none; padding: 15px; border-radius: 50%; cursor: pointer; transform: translateY(-50%);">
                        <i class="icon icon-arrow-left"></i>
                    </button>
                    
                    <div class="home-categories-track" style="display: flex; transition: transform 0.3s ease;">
                        @foreach($categories ?? [] as $category)
                        <div class="home-category-slide" style="min-width: 300px; margin: 0 15px;">
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
                    
                    <button class="next-home-cat" style="position: absolute; right: 10px; top: 50%; z-index: 10; background: rgba(0,0,0,0.7); color: white; border: none; padding: 15px; border-radius: 50%; cursor: pointer; transform: translateY(-50%);">
                        <i class="icon icon-arrow-right"></i>
                    </button>
                </div>
                
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
.home-categories-slider {
    position: relative;
}
</style>

@if(isset($categories) && count($categories) > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const homeTrack = document.querySelector('.home-categories-track');
    const homePrevBtn = document.querySelector('.prev-home-cat');
    const homeNextBtn = document.querySelector('.next-home-cat');
    const slideWidth = 330;
    let homeCurrentPosition = 0;
    const homeMaxSlides = {{ count($categories ?? []) }};
    const homeVisibleSlides = Math.floor(window.innerWidth / slideWidth);
    const homeMaxPosition = -(homeMaxSlides - homeVisibleSlides) * slideWidth;

    homeNextBtn.addEventListener('click', function() {
        if (homeCurrentPosition > homeMaxPosition) {
            homeCurrentPosition -= slideWidth;
            homeTrack.style.transform = `translateX(${homeCurrentPosition}px)`;
        }
    });

    homePrevBtn.addEventListener('click', function() {
        if (homeCurrentPosition < 0) {
            homeCurrentPosition += slideWidth;
            homeTrack.style.transform = `translateX(${homeCurrentPosition}px)`;
        }
    });
});
</script>
@endif