<section id="latest-blog" class="py-5 my-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
 
                <!-- Section Header -->
                <div class="section-header align-center">
                    <div class="title"><span>Read our articles</span></div>
                    <h2 class="section-title">Latest Articles</h2>
                </div>

                <!-- Recherche -->
                <div class="row mb-4">
                    <div class="col-md-6 offset-md-3">
                        <input type="text" id="searchInput" class="form-control text-center transparent-input" placeholder="Search articles or categories...">
                    </div>
                </div>

                <!-- Sélecteur de catégorie -->
                <div class="row mb-4">
                    <div class="col-md-4 offset-md-4">
                        <form method="GET" action="{{ route('articles') }}">
                            <select name="category" class="form-select text-center fw-bold category-select" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categoriesblogs as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <style>
                    .transparent-input { background-color: transparent !important; border: 4px solid #ccc; padding: 0.5rem 1rem; text-align: center; font-family: 'Times New Roman', Times, serif; }
                    .category-select { background-color: transparent !important; border: 1px solid #ccc; padding: 0.5rem 1rem; text-align: center; font-family: 'Times New Roman', Times, serif; }
                    .like-section { display: flex; align-items: center; gap: 10px; }
                    .like-btn.liked i { color: black; }
                    .like-btn.liked + .like-count { color: black; font-weight: bold; }
                    .like-count { cursor: pointer; text-decoration: underline; color: #c5a992; }
                    .section-title-centered { text-align: center; margin-bottom: 2rem; font-size: 2rem; }
                </style>

                <!-- ARTICLES RECOMMANDÉS -->
                @php
                    $selectedCategory = request('category');
                @endphp

                @if(isset($recommendedArticles) && $recommendedArticles->isNotEmpty())
                    @php
                        // Filtrer les articles recommandés selon la catégorie sélectionnée
                        $filteredRecommended = $selectedCategory 
                            ? $recommendedArticles->filter(fn($a) => $a->category->id == $selectedCategory)
                            : $recommendedArticles;
                    @endphp

                    @if($filteredRecommended->isNotEmpty())
                        <h2 class="section-title-centered">Recommended Articles for You</h2>
                        <div class="row" id="recommendedArticlesContainer">
                            @foreach($filteredRecommended as $index => $blog)
                                <div class="col-md-4">
                                    <article class="column" data-aos="fade-up" @if($index) data-aos-delay="{{ $index * 200 }}" @endif>
                                        <figure>
                                            <a href="{{ route('articleDetail', $blog->id) }}" class="image-hvr-effect">
                                                <img src="{{ $blog->image ? (filter_var($blog->image, FILTER_VALIDATE_URL) ? $blog->image : asset($blog->image)) : 'images/default-post.jpg' }}" alt="{{ $blog->title }}" class="post-image">
                                            </a>
                                        </figure>

                                        <div class="post-item">
                                            <div class="meta-date">{{ $blog->created_at ? \Carbon\Carbon::parse($blog->created_at)->format('M d, Y') : '' }}</div>
                                            <div class="meta-category text-muted mb-1">{{ $blog->category->name }}</div>

                                            <h3>
                                                <a href="{{ route('articleDetail', $blog->id) }}">
                                                    {{ \Illuminate\Support\Str::limit($blog->title, 50, '...') }}
                                                </a>
                                            </h3>

                                            <div class="links-element d-flex align-items-center justify-content-between">
                                                <div class="icons like-section">
                                                    <a
                                                        @guest href="{{ route('login') }}"
                                                        @else href="javascript:void(0)"
                                                        data-blog="{{ $blog->id }}"
                                                        class="like-btn {{ auth()->user() && collect($blog->likes ?? [])->contains('user_id', auth()->id()) ? 'liked' : '' }}"
                                                        @endguest>
                                                        <i class="bi {{ auth()->user() && collect($blog->likes ?? [])->contains('user_id', auth()->id()) ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' }}"></i>
                                                    </a>

                                                    <span class="like-count" data-bs-toggle="modal" data-bs-target="#likesModal{{ $blog->id }}">
                                                        {{ isset($blog->likes) ? collect($blog->likes)->count() : 0 }}
                                                    </span>

                                                    <a href="{{ route('articleDetail', $blog->id) }}" class="ms-3">
                                                        <i class="bi bi-chat-dots"></i>
                                                        {{ isset($blog->comments) ? collect($blog->comments)->count() : 0 }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </article>

                                    <!-- Popup Likes -->
                                    <div class="modal fade" id="likesModal{{ $blog->id }}" tabindex="-1" aria-labelledby="likesModalLabel{{ $blog->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="likesModalLabel{{ $blog->id }}">Likes</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <ul class="list-group">
                                                        @if(isset($blog->likes) && count($blog->likes) > 0)
                                                            @foreach($blog->likes as $like)
                                                                <li class="list-group-item">
                                                                    {{ $like->user->name ?? 'Anonymous' }}
                                                                    <span class="text-muted float-end">{{ isset($like->created_at) ? \Carbon\Carbon::parse($like->created_at)->format('d/m/Y H:i') : '' }}</span>
                                                                </li>
                                                            @endforeach
                                                        @else
                                                            <li class="list-group-item text-center text-muted">No likes yet</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif

                <hr>

                <!-- TOUS LES AUTRES ARTICLES -->
                @php
                    $filteredBlogs = $selectedCategory 
                        ? $blogs->filter(fn($a) => $a->category->id == $selectedCategory)
                        : $blogs;
                @endphp

                <h2 class="section-title-centered">Latest articles</h2>
                <div class="row" id="articlesContainer">
                    @foreach($filteredBlogs as $index => $blog)
                        <div class="col-md-4">
                            <article class="column" data-aos="fade-up" @if($index) data-aos-delay="{{ $index * 200 }}" @endif>
                                <figure>
                                    <a href="{{ route('articleDetail', $blog->id) }}" class="image-hvr-effect">
                                        <img src="{{ $blog->image ? (filter_var($blog->image, FILTER_VALIDATE_URL) ? $blog->image : asset($blog->image)) : 'images/default-post.jpg' }}" alt="{{ $blog->title }}" class="post-image">
                                    </a>
                                </figure>

                                <div class="post-item">
                                    <div class="meta-date">{{ $blog->created_at ? \Carbon\Carbon::parse($blog->created_at)->format('M d, Y') : '' }}</div>
                                    <div class="meta-category text-muted mb-1">{{ $blog->category->name ?? 'N/A' }}</div>

                                    <h3>
                                        <a href="{{ route('articleDetail', $blog->id) }}">
                                            {{ \Illuminate\Support\Str::limit($blog->title, 50, '...') }}
                                        </a>
                                    </h3>

                                    <div class="links-element d-flex align-items-center justify-content-between">
                                        <div class="icons like-section">
                                            <a
                                                @guest href="{{ route('login') }}"
                                                @else href="javascript:void(0)"
                                                data-blog="{{ $blog->id }}"
                                                class="like-btn {{ auth()->user() && collect($blog->likes ?? [])->contains('user_id', auth()->id()) ? 'liked' : '' }}"
                                                @endguest>
                                                <i class="bi {{ auth()->user() && collect($blog->likes ?? [])->contains('user_id', auth()->id()) ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' }}"></i>
                                            </a>

                                            <span class="like-count" data-bs-toggle="modal" data-bs-target="#likesModal{{ $blog->id }}">
                                                {{ isset($blog->likes) ? collect($blog->likes)->count() : 0 }}
                                            </span>

                                            <a href="{{ route('articleDetail', $blog->id) }}" class="ms-3">
                                                <i class="bi bi-chat-dots"></i>
                                                {{ isset($blog->comments) ? collect($blog->comments)->count() : 0 }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>

                            <!-- Popup Likes -->
                            <div class="modal fade" id="likesModal{{ $blog->id }}" tabindex="-1" aria-labelledby="likesModalLabel{{ $blog->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="likesModalLabel{{ $blog->id }}">Likes</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="list-group">
                                                @if(isset($blog->likes) && count($blog->likes) > 0)
                                                    @foreach($blog->likes as $like)
                                                        <li class="list-group-item">
                                                            {{ $like->user->name ?? 'Anonymous' }}
                                                            <span class="text-muted float-end">{{ isset($like->created_at) ? \Carbon\Carbon::parse($like->created_at)->format('d/m/Y H:i') : '' }}</span>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <li class="list-group-item text-center text-muted">No likes yet</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                <div class="row mt-4">
                    <div class="btn-wrap align-center">
                        <a href="{{ route('articles') }}" class="btn btn-outline-accent btn-accent-arrow" tabindex="0">
                            Read All Articles <i class="icon icon-ns-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du like
    document.querySelectorAll('.like-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const blogId = this.dataset.blog;
            const icon = this.querySelector('i');
            const likeCount = this.closest('.like-section').querySelector('.like-count');
            const modal = document.getElementById(`likesModal${blogId}`);
            const userList = modal.querySelector('ul');
            const isLiked = this.classList.contains('liked');

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

                if (isLiked) {
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

                if (userList) {
                    userList.innerHTML = '';
                    if (data.users.length > 0) {
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
            .catch(error => console.error('Erreur:', error));
        });
    });

    // Recherche dynamique
    document.getElementById('searchInput').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const articles = document.querySelectorAll('#articlesContainer .col-md-4, #recommendedArticlesContainer .col-md-4');
        articles.forEach(article => {
            const title = article.querySelector('h3 a').textContent.toLowerCase();
            const category = article.querySelector('.meta-category').textContent.toLowerCase();
            article.style.display = (title.includes(query) || category.includes(query)) ? '' : 'none';
        });
    });
});
</script>
