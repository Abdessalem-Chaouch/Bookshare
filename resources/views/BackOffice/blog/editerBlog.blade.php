@extends('baseB')
@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forms /</span> Edit Blog</h4>
        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Edit Blog</h5>
                        <small class="text-muted float-end">Edit Form</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Title -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="title">Title</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-book"></i></span>
                                        <input
                                            type="text"
                                            name="title"
                                            class="form-control @error('title') is-invalid @enderror"
                                            id="title"
                                            placeholder="Blog title"
                                            value="{{ old('title', $blog->title) }}">
                                    </div>
                                    @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="row mb-3">
                                <label class="col-sm-2 form-label" for="content">Content</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-comment"></i></span>
                                        <textarea
                                            name="content"
                                            class="form-control @error('content') is-invalid @enderror"
                                            id="content"
                                            placeholder="Write the blog content..."
                                            rows="5">{{ old('content', $blog->content) }}</textarea>
                                    </div>
                                    @error('content')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="row mb-3">
                                <label class="col-sm-2 form-label" for="category_id">Category</label>
                                <div class="col-sm-10">
                                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" id="category_id">
                                        <option value="">-- Select a category --</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (old('category_id', $blog->category_id) == $category->id) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Image -->
                            <div class="row mb-3">
                                <label class="col-sm-2 form-label">Image</label>
                                <div class="col-sm-10">
                                    <input
                                        type="hidden"
                                        role="uploadcare-uploader"
                                        name="image"
                                        class="@error('image') is-invalid @enderror"
                                        data-public-key="{{ config('services.uploadcare.public_key') }}"
                                        data-images-only="true"
                                        data-clearable="true"
                                        data-crop="free"
                                        value="{{ old('image', $blog->image) }}" 
                                    >
                                    @error('image')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>


                            <!-- Buttons -->
                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ route('listeBlog') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-backdrop fade"></div>
</div>

@endsection