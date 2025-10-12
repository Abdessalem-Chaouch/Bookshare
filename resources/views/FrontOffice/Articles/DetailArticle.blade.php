@extends('baseF')
@section('content')

<section id="article-detail" class="py-5 my-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <!-- Header -->
                <div class="section-header align-center">
                    <div class="title"><span>Article Detail</span></div>
                    <h2 class="section-title">{{ $blog->title }}</h2>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <article class="column" data-aos="fade-up">
                            <figure>
                                @if($blog->image)
                                    <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}" class="post-image w-100">
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

                                <!-- LIKE / COMMENT -->
                                <div class="links-element d-flex align-items-center justify-content-start mt-3 mb-4">
                                    <!-- Bouton Like -->
                                    <a
                                        @guest href="{{ route('login') }}" class="me-3"
                                        @else href="javascript:void(0)" class="like-btn me-3 {{ auth()->user() && $blog->likes->contains('user_id', auth()->id()) ? 'liked' : '' }}"
                                        data-blog="{{ $blog->id }}"
                                        @endguest
                                    >
                                        <i class="bi {{ auth()->user() && $blog->likes->contains('user_id', auth()->id()) ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' }}"></i>
                                    </a>

                                    <span class="like-count" data-bs-toggle="modal" data-bs-target="#likesModal{{ $blog->id }}">
                                        {{ $blog->likes->count() }}
                                    </span>

                                    <!-- Commentaires -->
                                    <a href="#comments" class="ms-3">
                                        <i class="bi bi-chat-dots"></i>
                                        <span class="comment-count">{{ $blog->comments->count() }}</span>
                                    </a>
                                </div>

                                <hr>

                                <!-- COMMENTS -->
                                <div id="comments" class="comments-section mt-4">
                                    <h4>Comments (<span class="comment-count">{{ $blog->comments->count() }}</span>)</h4>
                                    <ul class="list-unstyled comment-list">
                                        @foreach($blog->comments as $comment)
                                        <li class="mb-3 d-flex justify-content-between align-items-start" data-id="{{ $comment->id }}">
                                            <div>
                                                <strong>{{ $comment->user->name ?? 'Anonymous' }}</strong>
                                                <span class="text-muted">({{ $comment->created_at->format('M d, Y H:i') }})</span>
                                                <p class="comment-content mb-0">{{ $comment->content }}</p>
                                            </div>

                                            @if(Auth::id() === $comment->user_id)
                                            <div class="ms-3">
                                                <a href="javascript:void(0)" class="edit-comment me-2" data-id="{{ $comment->id }}" title="Modifier">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="javascript:void(0)" class="delete-comment" data-id="{{ $comment->id }}" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>

                                    <!-- Add Comment -->
                                    <div class="add-comment mt-5">
                                        @guest
                                        <p>
                                            <a href="{{ route('login') }}" class="btn btn-outline-primary">Please log in to comment</a>
                                        </p>
                                        @else
                                        <form id="comment-form" data-blog="{{ $blog->id }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="comment" class="form-label">Your Comment</label>
                                                <textarea id="comment" name="content" class="form-control" rows="4" placeholder="Write your comment..."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Post Comment</button>
                                        </form>
                                        @endguest
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                <!-- Back Button -->
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

<!-- POPUP LIKE -->
<div class="modal fade" id="likesModal{{ $blog->id }}" tabindex="-1" aria-labelledby="likesModalLabel{{ $blog->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="likesModalLabel{{ $blog->id }}">Likes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group" id="like-user-list">
                    @foreach($blog->likes as $like)
                        <li class="list-group-item">
                            {{ $like->user->name ?? 'Anonymous' }}
                            <span class="text-muted float-end">{{ $like->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                    @endforeach
                    @if($blog->likes->count() == 0)
                        <li class="list-group-item text-center text-muted">No likes yet</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
.like-btn i { color: #c5a992; cursor: pointer; }
.like-btn.liked i { color: black; }
.like-count { cursor: pointer; text-decoration: underline; color: #c5a992; margin-right: 15px; }
.like-btn.liked + .like-count { color: black; font-weight: bold; }
</style>

<!-- JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    // --- LISTE DES MOTS INTERDITS ---
    const forbiddenWords = [
        // Français
        'merde', 'putain', 'connard', 'salope', 'enculé',
        // Anglais
        'shit', 'fuck', 'bitch', 'asshole', 'damn'
    ];

    // --- FONCTION DE CENSURE ---
    function censorText(text) {
        forbiddenWords.forEach(word => {
            const regex = new RegExp(`\\b${word}\\b`, 'gi');
            text = text.replace(regex, match => '*'.repeat(match.length));
        });
        return text;
    }

    // --- LIKE / DISLIKE ---
    document.querySelectorAll('.like-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const blogId = this.dataset.blog;
            const icon = this.querySelector('i');
            const likeCount = this.nextElementSibling;
            const isLiked = this.classList.contains('liked');
            const userList = document.getElementById('like-user-list');

            fetch(`/blogs/${blogId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            })
            .then(res => res.json())
            .then(data => {
                likeCount.textContent = data.likes_count;

                if(isLiked){
                    this.classList.remove('liked');
                    icon.classList.remove('bi-hand-thumbs-up-fill');
                    icon.classList.add('bi-hand-thumbs-up');
                    likeCount.style.color = '#c5a992';
                } else {
                    this.classList.add('liked');
                    icon.classList.remove('bi-hand-thumbs-up');
                    icon.classList.add('bi-hand-thumbs-up-fill');
                    likeCount.style.color = 'black';
                }

                // Actualiser popup utilisateurs
                if(userList){
                    userList.innerHTML = '';
                    if(data.users.length > 0){
                        data.users.forEach(user => {
                            const li = document.createElement('li');
                            li.classList.add('list-group-item');
                            li.textContent = user.name;
                            userList.appendChild(li);
                        });
                    } else {
                        userList.innerHTML = '<li class="list-group-item text-center text-muted">No likes yet</li>';
                    }
                }
            })
            .catch(err => console.error(err));
        });
    });

    // --- COMMENT FORM ---
    const commentForm = document.getElementById('comment-form');
    if(commentForm){
        commentForm.addEventListener('submit', function(e){
            e.preventDefault();
            const blogId = this.dataset.blog;
            let content = this.querySelector('textarea[name="content"]').value.trim();

            // FILTRER LES MOTS INDESIRABLES
            content = censorText(content);

            const commentList = document.querySelector('.comment-list');
            const commentCountEls = document.querySelectorAll('.comment-count');

            if(content === '') return;

            fetch(`/blogs/${blogId}/comment`, {
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':'{{ csrf_token() }}',
                    'Accept':'application/json',
                    'Content-Type':'application/json'
                },
                body: JSON.stringify({ content })
            })
            .then(res=>res.json())
            .then(data=>{
                if(data.comment){
                    const li = document.createElement('li');
                    li.classList.add('mb-3','d-flex','justify-content-between','align-items-start');
                    li.dataset.id = data.comment.id;

                    li.innerHTML = `
                        <div>
                            <strong>${data.comment.user_name}</strong>
                            <span class="text-muted">(${data.comment.created_at})</span>
                            <p class="comment-content mb-0">${data.comment.content}</p>
                        </div>
                        <div class="ms-3">
                            <a href="javascript:void(0)" class="edit-comment me-2" data-id="${data.comment.id}" title="Modifier">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="javascript:void(0)" class="delete-comment" data-id="${data.comment.id}" title="Supprimer">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    `;

                    commentList.appendChild(li);
                    commentCountEls.forEach(el => el.textContent = data.comments_count);
                    commentForm.reset();
                }
            });
        });
    }

    // --- DELETE COMMENT ---
    document.addEventListener('click', function(e){
        const target = e.target.closest('a.delete-comment');
        if(target){
            const commentId = target.dataset.id;
            const li = target.closest('li');

            fetch(`/comments/${commentId}`, {
                method:'DELETE',
                headers:{
                    'X-CSRF-TOKEN':'{{ csrf_token() }}',
                    'Accept':'application/json'
                }
            })
            .then(res=>res.json())
            .then(data=>{
                if(data.success){
                    li.remove();
                    document.querySelectorAll('.comment-count').forEach(el=>{
                        el.textContent = parseInt(el.textContent)-1;
                    });
                }
            });
        }
    });

    // --- EDIT COMMENT ---
    document.addEventListener('click', function(e){
        const target = e.target.closest('a.edit-comment');
        if(target){
            const commentId = target.dataset.id;
            const li = target.closest('li');
            const contentP = li.querySelector('.comment-content');
            const oldContent = contentP.textContent;

            const textarea = document.createElement('textarea');
            textarea.value = oldContent;
            textarea.className = 'form-control';
            textarea.rows = 4;
            textarea.style.width = '100%';
            textarea.style.minHeight = contentP.offsetHeight+'px';
            textarea.style.marginBottom = '1rem';

            contentP.replaceWith(textarea);
            target.style.display = 'none';
            textarea.focus();

            textarea.addEventListener('keydown', function(ev){
                if(ev.key === 'Enter' && !ev.shiftKey){
                    ev.preventDefault();
                    let newContent = textarea.value.trim();

                    // FILTRER LES MOTS INDESIRABLES
                    newContent = censorText(newContent);

                    if(newContent === '') return;

                    fetch(`/comments/${commentId}`,{
                        method:'PUT',
                        headers:{
                            'X-CSRF-TOKEN':'{{ csrf_token() }}',
                            'Accept':'application/json',
                            'Content-Type':'application/json'
                        },
                        body: JSON.stringify({ content:newContent })
                    })
                    .then(res=>res.json())
                    .then(data=>{
                        if(data.comment){
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
