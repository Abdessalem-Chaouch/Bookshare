@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Payment History</h4>
        <x-currency-selector />
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">My Transactions</h5>
            <span class="badge bg-primary">{{ $payments->count() }} TRANSACTION(S)</span>
        </div>
        <div class="card-body">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>DATE</th>
                                <th>SUBSCRIPTION</th>
                                <th>AMOUNT</th>
                                <th>METHOD</th>
                                <th>STATUS</th>
                                <th>TRANSACTION ID</th>
                                <th>INVOICE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong>{{ $payment->subscription->name }}</strong><br>
                                    <small class="text-muted">{{ $payment->subscription->duration_days }} days</small>
                                </td>
                                <td>
                                    <strong>
                                        @php
                                            $currencyService = app(\App\Services\CurrencyService::class);
                                            $userCurrency = $currencyService->getUserCurrency();
                                            // Convert from payment currency to user's selected currency
                                            $convertedAmount = $currencyService->convert(
                                                $payment->amount,
                                                $payment->currency,
                                                $userCurrency
                                            );
                                            echo $currencyService->format($convertedAmount, $userCurrency);
                                        @endphp
                                    </strong>
                                </td>
                                <td>
                                    <i class="bx bx-credit-card me-1"></i>
                                    Card ********
                                </td>
                                <td>
                                    @switch($payment->payment_status)
                                        @case('completed')
                                            <span class="badge bg-success">SUCCESS</span>
                                            @break
                                        @case('failed')
                                            <span class="badge bg-danger">FAILED</span>
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning">PENDING</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ strtoupper($payment->payment_status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <code>{{ $payment->payment_id }}</code>
                                </td>
                                <td>
                                    @if($payment->invoice)
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('invoice.view', $payment->invoice->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               target="_blank"
                                               title="View Invoice">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a href="{{ route('invoice.download', $payment->invoice->id) }}" 
                                               class="btn btn-sm btn-outline-success"
                                               title="Download Invoice">
                                                <i class="bx bx-download"></i>
                                            </a>
                                        </div>
                                        <div class="mt-1">
                                            <small class="text-muted">{{ $payment->invoice->invoice_number }}</small>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bx bx-receipt" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="text-muted mt-2">No transactions found</p>
                    <a href="{{ route('author.subscriptions') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>Subscribe to a plan
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection