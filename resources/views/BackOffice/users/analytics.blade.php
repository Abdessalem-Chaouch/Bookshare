@extends('baseB')

@section('title', 'User Analytics')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">📊 User Analytics Dashboard</h4>
        <p class="text-muted mb-0">Comprehensive insights into user behavior and growth</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block mb-1">Total Users</span>
                            <h3 class="mb-0" id="totalUsers">0</h3>
                            <small class="text-success" id="totalTrend">
                                <i class="bx bx-trending-up"></i> +0%
                            </small>
                        </div>
                        <div class="avatar avatar-lg bg-label-primary">
                            <i class="bx bx-user fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block mb-1">New This Month</span>
                            <h3 class="mb-0" id="newUsers">0</h3>
                            <small class="text-success" id="newTrend">
                                <i class="bx bx-trending-up"></i> +0%
                            </small>
                        </div>
                        <div class="avatar avatar-lg bg-label-success">
                            <i class="bx bx-user-plus fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block mb-1">Active Users</span>
                            <h3 class="mb-0" id="activeUsers">0</h3>
                            <small class="text-info">
                                <i class="bx bx-time"></i> Last 30 days
                            </small>
                        </div>
                        <div class="avatar avatar-lg bg-label-info">
                            <i class="bx bx-check-circle fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block mb-1">Authors</span>
                            <h3 class="mb-0" id="totalAuthors">0</h3>
                            <small class="text-warning">
                                <i class="bx bx-crown"></i> Content Creators
                            </small>
                        </div>
                        <div class="avatar avatar-lg bg-label-warning">
                            <i class="bx bx-edit fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Registration Chart -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📈 User Registrations by Month</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" onclick="changeChartPeriod('12')">12M</button>
                        <button type="button" class="btn btn-outline-primary" onclick="changeChartPeriod('6')">6M</button>
                        <button type="button" class="btn btn-outline-primary" onclick="changeChartPeriod('3')">3M</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="registrationChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Role Distribution -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">🎯 Role Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="roleChart"></canvas>
                    <div class="mt-4" id="roleStats"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Users and Timeline -->
    <div class="row g-4">
        <!-- Top Active Users -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">🏆 Top 10 Active Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>User</th>
                                    <th>Activity Score</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody id="topUsersTable">
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        Loading...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registration Timeline -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">📅 Registration Timeline</h5>
                </div>
                <div class="card-body">
                    <canvas id="timelineChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
let registrationChart, roleChart, timelineChart;

// Load analytics data
document.addEventListener('DOMContentLoaded', function() {
    loadAnalyticsData();
});

function loadAnalyticsData() {
    fetch('{{ route("users.analytics.data") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatCards(data.stats);
                createRegistrationChart(data.registrations);
                createRoleChart(data.roles);
                createTimelineChart(data.timeline);
                updateTopUsers(data.topUsers);
            }
        })
        .catch(error => {
            console.error('Error loading analytics:', error);
        });
}

function updateStatCards(stats) {
    document.getElementById('totalUsers').textContent = stats.total;
    document.getElementById('newUsers').textContent = stats.newThisMonth;
    document.getElementById('activeUsers').textContent = stats.active;
    document.getElementById('totalAuthors').textContent = stats.authors;
    
    document.getElementById('totalTrend').innerHTML = `<i class="bx bx-trending-${stats.totalTrend >= 0 ? 'up' : 'down'}"></i> ${stats.totalTrend >= 0 ? '+' : ''}${stats.totalTrend}%`;
    document.getElementById('newTrend').innerHTML = `<i class="bx bx-trending-${stats.newTrend >= 0 ? 'up' : 'down'}"></i> ${stats.newTrend >= 0 ? '+' : ''}${stats.newTrend}%`;
}

function createRegistrationChart(data) {
    const ctx = document.getElementById('registrationChart').getContext('2d');
    
    if (registrationChart) {
        registrationChart.destroy();
    }
    
    registrationChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'New Users',
                data: data.values,
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
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return 'New Users: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
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

function createRoleChart(data) {
    const ctx = document.getElementById('roleChart').getContext('2d');
    
    if (roleChart) {
        roleChart.destroy();
    }
    
    roleChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.values,
                backgroundColor: [
                    '#667eea',
                    '#28c76f',
                    '#ff9f43'
                ],
                borderWidth: 0
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
                    padding: 12
                }
            }
        }
    });
    
    // Update role stats
    let statsHtml = '';
    data.labels.forEach((label, index) => {
        const percentage = ((data.values[index] / data.total) * 100).toFixed(1);
        const colors = ['#667eea', '#28c76f', '#ff9f43'];
        statsHtml += `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <span class="badge rounded-circle me-2" style="width: 12px; height: 12px; background-color: ${colors[index]};"></span>
                    <span>${label}</span>
                </div>
                <strong>${data.values[index]} (${percentage}%)</strong>
            </div>
        `;
    });
    document.getElementById('roleStats').innerHTML = statsHtml;
}

function createTimelineChart(data) {
    const ctx = document.getElementById('timelineChart').getContext('2d');
    
    if (timelineChart) {
        timelineChart.destroy();
    }
    
    timelineChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Users',
                data: data.values,
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderRadius: 8,
                barThickness: 40
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function updateTopUsers(users) {
    const medals = ['🥇', '🥈', '🥉'];
    let html = '';
    
    users.forEach((user, index) => {
        const maxScore = users[0].activity;
        const percentage = (user.activity / maxScore) * 100;
        
        html += `
            <tr>
                <td>
                    <span class="fs-5">${index < 3 ? medals[index] : (index + 1)}</span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                            ${user.photo ? 
                                `<img src="/uploads/${user.photo}" alt="${user.name}" class="rounded-circle">` :
                                `<span class="avatar-initial rounded-circle bg-label-primary">${user.name.charAt(0)}</span>`
                            }
                        </div>
                        <div>
                            <strong>${user.name}</strong>
                            <br><small class="text-muted">${user.email}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <strong class="text-primary">${user.activity}</strong>
                </td>
                <td style="width: 150px;">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: ${percentage}%"></div>
                    </div>
                </td>
            </tr>
        `;
    });
    
    document.getElementById('topUsersTable').innerHTML = html;
}

function changeChartPeriod(months) {
    // Update active button
    document.querySelectorAll('.btn-group button').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Reload data with new period
    fetch(`{{ route('users.analytics.data') }}?period=${months}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                createRegistrationChart(data.registrations);
            }
        });
}
</script>

<style>
.avatar-lg {
    width: 56px;
    height: 56px;
    font-size: 1.5rem;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.progress {
    background-color: rgba(102, 126, 234, 0.1);
}

.table tbody tr {
    transition: background-color 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
}
</style>

@endsection
