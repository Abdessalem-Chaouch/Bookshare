@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm">
                <div class="card-body text-center">

                    <!-- Title and author -->
                    <h2 class="card-title">{{ $blog->title }}</h2>
                    <p class="text-muted">
                        <strong>Author:</strong> {{ $blog->user->name ?? 'N/A' }} |
                        <strong>Category:</strong> {{ $blog->category->name ?? 'N/A' }} |
                        <strong>Created on:</strong> {{ $blog->created_at->format('d/m/Y H:i') }}
                    </p>

                    <!-- Image -->
                    @if($blog->image)
                        <img src="{{ asset($blog->image) }}" alt="Blog Image" class="img-fluid mb-3 rounded">
                    @endif

                    <!-- Content -->
                    <div class="mb-4">
                        <p>{{ $blog->content }}</p>
                    </div>

                    <!-- Centered Buttons for Likes + Comments -->
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#likesModal">
                            View Likes ({{ $blog->likes->count() }})
                        </button>

                        <button id="toggleCommentsBtn" class="btn btn-outline-secondary" type="button">
                            View Comments ({{ $blog->comments->count() }})
                        </button>
                    </div>

                    <!-- Likes Popup -->
                    <div class="modal fade" id="likesModal" tabindex="-1" aria-labelledby="likesModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="likesModalLabel">Likes</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    @if($blog->likes->count() > 0)
                                        <ul class="list-group">
                                            @foreach($blog->likes as $like)
                                                <li class="list-group-item">
                                                    {{ $like->user->name ?? 'Anonymous' }}
                                                    <span class="text-muted float-end">{{ $like->created_at->format('d/m/Y H:i') }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>No likes yet.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comments (collapse) -->
                    <div class="collapse mt-3" id="commentsCollapse">
                        <ul class="list-group text-start" id="commentsList">
                            @forelse($blog->comments as $comment)
                                <li class="list-group-item d-flex justify-content-between align-items-center" data-comment-id="{{ $comment->id }}">
                                    <div>
                                        <strong>{{ $comment->user->name ?? 'Anonymous' }}:</strong>
                                        {{ $comment->content }}
                                    </div>
                                    <div class="text-end">
                                        <span class="text-muted me-2">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                                        <i class="bi bi-trash text-danger trash-icon" style="cursor:pointer;"></i>
                                    </div>
                                </li>
                            @empty
                                <p id="noCommentsMsg">No comments yet.</p>
                            @endforelse
                        </ul>
                    </div>

                    <a href="{{ route('listeBlog') }}" class="btn btn-secondary mt-4">Back to Blogs</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- Bootstrap Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const collapseEl = document.getElementById('commentsCollapse');
    const btn = document.getElementById('toggleCommentsBtn');
    const bsCollapse = new bootstrap.Collapse(collapseEl, { toggle: false });
    const commentsList = document.getElementById('commentsList');
    const noCommentsMsgId = 'noCommentsMsg';

    // Initial comment count
    let commentCount = {{ $blog->comments->count() }};

    function updateButtonText() {
        if (collapseEl.classList.contains('show')) {
            btn.textContent = `Hide Comments (${commentCount})`;
        } else {
            btn.textContent = `View Comments (${commentCount})`;
        }
    }

    btn.addEventListener('click', function () {
        if (collapseEl.classList.contains('show')) {
            bsCollapse.hide();
        } else {
            bsCollapse.show();
        }
    });

    collapseEl.addEventListener('shown.bs.collapse', updateButtonText);
    collapseEl.addEventListener('hidden.bs.collapse', updateButtonText);

    function deleteCommentFromDOM(li) {
        li.remove();
        commentCount--;
        updateButtonText();

        if(commentCount === 0) {
            const p = document.createElement('p');
            p.id = noCommentsMsgId;
            p.textContent = 'No comments yet.';
            commentsList.appendChild(p);
        }
    }

    document.querySelectorAll('.trash-icon').forEach(icon => {
        icon.addEventListener('click', function () {
            const li = this.closest('li');
            const commentId = li.dataset.commentId;

            if(!confirm('Are you sure you want to delete this comment?')) return;

            fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
            .then(res => {
                if(res.ok){
                    deleteCommentFromDOM(li);
                } else {
                    alert('Failed to delete comment');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error deleting comment');
            });
        });
    });
});
</script>

@endsection
