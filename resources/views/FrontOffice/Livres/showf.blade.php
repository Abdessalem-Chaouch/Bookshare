@extends('baseF')

@section('content')
<section id="best-selling" class="leaf-pattern-overlay py-5">
    <div class="corner-pattern-overlay"></div>
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="row">

                    {{-- Image du livre --}}
                    <div class="col-md-6">
                        <figure class="products-thumb">
                            @if($livre->photo_couverture)
                                <img src="{{ asset('storage/' . $livre->photo_couverture) }}" alt="{{ $livre->titre }}" class="single-image">
                            @else
                                <img src="{{ asset('images/default-book.jpg') }}" alt="No Image" class="single-image">
                            @endif
                        </figure>
                    </div>

                    {{-- Détails du livre --}}
                    <div class="col-md-6 position-relative">
                        <div class="product-entry">
                            <h2 class="section-title divider">{{ $livre->titre }}</h2>

                            <div class="products-content">
                                <div class="author-name">By {{ $livre->auteur ?? 'Unknown' }}</div>
                                <p>{{ $livre->description ?? 'No description available.' }}</p>
                                <div class="item-price">${{ number_format($livre->prix, 2) }}</div>
                                <div class="item-stock">
                                    @if($livre->disponibilite == 'disponible')
                                        <span class="badge bg-success">Available</span>
                                    @elseif($livre->disponibilite == 'emprunte')
                                        <span class="badge bg-warning">Borrowed</span>
                                    @else
                                        <span class="badge bg-secondary">Reserved</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Boutons en bas à droite --}}
                            <div class="action-buttons">
                             
                          <form action="{{ route('borrows.pay', $livre->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary" 
                                    style="background: #9c9259; color: white; cursor: pointer;">
                                Borrow $5
                            </button>
                        </form>


                        <form action="{{ route('paypal') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="items[0][livre_id]" value="{{ $livre->id }}">
                            <input type="hidden" name="items[0][product_name]" value="{{ $livre->titre }}">
                            <input type="hidden" name="items[0][amount]" value="{{ $livre->prix }}">
                            
                            <button type="submit" class="btn btn-outline-success">Buy</button>
                        </form>

                            </div>

                        </div>
                    </div>

                </div>
                <!-- / row -->

            </div>

        </div>
    </div>
</section>

<style>
.single-image {
    width: 100%;
    height: 400px;
    object-fit: cover;
    display: block;
}
.products-content .item-stock {
    margin-top: 10px;
}
.action-buttons {
    position: absolute;
    bottom: 10px;
    right: 10px;
    display: flex;
    gap: 10px;
}
</style>
@endsection
