@extends('baseF')

@section('content')
<section id="featured-books" class="py-5 my-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="section-header align-center mb-4">
                    <div class="title">
                        <span>Your borrowed items</span>
                    </div>
                    <h2 class="section-title">My Borrows</h2>
                </div>

                @if($borrows->isEmpty())
                    <p class="text-center">You have not borrowed any books yet.</p>
                @else
                <div class="table-responsive">
                    <table class="table custom-table align-middle">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Owner</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($borrows as $borrow)
                                @if($borrow->livre)
                                <tr onclick="window.location='{{ route('livres.showf', $borrow->livre->id) }}'" 
                                    style="cursor: pointer;">
                                    <td>
                                        <img src="{{ asset('storage/' . $borrow->livre->photo_couverture) }}" 
                                             alt="{{ $borrow->livre->titre }}" 
                                             class="cover-img">
                                    </td>
                                    <td>{{ $borrow->livre->titre }}</td>
                                   <td>{{ $borrow->user ? $borrow->user->name : 'Unknown' }}</td>
                                    <td>{{ $borrow->date_debut->format('d M Y') }}</td>
                                    <td>{{ $borrow->date_fin->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge status-{{ $borrow->status }}">
                                            {{ ucfirst($borrow->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($borrow->status === 'active')
                                            <a href="{{ route('livres.reader', $borrow->livre->id) }}" 
                                               class="btn btn-outline-accent2 mb-1">
                                                Read
                                            </a>
                                           
                                        @else
                                            <span class="text-muted">Unavailable</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

            </div>
        </div>
    </div>
</section>

<style>
/* Table styling */
.custom-table {
    background: transparent !important;
    border-collapse: separate;
    border-spacing: 0 10px;
}
.custom-table thead {
    background: transparent !important;
    color: #fff;
}
.custom-table th, 
.custom-table td {
    vertical-align: middle;
    padding: 15px;
}
.custom-table tbody tr {
    background: rgba(255, 255, 255, 0.05); 
    border-radius: 10px;
    transition: transform 0.2s ease-in-out;
}
.custom-table tbody tr:hover {
    transform: scale(1.01);
}

/* Cover image */
.cover-img {
    width: 60px;
    height: 90px;
    object-fit: cover;
    border-radius: 5px;
}

/* Status badges */
.badge {
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 0.85rem;
}
.status-pending {
    background-color: #ffc107;
    color: #000;
}
.status-active {
    background-color: #28a745;
    color: #fff;
}
.status-expired {
    background-color: #dc3545;
    color: #fff;
}

/* Buttons */
.btn.btn-outline-accent2 {
    font-size: 0.9rem;
    border-color: #57553fd6;
    color: var(--dark-color) !important;
    background-color: #9a988584;
    border-radius: 8px;
    padding: 5px 15px;
    transition: 0.3s;
}
.btn.btn-outline-accent2:hover {
    border-color: #57553fd6;
    background-color: #57553f8a;
    color: var(--dark-color) !important;
}
</style>
@endsection
