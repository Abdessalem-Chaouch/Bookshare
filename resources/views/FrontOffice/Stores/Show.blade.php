@extends('baseF')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
<section class="store-details py-5">
    <div class="container">
        <div class="row align-items-start">
            <!-- Store Image (Left) -->
            <div class="text-center mb-4">
                <img src="{{ $store->store_image ? asset('storage/'.$store->store_image) : asset('images/store-placeholder.png') }}" 
                    alt="{{ $store->store_name }}" 
                    class="img-fluid rounded shadow-sm" 
                    style="object-fit: cover; max-height: 400px; width: 60%;">
            </div>

            <div class="text-center">
                <h2 class="mb-3">{{ $store->store_name }}</h2>
                <p><strong>Owner:</strong> {{ $store->owner_name ?? 'N/A' }}</p>
                <p><strong>Location:</strong> {{ $store->location }}</p>
                <p><strong>Contact:</strong> {{ $store->contact ?? 'N/A' }}</p>
                {{-- <p><strong>Number of Books:</strong> {{ $store->livres_count }}</p> --}}
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
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleEditForm({{ $review->id }})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>

                        {{-- Delete button --}}
                        <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
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

        <!-- Book Fetch Section -->
        <div class="text-center mt-5">
            <button class="btn btn-outline-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#bookFetchModal">
                📚 Book Fetch: We'll Find It & E-mail You
            </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="bookFetchModal" tabindex="-1" aria-labelledby="bookFetchModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="bookFetchModalLabel">Book Fetch Request</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center p-4">
                        <p class="mb-4 text-muted">Didn’t find your book? Submit a request and we’ll e-mail you if it turns up 💌</p>

                        <form action="{{ route('bookfetch.store', $store->id) }}" method="POST" class="text-start">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Your E-mail</label>
                                <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Book Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g., The Great Gatsby">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Author</label>
                                <input type="text" name="author" class="form-control" placeholder="e.g., F. Scott Fitzgerald">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ISBN</label>
                                <input type="text" name="isbn" class="form-control" placeholder="e.g., 978-3-16-148410-0">
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="specific_edition" class="form-check-input" id="specificEdition">
                                <label class="form-check-label" for="specificEdition">
                                    I’m looking for a specific edition
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Submit Request</button>
                        </form>
                    </div>
                </div>
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
