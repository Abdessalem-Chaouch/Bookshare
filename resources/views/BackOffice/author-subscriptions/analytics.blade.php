@extends('baseB')

@section('title', 'Subscription Analytics')

@section('content')
<style>
body {
    background: #f8f9fa;
}

.analytics-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 25px;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    transition: transform 0.3s ease;
}

.analytics-card:hover {
    transform: translateY(-5px);
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
    height: 100%;
    position: relative;
    display: flex;
    flex-direction: column;
}

.stat-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.stat-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    position: relative;
}

.stat-icon-wrapper i {
    font-size: 24px;
    z-index: 1;
}

.stat-label {
    color: #9ca3af;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 8px;
    display: block;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 8px 0;
    line-height: 1.2;
}

.chart-container {
    background: white;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    margin-bottom: 25px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.chart-container:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.chart-title {
    font-size: 18px;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f0f0f0;
}

.chart-title i {
    font-size: 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.trend-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
}

.trend-up {
    background: #e6f7ed;
    color: #0ca750;
}

.trend-down {
    background: #fee;
    color: #e53e3e;
}

.stat-subtitle {
    color: #6b7280;
    font-size: 11px;
    margin-top: auto;
    display: flex;
    align-items: center;
    gap: 4px;
    font-weight: 500;
}

.stat-subtitle i {
    font-size: 13px;
}

/* Icon colors */
.icon-green {
    background: #d1fae5;
    color: #10b981;
}

.icon-purple {
    background: #e9d5ff;
    color: #a855f7;
}

.icon-blue {
    background: #dbeafe;
    color: #3b82f6;
}

.icon-orange {
    background: #fed7aa;
    color: #f59e0b;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card, .chart-container {
    animation: fadeInUp 0.6s ease-out;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

.loading-spinner {
    display: inline-block;
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 8px;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.table-hover tbody tr {
    transition: all 0.3s ease;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.table thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
    border: none;
    padding: 15px;
}

.table tbody td {
    vertical-align: middle;
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
}
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold mb-2" style="color: #1a202c; font-size: 32px;">
                <i class='bx bx-line-chart me-2' style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Analytics & Statistics
            </h2>
            <p class="text-muted mb-0" style="font-size: 15px;">Subscription revenue and performance metrics</p>
        </div>
        <a href="{{ route('admin.author-transactions') }}" class="btn btn-outline-primary" style="border-radius: 12px; padding: 10px 24px; font-weight: 600; border-width: 2px;">
            <i class='bx bx-arrow-back me-2'></i>Back to Transactions
        </a>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="text-center py-5">
        <div class="loading-spinner mx-auto mb-3"></div>
        <p class="text-muted">Loading analytics data...</p>
    </div>

    <!-- Analytics Content -->
    <div id="analyticsContent" style="display: none;">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper icon-green">
                        <i class='bx bx-dollar-circle'></i>
                    </div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value" id="totalRevenue">$0.00</div>
                    <div class="stat-subtitle">
                        <i class='bx bx-trending-up' style="color: #10b981;"></i>
                        <span>+0%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper icon-purple">
                        <i class='bx bx-trending-up'></i>
                    </div>
                    <div class="stat-label">This Month</div>
                    <div class="stat-value" id="monthRevenue">$0.00</div>
                    <div class="stat-subtitle" id="monthDate">
                        <i class='bx bx-calendar' style="color: #a855f7;"></i>
                        <span>October 2025</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper icon-blue">
                        <i class='bx bx-receipt'></i>
                    </div>
                    <div class="stat-label">Transactions</div>
                    <div class="stat-value" id="totalTransactions">0</div>
                    <div class="stat-subtitle">
                        <i class='bx bx-time' style="color: #3b82f6;"></i>
                        <span>All time</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper icon-orange">
                        <i class='bx bx-bar-chart-alt-2'></i>
                    </div>
                    <div class="stat-label">Avg. Transaction</div>
                    <div class="stat-value" id="avgTransaction">$0.00</div>
                    <div class="stat-subtitle">
                        <i class='bx bx-bar-chart' style="color: #f59e0b;"></i>
                        <span>Average</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Revenue Trend Chart -->
            <div class="col-md-8 mb-4">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class='bx bx-line-chart'></i>
                        Revenue Trend (Last 6 Months)
                    </div>
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>

            <!-- Revenue by Plan Chart -->
            <div class="col-md-4 mb-4">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class='bx bx-pie-chart-alt-2'></i>
                        Revenue by Plan
                    </div>
                    <canvas id="planChart" height="200"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let revenueChart, planChart;

// Load analytics data
async function loadAnalytics() {
    try {
        const response = await fetch('{{ route("admin.transactions.analytics") }}?period=6');
        const data = await response.json();
        
        if (data.success) {
            updateStats(data.stats);
            createRevenueChart(data.revenue);
            createPlanChart(data.plans);
            
            // Hide loading, show content
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('analyticsContent').style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading analytics:', error);
        document.getElementById('loadingState').innerHTML = `
            <div class="alert alert-danger">
                <i class='bx bx-error-circle me-2'></i>
                Error loading analytics data. Please try again.
            </div>
        `;
    }
}

function updateStats(stats) {
    document.getElementById('totalRevenue').textContent = '$' + stats.totalRevenue.toFixed(2);
    document.getElementById('monthRevenue').textContent = '$' + stats.monthRevenue.toFixed(2);
    document.getElementById('totalTransactions').textContent = stats.totalTransactions;
    document.getElementById('avgTransaction').textContent = '$' + stats.avgTransaction.toFixed(2);
    
    // Update month date
    const now = new Date();
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"];
    document.getElementById('monthDate').innerHTML = `
        <i class='bx bx-calendar'></i>
        <span>${monthNames[now.getMonth()]} ${now.getFullYear()}</span>
    `;
}

function createRevenueChart(revenue) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    if (revenueChart) {
        revenueChart.destroy();
    }
    
    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenue.labels,
            datasets: [{
                label: 'Revenue ($)',
                data: revenue.values,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: $' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function createPlanChart(plans) {
    const ctx = document.getElementById('planChart').getContext('2d');
    
    if (planChart) {
        planChart.destroy();
    }
    
    const colors = [
        '#667eea',
        '#f093fb',
        '#4facfe',
        '#43e97b',
        '#fa709a',
        '#fee140'
    ];
    
    planChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: plans.labels,
            datasets: [{
                data: plans.values,
                backgroundColor: colors.slice(0, plans.labels.length),
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: $${value.toFixed(2)} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

// Load data on page load
document.addEventListener('DOMContentLoaded', loadAnalytics);
</script>
@endsection
