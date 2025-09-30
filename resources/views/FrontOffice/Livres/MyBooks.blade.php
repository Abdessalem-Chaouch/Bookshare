@extends('baseF')

@section('content')
<section id="featured-books" class="py-5 my-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="section-header align-center mb-4">
                    <div class="title">
                        <span>Your purchased items</span>
                    </div>
                    <h2 class="section-title">My Books</h2>
                </div>

                @if($payments->isEmpty())
                    <p class="text-center">You have not purchased any books yet.</p>
                @else
                <div class="table-responsive">
                    <table class="table custom-table align-middle">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Price</th>
                                <th>Purchase Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                @if($payment->livre)
                                <tr onclick="window.location='{{ route('livres.showf', $payment->livre->id) }}'" 
    style="cursor: pointer;">
                                    <td>
                                        <img src="{{ asset('storage/' . $payment->livre->photo_couverture) }}" 
                                             alt="{{ $payment->livre->titre }}" 
                                             class="cover-img">
                                    </td>
                                    <td>{{ $payment->livre->titre }}</td>
                                    <td>{{ $payment->livre->user ? $payment->livre->user->name : 'Auteur inconnu' }}</td>
                                    <td>{{ $payment->livre->prix ? $payment->livre->prix . ' DT' : 'Not specified' }}</td>
                                    <td>{{ $payment->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('livres.viewpdf', $payment->livre->id) }}" 
                                           class="btn btn-outline-accent2 mb-1">
                                            View
                                        </a>
                                        <a href="{{ route('livres.download', $payment->livre->id) }}" 
                                           class="btn btn-outline-accent2">
                                            Download
                                        </a>
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
