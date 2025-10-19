@extends('baseB')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Management /</span> Transactions
        </h4>

        <div class="card">
            <h5 class="card-header">Transactions</h5>
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Payer Email</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->product_name }}</td>
                            <td>{{ $payment->payer_name }}</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->currency }}</td>
                            <td>{{ $payment->payer_email }}</td>
                            <td>
                                @if($payment->payment_status == 'COMPLETED')
                                    <span class="badge bg-label-success">{{ $payment->payment_status }}</span>
                                @else
                                    <span class="badge bg-label-warning">{{ $payment->payment_status }}</span>
                                @endif
                            </td>
                            <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
  

            </div>
                  <div class="mt-3 d-flex justify-content-center">
    {{ $payments->links('pagination::bootstrap-5') }}
</div>
        </div>
    

   
    </div>
       
</div>
@endsection
