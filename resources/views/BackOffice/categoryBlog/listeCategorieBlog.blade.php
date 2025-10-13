@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold"><span class="text-muted fw-light">Tables /</span> Blog Categories List</h4>
        <a href="{{ route('categoryBlog.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>Add Category
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <h5 class="card-header">Blog Categories</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($categories as $category)
                    <tr>
                        <!-- Name -->
                        <td><strong>{{ $category->name }}</strong></td>

                        <!-- Description -->
                        <td>
                            <strong>
                                @php
                                $limit = 20; // number of characters to display
                                $content = $category->description;
                                @endphp

                                @if(strlen($content) > $limit)
                                <span class="short-text">{{ substr($content, 0, $limit) }}</span>
                                <span class="full-text d-none">{{ $content }}</span>
                                <a href="javascript:void(0)" class="toggle-text"> see more</a>
                                @else
                                {{ $content }}
                                @endif
                            </strong>
                        </td>

                        <!-- Actions -->
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('categoryBlog.edit', $category->id) }}">
                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('categoryBlog.destroy', $category->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item text-danger" onclick="return confirm('Do you really want to delete this category?')">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                    @if($categories->isEmpty())
                    <tr>
                        <td colspan="3" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bx bx-category bx-lg mb-2"></i>
                                <p>No category found</p>
                                <a href="{{ route('categoryBlog.create') }}" class="btn btn-primary btn-sm">
                                    Add the first category
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-text').forEach(function(link) {
        link.addEventListener('click', function() {
            const td = this.closest('td');
            const shortText = td.querySelector('.short-text');
            const fullText = td.querySelector('.full-text');

            if(shortText.classList.contains('d-none')) {
                // Back to short text
                shortText.classList.remove('d-none');
                fullText.classList.add('d-none');
                this.textContent = ' see more';
            } else {
                // Show full text
                shortText.classList.add('d-none');
                fullText.classList.remove('d-none');
                this.textContent = ' see less';
            }
        });
    });
});
</script>

@endsection
