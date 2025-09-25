@extends('baseF')

@section('content')
   <section id="user-profile" class="py-5 my-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="section-header align-center mb-4">
                    <div class="title">
                        <span>Your Account</span>
                    </div>
                    <h2 class="section-title">User Profile</h2>
                </div>

                {{-- Formulaire de mise à jour --}}
              <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="profile-photo text-center mb-4">
                        <div class="photo-wrapper">
                            <img src="{{ auth()->user()->photo_profil
                                    ? asset('storage/'.auth()->user()->photo_profil) 
                                    : asset('images/default-avatar.jpg') }}"
                                 alt="Profile Photo"
                                 class="profile-img mb-3"
                                 id="profilePreview">

                            {{-- Icône crayon pour changer la photo --}}
                            <label for="photoUpload" class="edit-icon">
                                <i class="bi bi-pencil"></i>

                            </label>
                            <input type="file" name="photo" id="photoUpload" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', auth()->user()->name) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', auth()->user()->email) }}">
                    </div>

                   
                    <div class="text-center">
                        <button type="submit" class="btn btn-outline-accent btn-accent-arrow">
                            Update Profile <i class="icon icon-ns-arrow-right"></i>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
@endsection
