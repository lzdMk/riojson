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
        .user-card {
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.2s ease;
        }
        .user-card:hover {
            transform: translateY(-2px);
        }
        .main-content {
            background: rgba(30, 41, 59, 0.3);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem;
        }
        .progress {
            height: 8px;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .user-actions .btn {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
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
                        <i class="bi bi-people me-2"></i>User Management
                    </h1>
                    <p class="mb-0 text-light opacity-75">Complete control over all user accounts</p>
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
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="bi bi-people text-primary me-2"></i>User Management</h2>
                    <p class="text-muted mb-0">Complete control over all user accounts</p>
                </div>
                <div>
                    <span class="badge bg-info me-2">Total Users: <?= count($users) ?></span>
                    <button class="btn btn-success btn-sm" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                </div>
            </div>

            <!-- Users List -->
            <div class="row">
                <?php foreach ($users as $user): ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="user-card">
                        <!-- User Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-1">
                                    <code><?= esc($user['user_id']) ?></code>
                                    <span class="badge bg-<?= $user['user_type'] === 'admin' ? 'danger' : ($user['user_type'] === 'paid' ? 'warning' : 'success') ?> ms-2">
                                        <?= esc($user['user_type']) ?>
                                    </span>
                                </h6>
                                <div class="text-muted small"><?= esc($user['email']) ?></div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark">
                                    <li><a class="dropdown-item" href="<?= base_url('admin/user/' . $user['user_id']) ?>">
                                        <i class="bi bi-eye me-2"></i>View Details
                                    </a></li>
                                    <li><button class="dropdown-item" onclick="changeUserType('<?= $user['user_id'] ?>', 'free')">
                                        <i class="bi bi-person me-2"></i>Make Free
                                    </button></li>
                                    <li><button class="dropdown-item" onclick="changeUserType('<?= $user['user_id'] ?>', 'paid')">
                                        <i class="bi bi-star me-2"></i>Make Paid
                                    </button></li>
                                    <?php if ($user['user_type'] !== 'admin'): ?>
                                    <li><button class="dropdown-item" onclick="changeUserType('<?= $user['user_id'] ?>', 'admin')">
                                        <i class="bi bi-shield-check me-2"></i>Make Admin
                                    </button></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><button class="dropdown-item text-danger" onclick="deleteUser('<?= $user['user_id'] ?>', '<?= esc($user['email']) ?>')">
                                        <i class="bi bi-trash me-2"></i>Delete User
                                    </button></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        <!-- User Stats -->
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="h6 text-info mb-0"><?= $user['file_count'] ?></div>
                                <div class="small text-muted">Files</div>
                            </div>
                            <div class="col-4">
                                <div class="h6 text-warning mb-0"><?= $user['api_key_count'] ?></div>
                                <div class="small text-muted">API Keys</div>
                            </div>
                            <div class="col-4">
                                <div class="h6 text-success mb-0"><?= $user['storage_mb'] ?>MB</div>
                                <div class="small text-muted">Storage</div>
                            </div>
                        </div>

                        <!-- Usage Progress -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Files Usage</span>
                                <span><?= $user['max_files'] >= 999999 ? 'Unlimited' : $user['files_percent'] . '%' ?></span>
                            </div>
                            <?php if ($user['max_files'] >= 999999): ?>
                                <div class="progress progress-custom">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                            <?php else: ?>
                                <div class="progress progress-custom">
                                    <div class="progress-bar bg-info" style="width: <?= min($user['files_percent'], 100) ?>%"></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Storage Usage</span>
                                <span><?= $user['max_storage_mb'] >= 999999 ? 'Unlimited' : $user['storage_percent'] . '%' ?></span>
                            </div>
                            <?php if ($user['max_storage_mb'] >= 999999): ?>
                                <div class="progress progress-custom">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                            <?php else: ?>
                                <div class="progress progress-custom">
                                    <div class="progress-bar bg-warning" style="width: <?= min($user['storage_percent'], 100) ?>%"></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Account Info -->
                        <div class="small text-muted">
                            <div><i class="bi bi-calendar me-1"></i>Created: <?= date('M j, Y', strtotime($user['created_at'])) ?></div>
                            <?php if ($user['last_login_at']): ?>
                            <div><i class="bi bi-clock me-1"></i>Last login: <?= date('M j, Y H:i', strtotime($user['last_login_at'])) ?></div>
                            <?php endif; ?>
                            <div><i class="bi bi-circle-fill me-1 <?= $user['is_active'] ? 'text-success' : 'text-danger' ?>"></i>
                                <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($users)): ?>
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h5 class="text-muted mt-3">No users found</h5>
                <p class="text-muted">Users will appear here once they register</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function changeUserType(userId, newType) {
            if (confirm(`Are you sure you want to change this user to ${newType} type?`)) {
                fetch('<?= base_url('admin/updateUserType') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${userId}&user_type=${newType}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User type updated successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Network error occurred');
                });
            }
        }

        function deleteUser(userId, email) {
            if (confirm(`Are you sure you want to delete user ${email}?\n\nThis action cannot be undone and will delete all their files and API keys.`)) {
                fetch('<?= base_url('admin/deleteUser') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${userId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User deleted successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Network error occurred');
                });
            }
        }
    </script>
</body>
</html>
