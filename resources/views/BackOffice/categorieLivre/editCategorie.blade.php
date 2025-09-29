@extends('baseB')
@section('content')

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forms/</span> Edit Category</h4>

        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Edit Category</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="name">Name</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-category"></i></span>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $category->name) }}" required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 form-label" for="description">Description</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-comment"></i></span>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description">{{ old('description', $category->description) }}</textarea>
                                    </div>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @if($category->image)
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Current Image</label>
                                <div class="col-sm-10">
                                    <img src="{{ asset('storage/' . $category->image) }}" 
                                         alt="{{ $category->name }}" 
                                         class="rounded" 
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="image">New Image</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*" style="display: none;">
                                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('image').click()">
                                            Choose file
                                        </button>
                                        <span class="form-control" id="file-name-edit">No file chosen</span>
                                    </div>
                                    <div class="form-text">Leave empty to keep current image</div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
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

<script>
document.getElementById('image').addEventListener('change', function() {
    document.getElementById('file-name-edit').textContent = this.files.length > 0 ? this.files[0].name : 'No file chosen';
});
</script>

@endsection