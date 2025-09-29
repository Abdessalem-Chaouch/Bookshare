@extends('baseF')

@section('content')
<section id="book-details" class="leaf-pattern-overlay py-5">
    <div class="corner-pattern-overlay"></div>
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-11">

                <div class="row">

                    {{-- Book Image --}}
                    <div class="col-md-6">
                        <figure class="products-thumb">
                            @if($livre->photo_couverture)
                                <img src="{{ asset('storage/' . $livre->photo_couverture) }}"
                                     alt="{{ $livre->titre }}" class="single-image">
                            @else
                                <img src="{{ asset('images/default-book.jpg') }}"
                                     alt="No Image" class="single-image">
                            @endif
                        </figure>
                    </div>


                    {{-- Book Details --}}
                    <div class="col-md-6">
                        <div class="product-entry d-flex align-items-center justify-content-between">
                            <h2 class="section-title divider">{{ $livre->titre }}</h2>

                          @auth
                            <!-- Rate Button -->
                            <button type="button" class="btn btn-rate" data-bs-toggle="modal" data-bs-target="#rateModal">
                                ⭐ Rate
                            </button>
                            @endauth
                        </div>

                        <div class="products-content mt-3">
                           <p><strong>Author:</strong> {{ $livre->user ? $livre->user->name : 'Auteur inconnu' }}</p>

                            <p><strong>Category:</strong> {{ $livre->categorie?->name ?? '—' }}</p>
                            <p><strong>Description:</strong> {{ $livre->description ?? 'No description available.' }}</p>
                            <p><strong>ISBN:</strong> {{ $livre->isbn ?? '—' }}</p>
                            <p><strong>Price:</strong> ${{ number_format($livre->prix, 2) }}</p>
                            <p><strong>Stock:</strong> {{ $livre->stock }}</p>
                            <p><strong>Availability:</strong>
                                @if($livre->stock == '0'|| $livre->stock == 0 )
                                    <span class="badge bg-warning">Unavailable</span>
                                @else
                                    <span class="badge bg-success">Available</span>
                               
                                @endif
                            </p>
                            <p><strong>Date Added:</strong> {{ $livre->date_ajout ?? '—' }}</p>
                              {{-- PDF --}}
                            @if($livre->pdf_contenu && Storage::disk('public')->exists($livre->pdf_contenu))
                              <!--  <div class="mt-3">
                                    <strong>PDF:</strong>
                                    <a href="{{ route('livres.download', $livre->id) }}" class="btn btn-primary btn-sm">
                                        📥 Download PDF
                                    </a>
                                </div>
                               -->
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

                            @else
                                <p><em>No PDF available for this book.</em></p>
                            @endif
    {{-- Average Rating --}}
    <div class="mt-3 mb-3">
        @php $average = $livre->averageRating(); @endphp
        <p><strong>Average Rating:</strong>
            @if($average)
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= round($average))
                        <span style="color: gold;">★</span>
                    @else
                        <span style="color: #ccc;">★</span>
                    @endif
                @endfor
                ({{ number_format($average, 1) }} / 5)
            @else
                No rating yet.
            @endif
        </p>
    </div>

    {{-- Display all ratings --}}
<div class="mt-3">
   <strong>Ratings & Comments : </strong>
     <!-- Show Reviews Button -->
        @if($livre->rates->count() > 0)
        <button type="button" class="btn btn-info ms-2" data-bs-toggle="modal" data-bs-target="#reviewsModal">
            SHOW REVIEWS
        </button>
        @endif
</div>



                        </div>
                    </div>
                </div>
                <!-- / row -->

                <div class="mt-4">
                    <a href="{{ route('livresf') }}" class="btn btn-secondary btn-sm">Back to list</a>
                </div>

            </div>
        </div>
    </div>
</section>
{{-- Reviews Modal --}}
@if($livre->rates->count() > 0)
<div class="modal fade" id="reviewsModal" tabindex="-1" aria-labelledby="reviewsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content p-3">
      <div class="modal-header border-0">
        <strong class="modal-title" id="reviewsModalLabel">Reviews for "{{ $livre->titre }}"</strong>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @foreach($livre->rates as $rate)
            <div class="review-card">
                <p><strong>{{ $rate->user->name }}</strong>
                    - @for($i = 1; $i <= 5; $i++)
                        @if($i <= $rate->note)
                            <span style="color: gold;">★</span>
                        @else
                            <span style="color: #ccc;">★</span>
                        @endif
                    @endfor
                </p>
                <p>{{ $rate->commentaire ?? 'No comment' }}</p>
                <small class="text-muted">{{ $rate->created_at->format('d M Y') }}</small>
            </div>
        @endforeach
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary btn-contained" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endif


{{-- Rate Modal --}}
@auth
<div class="modal fade" id="rateModal" tabindex="-1" aria-labelledby="rateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('rates.store', $livre->id) }}" method="POST">
        @csrf
        <div class="modal-header">
          <strong class="modal-title" id="rateModalLabel">Rate " {{ $livre->titre }} "</strong>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="note" class="form-label">Rating:</label>
            <div class="rating-stars">
              @for($i = 5; $i >= 1; $i--)
                  <input type="radio" id="star{{ $i }}" name="note" value="{{ $i }}" required />
                  <label for="star{{ $i }}">★</label>
              @endfor
            </div>
          </div>
          <div class="mb-3">
    <label for="commentaire" class="form-label">Comment:</label>
    <textarea name="commentaire" class="form-control textarea-shadow" id="commentaire" rows="3"></textarea>
</div>

        </div>
        <div class="modal-footer">
          <!-- MUI Contained style -->
          <button type="submit" class="btn btn-primary btn-contained">Submit</button>
          <button type="button" class="btn btn-secondary btn-contained" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>

.btn-info {
    background-color: #c3763cff;
    color: white;
    font-weight: 500;
    border: none;
    padding: 6px 16px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.modal-content {
    border-radius: 16px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    padding: 10px;
    background-color: #dfd0c2; /* NEW background color */
}

.review-card {
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    padding: 15px;
    margin-bottom: 12px;
    transition: transform 0.2s ease; background-color: #ffffffff;
}
.review-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.25);
}

.btn-info:hover {
    background-color: #935411ff;
}


    .textarea-shadow {
    border-radius: 12px;          /* Coins arrondis */
    box-shadow: 0px 3px 5px rgba(0,0,0,0.2); /* Ombre */
    border: 1px solid #ced4da;    /* Bordure légère */
    padding: 10px;
    transition: box-shadow 0.3s ease, border-color 0.3s ease;
}

.textarea-shadow:focus {
    outline: none;
    border-color: #935411ff;        /* Couleur focus similaire MUI */
    box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.2);
}

.btn-contained {
    background-color: #c3763cff; /* MUI primary */
    color: white;
    box-shadow: 0px 3px 1px -2px rgba(0,0,0,0.2),
                0px 2px 2px 0px rgba(0,0,0,0.14),
                0px 1px 5px 0px rgba(0,0,0,0.12);
    border: none;border-radius: 12px;
    transition: background-color 0.3s ease;
}
.btn-contained:hover {
    background-color: #935411ff;
    color: white;
}
</style>
@endauth


<style>
.products-content p {
    margin-bottom: 8px;
}
/* Button like MUI contained */
.btn-rate {
    background-color: #c3763cff;
    color: white;
    font-weight: 500;
    border: none;
    padding: 6px 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.btn-rate:hover {
    background-color: #935411ff;color: white;
}
/* Stars rating */
.rating-stars {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    font-size: 2rem;
}
.rating-stars input[type="radio"] {
    display: none;
}
.rating-stars label {
    color: white; /* avant c'était #ccc */
    cursor: pointer;
    transition: color 0.2s, transform 0.2s;
}

.rating-stars input[type="radio"]:checked ~ label,
.rating-stars label:hover,
.rating-stars label:hover ~ label {
    color: #ffb400; /* couleur des étoiles sélectionnées */
}
</style>
@endsection