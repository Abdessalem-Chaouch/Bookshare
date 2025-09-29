
@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Books /</span> Ratings</h4>

    <div class="card">
        <h5 class="card-header">All Ratings</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rates as $rate)
                    <tr>
                        <td>{{ $rate->livre->titre }}</td>
                        <td>{{ $rate->user->name }}</td>
                        <td>
                            @for($i=1; $i<=5; $i++)
                                <i class="bx {{ $i <= $rate->note ? 'bxs-star text-warning' : 'bx-star' }}"></i>
                            @endfor
                        </td>
                        <td>{{ $rate->commentaire ?? '—' }}</td>
                        <td>{{ $rate->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
