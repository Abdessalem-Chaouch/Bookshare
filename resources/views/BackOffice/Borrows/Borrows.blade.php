@extends('baseB')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Management /</span> Borrows
        </h4>

        <div class="card">
            <h5 class="card-header">Borrows</h5>
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Borrower</th>
                            <th>Author</th>
                            <th>Date Start</th>
                            <th>Date End</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($borrows as $borrow)
                        <tr>
                            <td>{{ $borrow->livre->titre ?? 'Unknown' }}</td>
                            <td>{{ $borrow->user->name ?? 'Unknown' }}</td>
                            <td>{{ $borrow->auteur->name ?? 'Unknown' }}</td>
                            <td>{{ $borrow->date_debut->format('d/m/Y H:i') }}</td>
                            <td>{{ $borrow->date_fin->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($borrow->status === 'active')
                                    <span class="badge bg-label-success">{{ ucfirst($borrow->status) }}</span>
                                @elseif($borrow->status === 'expired')
                                    <span class="badge bg-label-danger">{{ ucfirst($borrow->status) }}</span>
                                @else
                                    <span class="badge bg-label-warning">{{ ucfirst($borrow->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
        {{ $borrows->links('pagination::bootstrap-5') }}
    </div>
        </div>
    </div>
</div>
@endsection
