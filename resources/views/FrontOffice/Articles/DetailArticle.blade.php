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
                                <h3 class="mb-3">{{ $blog->title }}</h3>

                                <p>{{ $blog->content }}</p>

                                <div class="links-element d-flex align-items-center justify-content-start mt-3 mb-4">
                                    <a href="#" class="me-3">
                                        <i class="bi bi-hand-thumbs-up"></i> Like
                                    </a>
                                    <a href="#">
                                        <i class="bi bi-chat-dots"></i> Comment
                                    </a>
                                </div>

                                <hr>

                                <div class="comments-section mt-4">
                                    <h4>Comments (3)</h4>

                                    <ul class="list-unstyled">
                                        <li class="mb-3">
                                            <strong>John Doe</strong> 
                                            <span class="text-muted">(Sep 20, 2025 10:30)</span>
                                            <p>Great article! Really helped me understand the topic.</p>
                                        </li>
                                        <li class="mb-3">
                                            <strong>Jane Smith</strong> 
                                            <span class="text-muted">(Sep 21, 2025 14:12)</span>
                                            <p>Very informative. Thanks for sharing!</p>
                                        </li>
                                        <li class="mb-3">
                                            <strong>Anonymous</strong> 
                                            <span class="text-muted">(Sep 22, 2025 08:45)</span>
                                            <p>I enjoyed reading this. Looking forward to more posts.</p>
                                        </li>
                                    </ul>
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

@endsection
