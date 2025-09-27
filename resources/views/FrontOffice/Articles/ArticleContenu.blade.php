<section id="latest-blog" class="py-5 my-5">
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				<div class="section-header align-center">
					<div class="title">
						<span>Read our articles</span>
					</div>
					<h2 class="section-title">Latest Articles</h2>
				</div>

				<div class="row">
					@foreach($blogs as $index => $blog)
					<div class="col-md-4">
						<article class="column" data-aos="fade-up" @if($index) data-aos-delay="{{ $index * 200 }}" @endif>
							<figure>
								<a href="{{ route('articleDetail', $blog->id) }}" class="image-hvr-effect">
									@if($blog->image)
									<img src="{{ asset('uploads/' . $blog->image) }}" alt="{{ $blog->title }}" class="post-image">
									@else
									<img src="images/default-post.jpg" alt="default" class="post-image">
									@endif
								</a>
							</figure>

							<div class="post-item">
								<div class="meta-date">{{ $blog->created_at->format('M d, Y') }}</div>

								<!-- Affichage de la catégorie -->
								<div class="meta-category text-muted mb-1">
									{{ $blog->category->name ?? 'N/A' }}
								</div>

								<h3>
									<a href="{{ route('articleDetail', $blog->id) }}">
										{{ \Illuminate\Support\Str::limit($blog->title, 50, '...') }}
									</a>
								</h3>
								<div class="links-element d-flex align-items-center justify-content-between">
									<!-- Icônes like et chat -->
									<div class="icons d-flex align-items-center">
										<a href="javascript:void(0)" class="like-btn me-3" data-blog="{{ $blog->id }}">
											<i class="bi bi-hand-thumbs-up"></i>
											<span class="like-count">{{ $blog->likes->count() }}</span>
										</a>
										<a href="{{ route('articleDetail', $blog->id) }}" class="me-3">
											<i class="bi bi-chat-dots"></i>
											<span>{{ $blog->comments->count() }}</span>
										</a>
									</div>

									<!-- Icônes réseaux sociaux -->
									<div class="social-links d-flex align-items-center">
										<a href="#" class="me-2"><i class="icon icon-facebook"></i></a>
										<a href="#" class="me-2"><i class="icon icon-twitter"></i></a>
										<a href="#"><i class="icon icon-behance-square"></i></a>
									</div>
								</div>
							</div>
						</article>
					</div>
					@endforeach
				</div>

				<div class="row">
					<div class="btn-wrap align-center">
						<a href="{{ route('articles') }}" class="btn btn-outline-accent btn-accent-arrow" tabindex="0">Read All Articles
							<i class="icon icon-ns-arrow-right"></i>
						</a>
					</div>
				</div>

			</div>
		</div>
	</div>
</section>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		document.querySelectorAll('.like-btn').forEach(function(button) {
			button.addEventListener('click', function(e) {
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
		});
	});
</script>