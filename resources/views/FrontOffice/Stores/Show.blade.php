@extends('baseF')

@section('content')
<section class="store-details py-5">
    <div class="container">
        <div class="row align-items-start">
            <!-- Store Image (Left) -->
            <div class="col-md-6 mb-3">
                <img src="{{ $store->store_image ? asset('storage/'.$store->store_image) : asset('images/store-placeholder.png') }}" 
                     alt="{{ $store->store_name }}" class="img-fluid rounded shadow-sm w-100" style="object-fit: cover; max-height: 400px;">
            </div>

            <!-- Store Details (Right) -->
            <div class="col-md-6">
                <h2 class="mb-3">{{ $store->store_name }}</h2>
                <p><strong>Owner:</strong> {{ $store->owner_name ?? 'N/A' }}</p>
                <p><strong>Location:</strong> {{ $store->location }}</p>
                <p><strong>Contact:</strong> {{ $store->contact ?? 'N/A' }}</p>

                {{-- Future place for books --}}
                {{-- <h5 class="mt-4">Available Books:</h5>
                <ul>
                    @foreach($store->books as $book)
                        <li>{{ $book->name }} — {{ $book->quantity }} in stock</li>
                    @endforeach
                </ul> --}}
            </div>
        </div>

        {{-- Review Form --}}
        <div class="row mt-5">
            <div class="col-md-8 offset-md-2">
                <h4>Leave a Review</h4>
                <form action="{{ route('reviews.store', $store->id) }}" method="POST">
                    @csrf

                    {{-- Star Rating --}}
                    <div class="star-rating mb-3">
                        <input type="hidden" name="rating" id="rating" value="0">
                        <span class="star" data-value="1">&#9733;</span>
                        <span class="star" data-value="2">&#9733;</span>
                        <span class="star" data-value="3">&#9733;</span>
                        <span class="star" data-value="4">&#9733;</span>
                        <span class="star" data-value="5">&#9733;</span>
                    </div>

                    {{-- Comment --}}
                    <div class="mb-3">
                        <textarea name="comment" class="form-control" rows="3" placeholder="Write your review..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        </div>

        {{-- Existing Reviews --}}
        <div class="row mt-4">
            <div class="col-md-8 offset-md-2">
                <h4>Reviews</h4>
                @forelse($store->reviews as $review)
                    <div class="border rounded p-3 mb-2">
                        <div>
                            {{-- Show stars --}}
                            @for ($i = 1; $i <= 5; $i++)
                                <span style="color: {{ $i <= $review->rating ? '#ffc107' : '#e4e5e9' }};">&#9733;</span>
                            @endfor
                        </div>
                        <p class="mb-1">{{ $review->comment }}</p>
                        <small class="text-muted">
                            By {{ $review->user ? $review->user->name : 'Guest' }}
                        </small>

                        @if(auth()->check() && auth()->id() == $review->user_id)
                            <div class="mt-2">
                                {{-- Edit button --}}
                                <button class="btn btn-sm btn-secondary" onclick="toggleEditForm({{ $review->id }})">Edit</button>

                                {{-- Delete button --}}
                                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </div>

                            {{-- Hidden edit form --}}
                            <form id="edit-form-{{ $review->id }}" action="{{ route('reviews.update', $review->id) }}" method="POST" class="mt-2" style="display:none;">
                                @csrf
                                @method('PUT')
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="number" name="rating" min="1" max="5" value="{{ $review->rating }}" class="form-control form-control-sm" style="width:80px;">
                                    <input type="text" name="comment" value="{{ $review->comment }}" class="form-control form-control-sm">
                                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                </div>
                            </form>
                        @endif
                    </div>
                @empty
                    <p>No reviews yet. Be the first!</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

<script>
    function toggleEditForm(id) {
        const form = document.getElementById('edit-form-' + id);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    // Star rating JS
    document.querySelectorAll('.star-rating .star').forEach(star => {
        star.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            document.getElementById('rating').value = value;
            document.querySelectorAll('.star-rating .star').forEach(s => s.style.color = '#e4e5e9');
            for (let i = 0; i < value; i++) {
                document.querySelectorAll('.star-rating .star')[i].style.color = '#ffc107';
            }
        });
    });
</script>
@endsection
