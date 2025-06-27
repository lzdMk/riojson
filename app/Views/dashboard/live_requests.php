<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - RioConsoleJSON</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #111827 100%);
            min-height: 100vh;
        }
        
        .sidebar {
            background: rgba(15, 23, 42, 0.8);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            min-height: calc(100vh - 76px);
            transition: all 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 76px;
                left: -100%;
                width: 280px;
                height: calc(100vh - 76px);
                z-index: 1050;
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                left: 0;
                transform: translateX(0);
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 76px;
                left: 0;
                width: 100%;
                height: calc(100vh - 76px);
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
            
            .main-content {
                margin: 16px 8px !important;
                padding: 16px !important;
            }
            
            .col-mobile-full {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }
        }
        
        .sidebar .nav-link {
            color: #94a3b8;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 12px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        
        .main-content {
            background: rgba(30, 41, 59, 0.3);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .live-indicator {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .request-row {
            transition: all 0.3s ease;
        }
        
        .request-row:hover {
            background: rgba(59, 130, 246, 0.1);
        }
        
        .status-success { color: #10b981; }
        .status-error { color: #ef4444; }
        .status-warning { color: #f59e0b; }
        
        .stats-card {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
        }
        
        .mobile-menu-btn {
            display: none;
        }
        
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: inline-block;
            }
            
            .stats-card {
                margin-bottom: 16px;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            .navbar .d-flex span {
                display: none;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #3b82f6, #1d4ed8);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #1d4ed8, #1e40af);
        }
        
        .rate-limit-badge {
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
    </style>
</head>
<body class="bg-dark text-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary">
        <div class="container-fluid">
            <button class="mobile-menu-btn btn btn-outline-light btn-sm me-2" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand fw-bold" href="<?= base_url() ?>">
                <i class="bi bi-file-earmark-code text-primary me-2"></i>
                RioConsoleJSON
            </a>
            
            <div class="d-flex align-items-center">
                <span class="text-muted me-3">
                    Welcome, <?= esc($user_email) ?>
                    <?php if (isset($is_admin) && $is_admin): ?>
                        <span class="badge bg-danger ms-2">
                            <i class="bi bi-shield-check me-1"></i>ADMIN
                        </span>
                    <?php elseif (isset($user_type)): ?>
                        <?php if ($user_type === 'paid'): ?>
                            <span class="badge bg-success ms-2">
                                <i class="bi bi-star-fill me-1"></i>PAID
                            </span>
                        <?php elseif ($user_type === 'free'): ?>
                            <span class="badge bg-secondary ms-2">
                                <i class="bi bi-person me-1"></i>FREE
                            </span>
                        <?php endif; ?>
                    <?php endif; ?>
                </span>
                
                <!-- Admin Quick Access -->
                <?php if (isset($is_admin) && $is_admin): ?>
                    <a href="<?= base_url('admin') ?>" class="btn btn-outline-danger btn-sm me-2">
                        <i class="bi bi-shield-check me-1"></i><span class="d-none d-sm-inline">Admin Panel</span>
                    </a>
                <?php endif; ?>
                
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i><span class="d-none d-sm-inline">Logout</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar" id="sidebar">
                <div class="py-4">
                    <nav class="nav flex-column">
                        <!-- Admin Section (Only visible to admins) -->
                        <?php if (isset($is_admin) && $is_admin): ?>
                            <div class="mb-3">
                                <h6 class="text-danger mb-2 px-3">
                                    <i class="bi bi-shield-check me-2"></i>Admin Controls
                                </h6>
                                <a class="nav-link text-danger" href="<?= base_url('admin') ?>">
                                    <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
                                </a>
                                <a class="nav-link text-danger" href="<?= base_url('admin/users') ?>">
                                    <i class="bi bi-people me-2"></i>Manage Users
                                </a>
                                <a class="nav-link text-danger active" href="<?= base_url('admin/live-requests') ?>">
                                    <i class="bi bi-activity me-2"></i>Live Requests
                                </a>
                                <hr class="border-secondary">
                            </div>
                        <?php endif; ?>
                        
                        <!-- Regular Navigation -->
                        <a class="nav-link" href="<?= base_url('dashboard/silos') ?>">
                            <i class="bi bi-files me-2"></i>My Silos
                        </a>
                        <a class="nav-link" href="<?= base_url('dashboard/api') ?>">
                            <i class="bi bi-key me-2"></i>API Keys
                        </a>
                        <a class="nav-link" href="<?= base_url('dashboard/api/docs') ?>">
                            <i class="bi bi-book me-2"></i>API Docs
                        </a>
                        <a class="nav-link" href="<?= base_url('dashboard/settings') ?>">
                            <i class="bi bi-gear me-2"></i>Settings
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 col-mobile-full">
                <div class="main-content p-4 m-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <h2 class="mb-1">
                                <i class="bi bi-activity text-danger me-2"></i>Live Request Monitor
                                <span class="badge bg-success live-indicator ms-2">
                                    <i class="bi bi-circle-fill me-1"></i>LIVE
                                </span>
                            </h2>
                            <p class="text-muted mb-0">Real-time monitoring of API requests from all users</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-light btn-sm" onclick="refreshData()">
                                <i class="bi bi-arrow-clockwise me-1"></i><span class="d-none d-sm-inline">Refresh</span>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="toggleAutoRefresh()" id="autoRefreshBtn">
                                <i class="bi bi-pause-circle me-1"></i><span class="d-none d-sm-inline">Pause Auto-Refresh</span>
                            </button>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="row mb-4" id="statsContainer">
                        <div class="col-md-2 col-6">
                            <div class="stats-card p-3 text-center">
                                <div class="h4 mb-0 text-primary" id="totalRequests">-</div>
                                <small class="text-muted">Total Today</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="stats-card p-3 text-center">
                                <div class="h4 mb-0 text-secondary" id="freeRequests">-</div>
                                <small class="text-muted">Free Users</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="stats-card p-3 text-center">
                                <div class="h4 mb-0 text-success" id="paidRequests">-</div>
                                <small class="text-muted">Paid Users</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="stats-card p-3 text-center">
                                <div class="h4 mb-0 text-danger" id="adminRequests">-</div>
                                <small class="text-muted">Admin Users</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="stats-card p-3 text-center">
                                <div class="h4 mb-0 text-warning" id="rateLimited">-</div>
                                <small class="text-muted">Rate Limited</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="stats-card p-3 text-center">
                                <div class="h4 mb-0 text-info" id="avgResponseTime">-</div>
                                <small class="text-muted">Avg Response</small>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <select class="form-select bg-dark text-light border-secondary" id="userTypeFilter">
                                <option value="">All User Types</option>
                                <option value="free">Free Users</option>
                                <option value="paid">Paid Users</option>
                                <option value="admin">Admin Users</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select bg-dark text-light border-secondary" id="statusFilter">
                                <option value="">All Status Codes</option>
                                <option value="2xx">Success (2xx)</option>
                                <option value="4xx">Client Error (4xx)</option>
                                <option value="5xx">Server Error (5xx)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="rateLimitFilter">
                                <label class="form-check-label" for="rateLimitFilter">
                                    Show only rate-limited requests
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Live Requests Table -->
                    <div class="card bg-dark border-secondary">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-list-ul me-2"></i>Recent API Requests
                                <small class="text-muted ms-2">Updates every 5 seconds</small>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-dark table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>User</th>
                                            <th>Type</th>
                                            <th>Endpoint</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th>Response Time</th>
                                            <th>IP</th>
                                            <th>Rate Limited</th>
                                        </tr>
                                    </thead>
                                    <tbody id="requestsTable">
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                <i class="bi bi-hourglass-split me-2"></i>Loading requests...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        let autoRefreshInterval;
        let isAutoRefreshEnabled = true;
        
        $(document).ready(function() {
            // Initial load
            refreshData();
            
            // Start auto-refresh
            startAutoRefresh();
            
            // Filter events
            $('#userTypeFilter, #statusFilter, #rateLimitFilter').on('change', function() {
                filterRequests();
            });
        });
        
        function refreshData() {
            $.ajax({
                url: '<?= base_url('admin/live-requests/data') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        console.error('Error:', response.error);
                        return;
                    }
                    
                    updateStats(response.stats);
                    updateRequestsTable(response.requests);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        }
        
        function updateStats(stats) {
            $('#totalRequests').text(stats.total_requests_today);
            $('#freeRequests').text(stats.free_requests);
            $('#paidRequests').text(stats.paid_requests);
            $('#adminRequests').text(stats.admin_requests);
            $('#rateLimited').text(stats.rate_limited);
            $('#avgResponseTime').text(stats.avg_response_time);
        }
        
        function updateRequestsTable(requests) {
            const tbody = $('#requestsTable');
            tbody.empty();
            
            if (requests.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            <i class="bi bi-inbox me-2"></i>No requests found
                        </td>
                    </tr>
                `);
                return;
            }
            
            requests.forEach(function(request) {
                const userTypeBadge = getUserTypeBadge(request.user_type);
                const statusClass = getStatusClass(request.status);
                const rateLimitBadge = request.rate_limit_hit ? 
                    '<span class="badge bg-danger rate-limit-badge">LIMITED</span>' : 
                    '<span class="badge bg-success">OK</span>';
                
                const row = `
                    <tr class="request-row" data-user-type="${request.user_type}" data-status="${request.status}" data-rate-limited="${request.rate_limit_hit}">
                        <td><small>${formatTime(request.timestamp)}</small></td>
                        <td><small>${request.user_email}</small></td>
                        <td>${userTypeBadge}</td>
                        <td><code>${request.endpoint}</code></td>
                        <td><span class="badge bg-secondary">${request.method}</span></td>
                        <td><span class="badge ${statusClass}">${request.status}</span></td>
                        <td><small>${request.response_time}</small></td>
                        <td><small>${request.ip}</small></td>
                        <td>${rateLimitBadge}</td>
                    </tr>
                `;
                tbody.append(row);
            });
        }
        
        function getUserTypeBadge(userType) {
            switch(userType) {
                case 'admin':
                    return '<span class="badge bg-danger"><i class="bi bi-shield-check me-1"></i>ADMIN</span>';
                case 'paid':
                    return '<span class="badge bg-success"><i class="bi bi-star-fill me-1"></i>PAID</span>';
                case 'free':
                    return '<span class="badge bg-secondary"><i class="bi bi-person me-1"></i>FREE</span>';
                default:
                    return '<span class="badge bg-secondary">UNKNOWN</span>';
            }
        }
        
        function getStatusClass(status) {
            if (status >= 200 && status < 300) return 'bg-success';
            if (status >= 300 && status < 400) return 'bg-info';
            if (status >= 400 && status < 500) return 'bg-warning';
            if (status >= 500) return 'bg-danger';
            return 'bg-secondary';
        }
        
        function formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleString();
        }
        
        function filterRequests() {
            const userTypeFilter = $('#userTypeFilter').val();
            const statusFilter = $('#statusFilter').val();
            const rateLimitFilter = $('#rateLimitFilter').is(':checked');
            
            $('.request-row').each(function() {
                const row = $(this);
                let showRow = true;
                
                // User type filter
                if (userTypeFilter && row.data('user-type') !== userTypeFilter) {
                    showRow = false;
                }
                
                // Status filter
                if (statusFilter) {
                    const status = row.data('status');
                    if (statusFilter === '2xx' && (status < 200 || status >= 300)) showRow = false;
                    if (statusFilter === '4xx' && (status < 400 || status >= 500)) showRow = false;
                    if (statusFilter === '5xx' && status < 500) showRow = false;
                }
                
                // Rate limit filter
                if (rateLimitFilter && !row.data('rate-limited')) {
                    showRow = false;
                }
                
                if (showRow) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        }
        
        function startAutoRefresh() {
            autoRefreshInterval = setInterval(refreshData, 5000); // Refresh every 5 seconds
        }
        
        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        }
        
        function toggleAutoRefresh() {
            const btn = $('#autoRefreshBtn');
            
            if (isAutoRefreshEnabled) {
                stopAutoRefresh();
                btn.html('<i class="bi bi-play-circle me-1"></i><span class="d-none d-sm-inline">Resume Auto-Refresh</span>');
                isAutoRefreshEnabled = false;
            } else {
                startAutoRefresh();
                btn.html('<i class="bi bi-pause-circle me-1"></i><span class="d-none d-sm-inline">Pause Auto-Refresh</span>');
                isAutoRefreshEnabled = true;
            }
        }
        
        // Toggle sidebar for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
        
        // Close sidebar when clicking on a link (mobile)
        $('.sidebar .nav-link').click(function() {
            if (window.innerWidth <= 768) {
                toggleSidebar();
            }
        });
    </script>
</body>
</html>
