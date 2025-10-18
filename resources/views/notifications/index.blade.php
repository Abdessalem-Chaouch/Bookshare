@extends('baseF')

@section('content')
<section class="py-5 text-center">
    <div class="container">
        <h2 class="mb-4">📊 Prediction Notifications</h2>

        @if($notifications->isEmpty())
            <p class="text-muted">No notifications yet 🚀</p>
        @else
            @foreach($notifications as $notification)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary">{{ $notification->title }}</h5>
                        <p class="card-text">{{ $notification->message }}</p>
                        <small class="text-muted">
                            Store: {{ $notification->store->store_name ?? 'Unknown' }} <br>
                            Created: {{ $notification->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</section>
@endsection
