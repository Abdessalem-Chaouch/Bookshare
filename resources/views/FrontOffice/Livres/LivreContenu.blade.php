<section id="featured-books" class="py-5 my-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="section-header align-center">
                    <div class="title">
                        <span>Some quality items</span>
                    </div>
                    <h2 class="section-title"> Books</h2>
                </div>

                <div class="product-list" data-aos="fade-up">
                    <div class="row">


                            @foreach($livres as $livre)
                                <div class="col-md-3" >
                                    <div   class="product-item">
                                        <figure class="product-style">
                                            <img src="{{ asset('storage/' . $livre->photo_couverture) }}" alt="{{ $livre->titre }}" class="livre-image">
                                            <button type="button" class="add-to-cart" data-product-id="{{ $livre->id }}">
                                                Add to Cart
                                            </button>
                                        </figure>
                                        <figcaption>
                                            <h3><a href="{{ route('livres.showf', $livre->id) }}"> {{ $livre->titre }}</a></h3>
                                            <span>{{ $livre->auteur }}</span>
                                            <p><strong>Prix :</strong> {{ $livre->prix ? $livre->prix . ' DT' : 'Non spécifié' }}</p>

                                        </figcaption>
                                    </div>
                                </div>
                            @endforeach


                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<style>
    .livre-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        display: block;
    }
</style>
