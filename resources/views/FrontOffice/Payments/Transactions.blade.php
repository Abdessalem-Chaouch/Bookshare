@extends('baseF')

@section('content')
<section id="featured-payments" class="py-5 my-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

           <div class="section-header align-center mb-4">
                    <div class="title">
                        <span>Your Transactions</span>
                    </div>
                    <h2 class="section-title">My Purchases</h2>
                </div>

                @if($payments->isEmpty())
                    <p class="text-center text-muted">You have no payments yet.</p>
                @else
                <div class="table-responsive">
                    <table class="table custom-table align-middle">
                        <thead>
                            <tr>
                                <th>Book</th>
                                <th>Amount</th>
                                <th>Currency</th>
                                <th>Payer Email</th>
                                <th>Status</th>
                                <th>Method</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->product_name }}</td>
                                    <td><strong>{{ number_format($payment->amount, 2) }}</strong></td>
                                    <td>{{ $payment->currency }}</td>
                                    <td>{{ $payment->payer_email }}</td>
                                    <td>
                                        <span class="badge status-{{ strtolower($payment->payment_status) }}">
                                            {{ ucfirst(strtolower($payment->payment_status)) }}
                                        </span>
                                    </td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                    <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $payments->links('pagination::bootstrap-5') }}
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
    width: 100%;
    color: #1D150A;
}
.custom-table thead {
    background: #EAE3DF !important;
    color: #1D150A;
}
.custom-table th, 
.custom-table td {
    vertical-align: middle;
    padding: 15px;
    border: none;
}
.custom-table tbody tr {
    background: #ffffffc4; 
    border-radius: 10px;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.custom-table tbody tr:hover {
    transform: scale(1.01);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Status badges */
.badge {
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 0.85rem;
    text-transform: capitalize;
}
.status-completed {
    background-color: #9c9259;
    color: #fff;
}
.status-pending, .status-processing {
    background-color: #f0c75e;
    color: #000;
}
.status-failed, .status-canceled {
    background-color: #d9534f;
    color: #fff;
}


</style>
@endsection
