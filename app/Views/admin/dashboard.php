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
        .stats-card {
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #3b82f6;
        }
        .main-content {
            background: rgba(30, 41, 59, 0.3);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem;
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
                        <i class="bi bi-shield-check me-2"></i>Admin Dashboard
                    </h1>
                    <p class="mb-0 text-light opacity-75">Complete system management and user control</p>
                </div>
                <div>
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
            <!-- System Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stats-number"><?= $stats['total_users'] ?></div>
                        <div class="text-muted">Total Users</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stats-number"><?= $stats['total_files'] ?></div>
                        <div class="text-muted">Total Files</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stats-number"><?= $stats['total_api_keys'] ?></div>
                        <div class="text-muted">API Keys</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stats-number"><?= $live_stats['total_requests_24h'] ?></div>
                        <div class="text-muted">Requests (24h)</div>
                    </div>
                </div>
            </div>

            <!-- Live API Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="h4 text-success"><?= $live_stats['successful_requests'] ?></div>
                        <div class="text-muted">Successful</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="h4 text-danger"><?= $live_stats['failed_requests'] ?></div>
                        <div class="text-muted">Failed</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="h4 text-info"><?= $live_stats['success_rate'] ?>%</div>
                        <div class="text-muted">Success Rate</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="h4 text-warning"><?= $stats['user_types']['admin'] ?></div>
                        <div class="text-muted">Admin Users</div>
                    </div>
                </div>
            </div>

            <!-- User Type Distribution -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="text-success mb-0">
                                <i class="bi bi-person me-2"></i>Free Users
                            </h6>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark">
                                    <li><a class="dropdown-item" href="#" onclick="editUserLimits('free')">
                                        <i class="bi bi-pencil-square me-2"></i>Edit Limits
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h4 text-success"><?= $stats['user_types']['free'] ?></span>
                            <small class="text-muted" id="free-limits"><?= $stats['user_limits']['free']['max_files'] ?> files, <?= $stats['user_limits']['free']['max_storage_mb'] ?>MB limit</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="text-warning mb-0">
                                <i class="bi bi-star me-2"></i>Paid Users
                            </h6>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark">
                                    <li><a class="dropdown-item" href="#" onclick="editUserLimits('paid')">
                                        <i class="bi bi-pencil-square me-2"></i>Edit Limits
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h4 text-warning"><?= $stats['user_types']['paid'] ?></span>
                            <small class="text-muted" id="paid-limits"><?= $stats['user_limits']['paid']['max_files'] ?> files, <?= $stats['user_limits']['paid']['max_storage_mb'] ?>MB limit</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <h6 class="text-danger mb-3">
                            <i class="bi bi-shield-check me-2"></i>Admin Users
                        </h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h4 text-danger"><?= $stats['user_types']['admin'] ?></span>
                            <small class="text-muted">Unlimited access</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="mb-3">
                        <i class="bi bi-lightning me-2"></i>Administrative Tools
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <a href="<?= base_url('admin/users') ?>" class="btn btn-primary w-100 p-3">
                                <i class="bi bi-people me-2 fs-5"></i><br>
                                <strong>User Management</strong><br>
                                <small>Manage all user accounts</small>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="<?= base_url('admin/live-requests') ?>" class="btn btn-info w-100 p-3">
                                <i class="bi bi-activity me-2 fs-5"></i><br>
                                <strong>Live Requests</strong><br>
                                <small>Monitor API activity</small>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="<?= base_url('backup') ?>" class="btn btn-danger w-100 p-3">
                                <i class="bi bi-database-gear me-2 fs-5"></i><br>
                                <strong>System Backup</strong><br>
                                <small>Database backup & restore</small>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="<?= base_url('dashboard/api/docs') ?>" class="btn btn-secondary w-100 p-3">
                                <i class="bi bi-book me-2 fs-5"></i><br>
                                <strong>API Documentation</strong><br>
                                <small>View API reference</small>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="<?= base_url('dashboard/api') ?>" class="btn btn-warning w-100 p-3">
                                <i class="bi bi-key me-2 fs-5"></i><br>
                                <strong>API Keys</strong><br>
                                <small>Manage API access</small>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <button class="btn btn-success w-100 p-3" onclick="window.location.reload()">
                                <i class="bi bi-arrow-clockwise me-2 fs-5"></i><br>
                                <strong>Refresh Stats</strong><br>
                                <small>Update dashboard data</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">
                        <i class="bi bi-person-plus me-2"></i>Recent Users
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_users as $user): ?>
                                <tr>
                                    <td>
                                        <code><?= esc($user['user_id']) ?></code>
                                    </td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $user['user_type'] === 'admin' ? 'danger' : ($user['user_type'] === 'paid' ? 'warning' : 'success') ?>">
                                            <?= esc($user['user_type']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('M j, Y', strtotime($user['created_at'])) ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Files -->
                <div class="col-md-6">
                    <h5 class="mb-3">
                        <i class="bi bi-file-earmark-code me-2"></i>Recent Files
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>File ID</th>
                                    <th>Owner</th>
                                    <th>Filename</th>
                                    <th>Uploaded</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_files as $file): ?>
                                <tr>
                                    <td>
                                        <code><?= esc($file['id']) ?></code>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= esc($file['email']) ?></small>
                                    </td>
                                    <td><?= esc($file['original_filename']) ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('M j, H:i', strtotime($file['uploaded_at'])) ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Limits Modal -->
    <div class="modal fade" id="editLimitsModal" tabindex="-1" aria-labelledby="editLimitsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLimitsModalLabel">Edit User Limits</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editLimitsForm">
                        <input type="hidden" id="userTypeInput" name="user_type">
                        
                        <div class="mb-3">
                            <label for="maxFiles" class="form-label">Maximum Files</label>
                            <input type="number" class="form-control bg-dark border-secondary text-light" id="maxFiles" name="max_files" min="1" required>
                            <div class="form-text text-muted">Maximum number of files per user</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="maxStorage" class="form-label">Maximum Storage (MB)</label>
                            <input type="number" class="form-control bg-dark border-secondary text-light" id="maxStorage" name="max_storage_mb" min="1" required>
                            <div class="form-text text-muted">Maximum storage per user in megabytes</div>
                        </div>
                        
                        <div class="alert alert-warning" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This will update limits for all existing <span id="userTypeText"></span> users.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateUserLimits()">
                        <i class="bi bi-check-circle me-2"></i>Update Limits
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let currentUserType = '';
        const userLimits = <?= json_encode($stats['user_limits']) ?>;
        
        function editUserLimits(userType) {
            currentUserType = userType;
            document.getElementById('userTypeInput').value = userType;
            document.getElementById('userTypeText').textContent = userType;
            
            // Set current values from database
            const limits = userLimits[userType];
            document.getElementById('maxFiles').value = limits.max_files;
            document.getElementById('maxStorage').value = limits.max_storage_mb;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editLimitsModal'));
            modal.show();
        }
        
        async function updateUserLimits() {
            const form = document.getElementById('editLimitsForm');
            const formData = new FormData(form);
            
            try {
                const response = await fetch('<?= base_url('admin/updateUserLimits') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Update the display
                    const maxFiles = formData.get('max_files');
                    const maxStorage = formData.get('max_storage_mb');
                    const limitsText = `${maxFiles} files, ${maxStorage}MB limit`;
                    
                    document.getElementById(currentUserType + '-limits').textContent = limitsText;
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editLimitsModal'));
                    modal.hide();
                    
                    // Show success message
                    showAlert('success', result.message);
                } else {
                    showAlert('danger', result.message || 'Failed to update user limits');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while updating user limits');
            }
        }
        
        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
</body>
</html>
