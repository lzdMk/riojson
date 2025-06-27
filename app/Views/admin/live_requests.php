<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - RioConsoleJSON</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #111827 100%);
            min-height: 100vh;
        }
        .admin-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .main-content {
            background: rgba(30, 41, 59, 0.3);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem;
        }
        .request-item {
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }
        .request-item:hover {
            border-color: rgba(59, 130, 246, 0.5);
            transform: translateX(2px);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        .user-type-badge {
            font-size: 0.75rem;
        }
    </style>
</head>
<body class="bg-dark text-light">
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0 text-white">
                        <i class="bi bi-activity me-2"></i>Live Request Monitor
                    </h1>
                    <p class="mb-0 text-light opacity-75">Real-time API request monitoring and analytics</p>
                </div>
                <div>
                    <a href="<?= base_url('admin') ?>" class="btn btn-outline-light me-2">
                        <i class="bi bi-arrow-left me-1"></i>Back to Admin
                    </a>
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-light me-2">
                        <i class="bi bi-house me-1"></i>Dashboard
                    </a>
                    <a href="<?= base_url('auth/logout') ?>" class="btn btn-light">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="main-content">
            <!-- Control Panel -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="bi bi-broadcast-pin me-2"></i>Live API Requests</h4>
                <div class="d-flex gap-2">
                    <span class="badge bg-success" id="status-badge">
                        <i class="bi bi-circle-fill me-1"></i>Live
                    </span>
                    <button class="btn btn-primary btn-sm" onclick="toggleAutoRefresh()">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        <span id="refresh-text">Auto Refresh: ON</span>
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="clearRequests()">
                        <i class="bi bi-trash me-1"></i>Clear
                    </button>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-dark border-secondary">
                        <div class="card-body text-center">
                            <h5 class="text-primary" id="total-requests">0</h5>
                            <small class="text-muted">Total Requests (24h)</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark border-secondary">
                        <div class="card-body text-center">
                            <h5 class="text-success" id="free-requests">0</h5>
                            <small class="text-muted">Free User Requests</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark border-secondary">
                        <div class="card-body text-center">
                            <h5 class="text-warning" id="paid-requests">0</h5>
                            <small class="text-muted">Paid User Requests</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark border-secondary">
                        <div class="card-body text-center">
                            <h5 class="text-danger" id="admin-requests">0</h5>
                            <small class="text-muted">Admin Requests</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Request List -->
            <div class="card bg-dark border-secondary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recent API Requests</h6>
                    <small class="text-muted">Last 100 requests (24h)</small>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    <div id="requests-container">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-hourglass-split fs-3"></i>
                            <p>Loading requests...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let autoRefresh = true;
        let refreshInterval;

        // Start monitoring when page loads
        document.addEventListener('DOMContentLoaded', function() {
            fetchRequests();
            startAutoRefresh();
        });

        function startAutoRefresh() {
            if (refreshInterval) clearInterval(refreshInterval);
            refreshInterval = setInterval(fetchRequests, 5000); // Refresh every 5 seconds
        }

        function toggleAutoRefresh() {
            autoRefresh = !autoRefresh;
            const refreshText = document.getElementById('refresh-text');
            const statusBadge = document.getElementById('status-badge');
            
            if (autoRefresh) {
                refreshText.textContent = 'Auto Refresh: ON';
                statusBadge.className = 'badge bg-success';
                statusBadge.innerHTML = '<i class="bi bi-circle-fill me-1"></i>Live';
                startAutoRefresh();
            } else {
                refreshText.textContent = 'Auto Refresh: OFF';
                statusBadge.className = 'badge bg-secondary';
                statusBadge.innerHTML = '<i class="bi bi-pause-circle me-1"></i>Paused';
                clearInterval(refreshInterval);
            }
        }

        async function fetchRequests() {
            if (!autoRefresh) return;

            try {
                const response = await fetch('<?= base_url('admin/live-requests/data') ?>');
                const data = await response.json();
                
                if (data.success) {
                    updateRequestsList(data.requests);
                    updateStats(data);
                } else {
                    console.error('Failed to fetch requests:', data.message);
                }
            } catch (error) {
                console.error('Error fetching requests:', error);
            }
        }

        function updateStats(data) {
            // Update individual stats based on user types in the requests
            let stats = {
                total: data.requests.length,
                free: 0,
                paid: 0,
                admin: 0
            };

            data.requests.forEach(request => {
                if (request.user_type) {
                    stats[request.user_type] = (stats[request.user_type] || 0) + 1;
                }
            });

            document.getElementById('total-requests').textContent = stats.total;
            document.getElementById('free-requests').textContent = stats.free;
            document.getElementById('paid-requests').textContent = stats.paid;
            document.getElementById('admin-requests').textContent = stats.admin;
        }

        function updateRequestsList(requests) {
            const container = document.getElementById('requests-container');
            
            if (requests.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3"></i>
                        <p>No recent requests in the last 24 hours</p>
                    </div>
                `;
                return;
            }

            const requestsHtml = requests.map(request => {
                const userTypeBadge = getUserTypeBadge(request.user_type);
                const statusBadge = getStatusBadge(request.response_code);
                const timeAgo = getTimeAgo(request.request_time);
                
                return `
                    <div class="request-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <strong class="text-primary me-2">${request.method || 'GET'}</strong>
                                    <code class="text-light">${request.endpoint || 'N/A'}</code>
                                    ${statusBadge}
                                </div>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bi bi-person me-1"></i>
                                    <span class="me-3">${request.user_id || 'Anonymous'}</span>
                                    ${userTypeBadge}
                                    <span class="ms-3">
                                        <i class="bi bi-globe me-1"></i>${request.ip_address || 'N/A'}
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">${timeAgo}</small>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            container.innerHTML = requestsHtml;
        }

        function getUserTypeBadge(userType) {
            const badges = {
                'free': '<span class="badge bg-success user-type-badge">Free</span>',
                'paid': '<span class="badge bg-warning user-type-badge">Paid</span>',
                'admin': '<span class="badge bg-danger user-type-badge">Admin</span>'
            };
            return badges[userType] || '<span class="badge bg-secondary user-type-badge">Unknown</span>';
        }

        function getStatusBadge(code) {
            if (!code) return '<span class="badge bg-secondary status-badge">N/A</span>';
            
            if (code >= 200 && code < 300) {
                return `<span class="badge bg-success status-badge">${code}</span>`;
            } else if (code >= 400 && code < 500) {
                return `<span class="badge bg-warning status-badge">${code}</span>`;
            } else if (code >= 500) {
                return `<span class="badge bg-danger status-badge">${code}</span>`;
            }
            return `<span class="badge bg-info status-badge">${code}</span>`;
        }

        function getTimeAgo(timestamp) {
            const now = new Date();
            const requestTime = new Date(timestamp);
            const diffInSeconds = Math.floor((now - requestTime) / 1000);
            
            if (diffInSeconds < 60) return `${diffInSeconds}s ago`;
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
            return `${Math.floor(diffInSeconds / 86400)}d ago`;
        }

        function clearRequests() {
            document.getElementById('requests-container').innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-3"></i>
                    <p>Request list cleared</p>
                </div>
            `;
            
            // Reset stats
            document.getElementById('total-requests').textContent = '0';
            document.getElementById('free-requests').textContent = '0';
            document.getElementById('paid-requests').textContent = '0';
            document.getElementById('admin-requests').textContent = '0';
        }
    </script>
</body>
</html>
