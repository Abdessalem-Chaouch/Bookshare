{{-- Hero Section with Image and Centered Vertical Form --}}
<section id="stores-hero" style="position: relative; height: 80vh; overflow: hidden;">
    <img src="{{ asset('images/store-hero.jpg') }}" 
         alt="Featured Stores" 
         style="width: 100%; height: 100%; object-fit: cover;">

    {{-- Centered Overlay Form --}}
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                background: rgba(255, 255, 255, 0.9); padding: 25px; border-radius: 10px;
                max-width: 400px; width: 85%; text-align: center; font-size: 14px;">
        <h3 class="mb-2">Independent Bookstores</h3>
        <p class="mb-3">
            We work with over 500 independent bookstores and booksellers.
        </p>

        <form action="{{ route('stores') }}" method="GET" class="d-flex flex-column gap-2">
            <input type="text" name="name" class="form-control form-control-sm" 
                placeholder="By Name" value="{{ request('name') }}">
            <input type="text" name="owner_name" class="form-control form-control-sm" 
                placeholder="By Bookseller Name" value="{{ request('owner_name') }}">

            <div class="d-flex gap-2 mt-2">
                <button type="submit" 
                        class="btn btn-primary btn-sm flex-fill d-flex justify-content-center align-items-center"
                        style="height: 32px; padding: 0;">
                    Search
                </button>
                <a href="{{ route('stores') }}" 
                class="btn btn-secondary btn-sm flex-fill d-flex justify-content-center align-items-center"
                style="height: 32px; padding: 0;">
                    Reset
                </a>
            </div>
        </form>

    </div>
</section>

{{-- Featured Stores Grid --}}
<section id="featured-stores" class="py-4 my-4">
    <div class="container">
        <div class="row">
            @if($stores->count() > 0)
                @foreach($stores as $store)
                    <div class="col-md-3 mb-3">
                        <div class="product-item border rounded shadow-sm p-2 h-100">
                            <figure class="product-style">
                                <img src="{{ $store->store_image ? asset('storage/'.$store->store_image) : asset('images/store-placeholder.png') }}" 
                                    alt="{{ $store->store_name }}" 
                                    class="img-fluid w-100 mb-2" 
                                    style="height: 180px; object-fit: cover;">
                                <a href="{{ route('stores.show', $store->id) }}" 
                                   class="btn btn-primary w-100 btn-sm">
                                    Visit Store
                                </a>
                            </figure>
                            <figcaption class="mt-2">
                                <h6>{{ $store->store_name }}</h6>
                                <p class="mb-1">Owner: {{ $store->owner_name }}</p>
                                <p class="mb-1">Location: {{ $store->location }}</p>
                                <p class="mb-0">Contact: {{ $store->contact }}</p>
                            </figcaption>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-md-12 text-center py-5">
                    <h5 class="text-muted">No stores found matching your search.</h5>
                </div>
            @endif
        </div>
    </div>
</section>
