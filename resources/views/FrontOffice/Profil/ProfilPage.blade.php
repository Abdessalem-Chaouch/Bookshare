@extends('baseF')

@section('content')
<section id="user-profile" class="py-5 my-5">
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-6">

                {{-- Section Header --}}
                <div class="section-header text-center mb-4">
                    <div class="title">
                        <span>Your Account</span>
                    </div>
                    <h2 class="section-title">User Profile</h2>
                </div>

                {{-- Profile Update Form --}}
                <div class="card shadow-sm rounded-4 p-4 mb-5">
                    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Profile Photo --}}
                        <div class="profile-photo text-center mb-4">
                            <div class="photo-wrapper position-relative d-inline-block">
                                <img src="{{ auth()->user()->photo_profil
                                        ? asset('storage/'.auth()->user()->photo_profil) 
                                        : asset('images/default-avatar.jpg') }}"
                                     alt="Profile Photo"
                                     class="profile-img mb-3 rounded-circle"
                                     id="profilePreview"
                                     style="width:120px; height:120px; object-fit:cover;">

                                <label for="photoUpload" class="edit-icon position-absolute bottom-0 end-0 bg-white rounded-circle p-1" style="cursor:pointer;">
                                    <i class="bi bi-pencil"></i>
                                </label>
                                <input type="file" name="photo" id="photoUpload" accept="image/*" hidden>
                            </div>
                        </div>

                        {{-- Name & Email --}}
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}">
                        </div>

                        {{-- Change Password --}}
                        <button class="btn btn-link mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#passwordChange">
                            Change Password
                        </button>
                        <div class="collapse" id="passwordChange">
                            <input type="password" name="current_password" class="form-control mb-2" placeholder="Current Password">
                            <input type="password" name="new_password" class="form-control mb-2" placeholder="New Password">
                            <input type="password" name="new_password_confirmation" class="form-control mb-2" placeholder="Confirm New Password">
                        </div>

                        {{-- Review Count Card --}}
                        <div class="d-flex justify-content-center mt-4">
                            <div class="card text-center shadow-sm p-3" style="width: 120px; border-radius: 12px; cursor:pointer;" onclick="scrollToReviews()">
                                <div class="card-body p-2">
                                    <div class="mb-1" style="font-size: 24px; color: #ffc107;">
                                        <i class="bi bi-star-fill"></i>
                                    </div>
                                    <h5 class="mb-0">{{ $reviewsCount }}</h5>
                                    <small class="text-muted">Reviews</small>
                                </div>
                            </div>
                        </div>

                        {{-- Update Button --}}
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-outline-accent btn-accent-arrow">
                                Update Profile <i class="icon icon-ns-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- User Reviews --}}
                <div id="user-reviews">
                    <div class="section-header text-center mb-3">
                        <h3>Your Store Reviews</h3>
                    </div>

                    @forelse(auth()->user()->reviews as $review)
                        <div class="card shadow-sm mb-3 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>{{ $review->store->store_name ?? 'Store Deleted' }}</strong>
                                <div>
                                    @for($i=1; $i<=5; $i++)
                                        <span style="color: {{ $i <= $review->rating ? '#ffc107' : '#e4e5e9' }};">&#9733;</span>
                                    @endfor
                                </div>
                            </div>
                            <p class="mb-2">{{ $review->comment }}</p>
                            <small class="text-muted">Reviewed on {{ $review->created_at->format('d M Y') }}</small>

                            <div class="mt-2">
                                <button class="btn btn-sm btn-secondary" onclick="toggleEditForm({{ $review->id }})">Edit</button>
                                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="d-inline-block">
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
                        </div>
                    @empty
                        <p class="text-center text-muted">You haven’t reviewed any stores yet.</p>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</section>

<script>
    // Profile photo preview
    document.getElementById('photoUpload').onchange = function (evt) {
        const [file] = this.files;
        if (file) {
            document.getElementById('profilePreview').src = URL.createObjectURL(file);
        }
    };

    // Toggle edit form for reviews
    function toggleEditForm(id) {
        const form = document.getElementById('edit-form-' + id);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    // Scroll to reviews when clicking the count card
    function scrollToReviews() {
        const reviewsSection = document.getElementById('user-reviews');
        reviewsSection.scrollIntoView({ behavior: 'smooth' });
    }
</script>
@endsection
