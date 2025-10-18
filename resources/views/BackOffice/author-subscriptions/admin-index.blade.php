@extends('baseB')

@section('title', 'Author Subscriptions')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Management /</span> Author Subscriptions
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Subscription List</h5>
            <div class="d-flex gap-2">
                <button id="aiAnalysisBtn" class="btn btn-ai-analysis">
                    <i class="bx bx-brain"></i> AI Analysis
                </button>
            </div>
        </div>
        
        <div class="card-body">
            @if($authorSubscriptions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>AUTHOR</th>
                                <th>EMAIL</th>
                                <th>PLAN</th>
                                <th>PRICE</th>
                                <th>START DATE</th>
                                <th>EXPIRATION DATE</th>
                                <th>STATUS</th>
                                <th>DURATION</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($authorSubscriptions as $subscription)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                @if($subscription->user->photo_profil)
                                                    <img src="{{ asset('uploads/' . $subscription->user->photo_profil) }}" 
                                                         alt="Avatar" class="rounded-circle">
                                                @else
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($subscription->user->name, 0, 1)) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $subscription->user->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $subscription->user->email }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $subscription->subscription->name }}</span>
                                    </td>
                                    <td>{{ number_format($subscription->subscription->price, 2) }} €</td>
                                    <td>{{ $subscription->starts_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $subscription->expires_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($subscription->isActive())
                                            <span class="badge bg-success">ACTIVE</span>
                                        @elseif($subscription->isExpired())
                                            <span class="badge bg-danger">EXPIRED</span>
                                        @else
                                            <span class="badge bg-secondary">INACTIVE</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $duration = $subscription->starts_at->diffInDays($subscription->expires_at);
                                        @endphp
                                        {{ $duration }} days
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.author-subscriptions.destroy', $subscription->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this subscription?')">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($authorSubscriptions->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4 px-3 pb-3">
                        <div class="text-muted small">
                            Showing {{ $authorSubscriptions->firstItem() }} to {{ $authorSubscriptions->lastItem() }} of {{ $authorSubscriptions->total() }} results
                        </div>
                        <nav>
                            {{ $authorSubscriptions->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="bx bx-package bx-lg text-muted mb-3"></i>
                    <h5 class="text-muted">No subscriptions found</h5>
                    <p class="text-muted">No authors have subscribed to a plan yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- AI Analysis Modal -->
    <div class="modal fade" id="aiAnalysisModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title">
                        <i class="bx bx-brain me-2"></i>
                        AI Subscription Analysis
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="aiAnalysisContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Generating...</span>
                            </div>
                            <h6 class="text-primary">AI Analysis in progress...</h6>
                            <p class="text-muted mb-0">Processing subscription data</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.getElementById('aiAnalysisBtn').addEventListener('click', function() {
    const modal = new bootstrap.Modal(document.getElementById('aiAnalysisModal'));
    modal.show();
    
    fetch('{{ route("admin.author-subscriptions.ai-analysis") }}?t=' + Date.now())
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let content = '';
                
                // Header with analysis type
                const isML = data.source === 'machine_learning';
                const confidenceBadge = data.confidence === 'VERY HIGH' ? 'success' : 
                                       data.confidence === 'HIGH' ? 'primary' : 
                                       data.confidence === 'MEDIUM' ? 'warning' : 'secondary';
                
                content += `
                    <div class="ai-analysis-header mb-4">
                        <div class="ai-header-content">
                            <div class="ai-icon-container">
                                <i class="bx bx-brain ai-brain-icon"></i>
                            </div>
                            <div class="ai-header-text">
                                <h5 class="ai-title mb-2">
                                    <span class="gradient-text">🤖 Machine Learning Analysis</span>
                                </h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="confidence-badge confidence-${confidenceBadge}">
                                        <i class="bx bx-check-circle me-1"></i>
                                        Confidence: ${data.confidence}
                                    </span>
                                    <span class="ai-status-badge">
                                        <span class="pulse-dot"></span>
                                        Live Analysis
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Prédictions ML en cartes modernes
                if (data.ml_predictions) {
                    content += `
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-sm bg-label-danger me-2">
                                                <i class="bx bx-trending-down"></i>
                                            </div>
                                            <h6 class="card-title mb-0">Churn Prediction</h6>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">Current rate</span>
                                                <strong class="text-danger">${data.ml_predictions.churn_prediction.current_churn_rate}%</strong>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-danger" style="width: ${data.ml_predictions.churn_prediction.current_churn_rate}%"></div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Predicted: ${data.ml_predictions.churn_prediction.predicted_churn_rate}%</span>
                                            <span class="badge bg-${data.ml_predictions.churn_prediction.risk_level === 'LOW' ? 'success' : data.ml_predictions.churn_prediction.risk_level === 'MODERATE' ? 'warning' : 'danger'}">
                                                ${data.ml_predictions.churn_prediction.risk_level}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-sm bg-label-success me-2">
                                                <i class="bx bx-trending-up"></i>
                                            </div>
                                            <h6 class="card-title mb-0">Growth Forecast</h6>
                                        </div>
                                        <div class="row g-2 text-center">
                                            <div class="col-6">
                                                <div class="border rounded p-2">
                                                    <div class="text-success fw-bold">${data.ml_predictions.growth_forecast.current_month || 0}</div>
                                                    <small class="text-muted">This month</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="border rounded p-2">
                                                    <div class="text-primary fw-bold">${data.ml_predictions.growth_forecast.next_month}</div>
                                                    <small class="text-muted">Next</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-center">
                                            <span class="badge bg-${data.ml_predictions.growth_forecast.growth_rate >= 0 ? 'success' : 'danger'} fs-6">
                                                ${data.ml_predictions.growth_forecast.growth_rate >= 0 ? '↗' : '↘'} ${data.ml_predictions.growth_forecast.growth_rate}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                // Analyse principale avec style amélioré
                content += `
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h6 class="card-title mb-0">
                                <i class="bx bx-analyse text-primary me-2"></i>
                                Detailed Analysis
                            </h6>
                        </div>
                        <div class="card-body pt-2">
                            <div class="analysis-content" style="white-space: pre-line; line-height: 1.6; font-size: 0.95em;">${data.analysis}</div>
                        </div>
                    </div>
                `;
                
                // Recommandations ML
                if (data.recommendations && data.recommendations.length > 0) {
                    content += `
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">🎯 ML Recommendations</h6>
                                <ul class="list-unstyled mb-0">
                    `;
                    data.recommendations.forEach(rec => {
                        content += `<li class="mb-2"><i class="bx bx-check-circle text-primary"></i> ${rec}</li>`;
                    });
                    content += `
                                </ul>
                            </div>
                        </div>
                    `;
                }
                
                // Stats de base avec design moderne
                content += `
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <div class="row g-3 text-center">
                                <div class="col-3">
                                    <div class="d-flex flex-column">
                                        <span class="text-primary fw-bold fs-4">${data.stats.total}</span>
                                        <small class="text-muted">Total</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="d-flex flex-column">
                                        <span class="text-success fw-bold fs-4">${data.stats.active}</span>
                                        <small class="text-muted">Active</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="d-flex flex-column">
                                        <span class="text-info fw-bold fs-4">${data.stats.this_month}</span>
                                        <small class="text-muted">This month</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="d-flex flex-column">
                                        <span class="text-warning fw-bold fs-4">${data.stats.conversion_rate}%</span>
                                        <small class="text-muted">Rate</small>
                                    </div>
                                </div>
                            </div>
                            ${data.timestamp ? `<div class="text-center mt-3 pt-3 border-top"><small class="text-muted"><i class="bx bx-time-five me-1"></i>Last updated: ${data.timestamp}</small></div>` : ''}
                        </div>
                    </div>
                `;
                
                document.getElementById('aiAnalysisContent').innerHTML = content;
            } else {
                document.getElementById('aiAnalysisContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bx bx-error"></i> ML analysis error: ${data.message || 'Unknown error'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('aiAnalysisContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bx bx-error"></i> Connection error. Check that your server and database are active.
                </div>
            `;
        });
});
</script>

<style>
/* AI Analysis Button Styles */
.btn-ai-analysis {
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

.btn-ai-analysis::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s;
}

.btn-ai-analysis:hover::before {
    left: 100%;
}

.btn-ai-analysis:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
    color: white;
}

.btn-ai-analysis i {
    font-size: 18px;
    animation: brain-pulse 2s infinite;
}

@keyframes brain-pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.15);
    }
}

/* AI Analysis Header Styles */
.ai-analysis-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.ai-analysis-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate-gradient 10s linear infinite;
}

@keyframes rotate-gradient {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.ai-header-content {
    display: flex;
    align-items: center;
    position: relative;
    z-index: 1;
}

.ai-icon-container {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.ai-brain-icon {
    font-size: 40px;
    color: white;
    animation: brain-pulse 2s ease-in-out infinite;
}

@keyframes brain-pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); }
}

.ai-header-text {
    flex: 1;
}

.ai-title {
    margin: 0;
    color: white;
    font-weight: 700;
}

.gradient-text {
    background: linear-gradient(90deg, #fff 0%, #f0f0f0 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 24px;
}

.confidence-badge {
    background: rgba(255, 255, 255, 0.25);
    color: white;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    display: inline-flex;
    align-items: center;
}

.confidence-success {
    background: rgba(40, 199, 111, 0.3);
    border-color: rgba(40, 199, 111, 0.5);
}

.confidence-primary {
    background: rgba(102, 126, 234, 0.3);
    border-color: rgba(102, 126, 234, 0.5);
}

.confidence-warning {
    background: rgba(255, 193, 7, 0.3);
    border-color: rgba(255, 193, 7, 0.5);
}

.ai-status-badge {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 12px;
    font-weight: 600;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: inline-flex;
    align-items: center;
}

.pulse-dot {
    width: 8px;
    height: 8px;
    background: #4ade80;
    border-radius: 50%;
    margin-right: 8px;
    animation: pulse-dot 2s ease-in-out infinite;
    box-shadow: 0 0 10px #4ade80;
}

@keyframes pulse-dot {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.2);
    }
}
</style>

@endsection