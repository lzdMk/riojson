<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #111827 100%);
            min-height: 100vh;
        }
        .stats-card {
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.2s ease;
        }
        .stats-card:hover {
            transform: translateY(-2px);
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
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="<?= base_url() ?>">
                <i class="bi bi-file-earmark-code text-primary me-2"></i>
                RioConsoleJSON
            </a>
            
            <div class="d-flex align-items-center">
                <span class="text-muted me-3">
                    Welcome, <?= esc($user_email) ?>
                    <?php if ($is_admin): ?>
                        <span class="admin-badge ms-2">
                            <i class="bi bi-shield-check me-1"></i>ADMIN
                        </span>
                    <?php endif; ?>
                </span>
                
                <!-- Admin Quick Access -->
                <?php if ($is_admin): ?>
                    <a href="<?= base_url('admin') ?>" class="btn btn-outline-danger btn-sm me-2">
                        <i class="bi bi-shield-check me-1"></i>Admin Panel
                    </a>
                <?php endif; ?>
                
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="main-content">
            <!-- Welcome Section -->
            <div class="text-center py-4">
                <h1 class="mb-3">
                    <i class="bi bi-house-door text-primary me-2"></i>
                    Dashboard
                </h1>
                <p class="lead text-muted">
                    <?php if ($is_admin): ?>
                        Administrator Dashboard - Full System Control
                    <?php else: ?>
                        Welcome to your RioConsoleJSON account
                    <?php endif; ?>
                </p>
            </div>

            <!-- User Quick Stats -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="bi bi-person-circle text-info fs-1 mb-3"></i>
                        <h5 class="mb-1">Account Type</h5>
                        <span class="badge bg-<?= $user['user_type'] === 'admin' ? 'danger' : ($user['user_type'] === 'paid' ? 'warning' : 'success') ?> fs-6">
                            <?= strtoupper($user['user_type']) ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="bi bi-files text-primary fs-1 mb-3"></i>
                        <h5 class="mb-1">File Limit</h5>
                        <h3 class="text-primary mb-0">
                            <?= $user['max_files'] >= 999999 ? 'Unlimited' : number_format($user['max_files']) ?>
                        </h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="bi bi-hdd text-warning fs-1 mb-3"></i>
                        <h5 class="mb-1">Storage Limit</h5>
                        <h3 class="text-warning mb-0">
                            <?= $user['max_storage_mb'] >= 999999 ? 'Unlimited' : $user['max_storage_mb'] . ' MB' ?>
                        </h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="bi bi-calendar-check text-success fs-1 mb-3"></i>
                        <h5 class="mb-1">Member Since</h5>
                        <p class="text-success mb-0">
                            <?= date('M Y', strtotime($user['created_at'])) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="stats-card">
                        <h5 class="mb-3">
                            <i class="bi bi-lightning me-2"></i>Quick Actions
                        </h5>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="<?= base_url('dashboard/silos') ?>" class="btn btn-primary">
                                <i class="bi bi-files me-2"></i>My JSON Files
                            </a>
                            <a href="<?= base_url('dashboard/silos/create') ?>" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i>Upload New File
                            </a>
                            <a href="<?= base_url('dashboard/api') ?>" class="btn btn-outline-primary">
                                <i class="bi bi-key me-2"></i>Manage API Keys
                            </a>
                            <a href="<?= base_url('dashboard/api/docs') ?>" class="btn btn-outline-success">
                                <i class="bi bi-code me-2"></i>API Documentation
                            </a>
                            <a href="<?= base_url('dashboard/settings') ?>" class="btn btn-outline-info">
                                <i class="bi bi-gear me-2"></i>Account Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
