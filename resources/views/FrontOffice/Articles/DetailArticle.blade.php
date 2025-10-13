@extends('baseF')
@section('content')

<section id="article-detail" class="py-5 my-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="section-header align-center">
                    <div class="title">
                        <span>Article Detail</span>
                    </div>
                    <h2 class="section-title">{{ $blog->title }}</h2>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <article class="column" data-aos="fade-up">
                            <figure>
                                @if($blog->image)
                                <img src="{{ asset('uploads/' . $blog->image) }}" alt="{{ $blog->title }}" class="post-image w-100">
                                @else
                                <img src="images/default-post.jpg" alt="default" class="post-image w-100">
                                @endif
                            </figure>

                            <div class="post-item mt-4">
                                <div class="meta-date mb-2">{{ $blog->created_at->format('M d, Y') }}</div>
                                <div class="meta-category text-muted mb-2">
                                    Catégorie : {{ $blog->category->name ?? 'N/A' }}
                                </div>
                                <h3 class="mb-3">{{ $blog->title }}</h3>

                                <p>{{ $blog->content }}</p>

                                <!-- ✅ Zone like/comment -->
                                <div class="links-element d-flex align-items-center justify-content-start mt-3 mb-4">

                                    <a
                                        @guest
                                        class=" me-2"
                                        href="{{ route('login') }}"
                                        @else
                                        href="javascript:void(0)"
                                        class="like-btn me-3"
                                        data-blog="{{ $blog->id }}"
                                        @endguest>
                                        <i class="bi bi-hand-thumbs-up"></i>
                                        <span class="like-count">{{ $blog->likes->count() }}</span>
                                    </a>

                                    <a href="#comments">
                                        <i class="bi bi-chat-dots"></i>
                                        <span class="comment-count">{{ $blog->comments->count() }}</span>
                                    </a>
                                </div>

                                <hr>

                                <div id="comments" class="comments-section mt-4">
                                    <h4>Comments (<span class="comment-count">{{ $blog->comments->count() }}</span>)</h4>

                                    <ul class="list-unstyled comment-list">
                                        @foreach($blog->comments as $comment)
                                        <li class="mb-3 d-flex justify-content-between align-items-start" data-id="{{ $comment->id }}">
                                            <!-- Partie gauche : auteur, date et contenu -->
                                            <div>
                                                <strong>{{ $comment->user->name ?? 'Anonymous' }}</strong>
                                                <span class="text-muted">({{ $comment->created_at->format('M d, Y H:i') }})</span>
                                                <p class="comment-content mb-0">{{ $comment->content }}</p>
                                            </div>

                                            <!-- Partie droite : icônes -->
                                            @if(Auth::id() === $comment->user_id)
                                            <div class="ms-3">
                                                <a href="javascript:void(0)"
                                                    class="edit-comment me-2"
                                                    data-id="{{ $comment->id }}"
                                                    title="Modifier">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="delete-comment"
                                                    data-id="{{ $comment->id }}"
                                                    title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                            @endif
                                        </li>

                                        @endforeach
                                    </ul>

                                    <div class="add-comment mt-5">
                                        @guest
                                        <p>
                                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                                Please log in to comment
                                            </a>
                                        </p>
                                        @else
                                        <form id="comment-form" data-blog="{{ $blog->id }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="comment" class="form-label">Your Comment</label>
                                                <textarea id="comment" name="content" class="form-control" rows="4" placeholder="Write your comment..."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-send"></i> Post Comment
                                            </button>
                                        </form>
                                        @endguest
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="btn-wrap align-center">
                        <a href="{{ route('articles') }}" class="btn btn-outline-accent btn-accent-arrow">
                            Back to Articles <i class="icon icon-ns-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ✅ Script AJAX Like & Comment -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- LIKE ---
        const likeBtn = document.querySelector('.like-btn');
        if (likeBtn) {
            likeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const blogId = this.dataset.blog;
                const likeCount = this.querySelector('.like-count');

                fetch(`/blogs/${blogId}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        likeCount.textContent = data.likes_count;
                    })
                    .catch(error => console.error('Erreur:', error));
            });
        }

        // --- COMMENT ---

        const commentForm = document.getElementById('comment-form');
        if (commentForm) {
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const blogId = this.dataset.blog;
                const content = this.querySelector('textarea[name="content"]').value.trim();
                const commentList = document.querySelector('.comment-list');
                const commentCountEls = document.querySelectorAll('.comment-count');

                if (content === '') return; // éviter vide

                fetch(`/blogs/${blogId}/comment`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            content
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.comment) {
                            // ✅ construire le HTML du nouveau commentaire
                            const li = document.createElement('li');
                            li.classList.add('mb-3');
                            li.innerHTML = `
                        <strong>${data.comment.user_name}</strong>
                        <span class="text-muted">(${data.comment.created_at})</span>
                        <p>${data.comment.content}</p>
                    `;

                            // ✅ ajouter en bas (dernier commentaire)
                            commentList.appendChild(li);

                            // ✅ mettre à jour le compteur
                            commentCountEls.forEach(el => {
                                el.textContent = data.comments_count;
                            });

                            // ✅ vider le champ textarea
                            commentForm.reset();
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
            });
        }
        // --- DELETE COMMENT ---
        document.addEventListener('click', function(e) {
            const target = e.target.closest('a.delete-comment');
            if (target) {
                const commentId = target.dataset.id;
                const li = target.closest('li');

                fetch(`/comments/${commentId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            li.remove();
                            document.querySelectorAll('.comment-count').forEach(el => {
                                el.textContent = parseInt(el.textContent) - 1;
                            });
                        }
                    });
            }
        });

        // --- EDIT COMMENT (inline, Enter pour valider) ---
        document.addEventListener('click', function(e) {
            const target = e.target.closest('a.edit-comment');
            if (target) {
                const commentId = target.dataset.id;
                const li = target.closest('li');
                const contentP = li.querySelector('.comment-content');
                const oldContent = contentP.textContent;

                // Créer le textarea avec la même classe que le formulaire
                const textarea = document.createElement('textarea');
                textarea.value = oldContent;
                textarea.className = 'form-control'; // style identique au formulaire
                textarea.rows = 4; // hauteur identique
                textarea.style.width = '800px'; // même largeur que le paragraphe
                textarea.style.minHeight = contentP.offsetHeight + 'px';
                textarea.style.marginBottom = '1rem';

                contentP.replaceWith(textarea);
                target.style.display = 'none';
                textarea.focus();

                // Valider sur Enter (sans Shift)
                textarea.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' && !event.shiftKey) {
                        event.preventDefault();
                        const newContent = textarea.value.trim();
                        if (newContent === '') return;

                        fetch(`/comments/${commentId}`, {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    content: newContent
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.comment) {
                                    const p = document.createElement('p');
                                    p.classList.add('comment-content');
                                    p.textContent = data.comment.content;
                                    textarea.replaceWith(p);
                                    target.style.display = 'inline-block';
                                }
                            });
                    }
                });
            }
        });




    });
</script>

@endsection