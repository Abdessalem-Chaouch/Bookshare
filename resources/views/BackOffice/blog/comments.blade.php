@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4>Comments for: {{ $blog->title }}</h4>


    @if($blog->comments->count() > 0)
        <ul class="list-group">
            @foreach($blog->comments as $comment)
                <li class="list-group-item">
                    <strong>{{ $comment->user->name ?? 'Anon' }}:</strong> {{ $comment->content }}
                    <span class="text-muted float-end">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                </li>
            @endforeach
        </ul>
    @else
        <p>No comments yet.</p>
    @endif
    <br>
    <a href="{{ route('listeBlog') }}" class="btn btn-secondary mb-3">Back to Blogs</a>

</div>

@endsection
