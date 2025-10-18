@extends('baseB')

@section('title', 'Subscription Transactions')

@section('content')
<style>
/* Fix pagination duplication issue */
.pagination {
    display: flex !important;
    padding-left: 0;
    list-style: none;
}
.pagination::before,
.pagination::after {
    display: none !important;
    content: none !important;
}
nav[aria-label="Page navigation"] {
    position: relative;
}
nav[aria-label="Page navigation"]::before,
nav[aria-label="Page navigation"]::after {
    display: none !important;
    content: none !important;
}
.btn-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 10px;
    position: relative;
    overflow: hidden;
}

.btn-gradient-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s;
}

.btn-gradient-primary:hover::before {
    left: 100%;
}

.btn-gradient-primary:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
    color: white;
}

.btn-gradient-primary i {
    font-size: 18px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

/* Center table content */
.table thead th,
.table tbody td {
    text-align: center;
    vertical-align: middle;
}

.table tbody td:first-child,
.table thead th:first-child {
    text-align: center;
}
</style>
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Management /</span> Subscription Transactions
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Transaction History</h5>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge bg-primary">{{ $transactions->total() }} TRANSACTIONS</span>
                <a href="{{ route('admin.author-transactions.analytics') }}" class="btn btn-gradient-primary">
                    <i class='bx bx-line-chart me-2'></i>View Analytics
                </a>
            </div>
        </div>
        
        <div class="card-body">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>TRANSACTION ID</th>
                                <th>AUTHOR</th>
                                <th>PLAN</th>
                                <th>AMOUNT</th>
                                <th>STATUS</th>
                                <th>METHOD</th>
                                <th>DATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        <code>{{ $transaction->payment_id }}</code>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                @if($transaction->user->photo_profil)
                                                    <img src="{{ asset('uploads/' . $transaction->user->photo_profil) }}" 
                                                         alt="Avatar" class="rounded-circle">
                                                @else
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $transaction->user->name }}</h6>
                                                <small class="text-muted">{{ $transaction->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $transaction->subscription->name }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</strong>
                                    </td>
                                    <td>
                                        @if($transaction->payment_status === 'completed')
                                            <span class="badge bg-success">COMPLETE</span>
                                        @elseif($transaction->payment_status === 'pending')
                                            <span class="badge bg-warning">PENDING</span>
                                        @elseif($transaction->payment_status === 'failed')
                                            <span class="badge bg-danger">FAILED</span>
                                        @else
                                            <span class="badge bg-secondary">{{ strtoupper($transaction->payment_status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ ucfirst($transaction->payment_method) }}</span>
                                    </td>
                                    <td>
                                        {{ $transaction->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($transactions->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4 px-3 pb-3">
                        <div class="text-muted small">
                            Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} results
                        </div>
                        <nav>
                            {{ $transactions->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="bx bx-credit-card bx-lg text-muted mb-3"></i>
                    <h5 class="text-muted">No transactions found</h5>
                    <p class="text-muted">No subscription transactions have been made yet.</p>
                </div>
            @endif
        </div>
    </div>


</div>
@endsection