<section id="featured-stores" class="py-5 my-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="section-header align-center">
                    <div class="title">
                        <span>Some quality stores</span>
                    </div>
                    <h2 class="section-title">Featured Stores</h2>
                </div>

                <div class="product-list" data-aos="fade-up">
                    <div class="row">
                        @foreach($stores as $store)
                            <div class="col-md-3">
                                <div class="product-item">
                                    <figure class="product-style">
                                        <img src="{{ asset('images/store-placeholder.png') }}" 
                                            alt="{{ $store->store_name }}" 
                                            class="product-item">
                                        <a href="{{ route('stores.show', $store->id) }}" class="btn btn-primary">
                                            Visit Store
                                        </a>
                                    </figure>
                                    <figcaption>
                                        <h3>{{ $store->store_name }}</h3>
                                        <span>Owner: {{ $store->owner_name }}</span>
                                        <div class="item-price">{{ $store->location }}</div>
                                        <div class="item-price">{{ $store->contact }}</div>
                                    </figcaption>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div><!-- grid -->

            </div><!-- inner-content -->
        </div>
    </div>
</section>
