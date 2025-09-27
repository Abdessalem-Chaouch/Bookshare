@extends('baseF')

@section('content')
    <section class="store-details py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <img src="{{ asset('images/store-placeholder.png') }}" alt="{{ $store->store_name }}" class="img-fluid">
                </div>
                <div class="col-md-6">
                    <h2>{{ $store->store_name }}</h2>
                    <p><strong>Owner:</strong> {{ $store->owner_name }}</p>
                    <p><strong>Location:</strong> {{ $store->location }}</p>
                    <p><strong>Contact:</strong> {{ $store->contact }}</p>
                    <!-- Add more details if needed -->
                </div>
            </div>
        </div>
    </section>
@endsection
