<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
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
                <span class="text-muted me-3">Welcome, <?= esc($user_email) ?></span>
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                    <h1 class="mt-3 mb-4">Welcome to RioConsoleJSON Dashboard!</h1>
                    <p class="lead text-muted mb-4">You have successfully logged in to your account.</p>
                    <div class="alert alert-info d-inline-block">
                        <i class="bi bi-info-circle me-2"></i>
                        This is a demo dashboard. The full console functionality will be implemented here.
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="row g-4 mt-4">
            <div class="col-md-3">
                <div class="card bg-dark border-secondary">
                    <div class="card-body text-center">
                        <i class="bi bi-files text-primary fs-1 mb-3"></i>
                        <h5 class="card-title">JSON Files</h5>
                        <h3 class="text-primary">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up text-success fs-1 mb-3"></i>
                        <h5 class="card-title">API Requests</h5>
                        <h3 class="text-success">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary">
                    <div class="card-body text-center">
                        <i class="bi bi-hdd text-warning fs-1 mb-3"></i>
                        <h5 class="card-title">Storage Used</h5>
                        <h3 class="text-warning">0 MB</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary">
                    <div class="card-body text-center">
                        <i class="bi bi-clock text-info fs-1 mb-3"></i>
                        <h5 class="card-title">Uptime</h5>
                        <h3 class="text-info">100%</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-dark border-secondary">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-3">
                            <a href="<?= base_url('dashboard/silos/create') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Upload JSON File
                            </a>
                            <a href="<?= base_url('dashboard/api') ?>" class="btn btn-outline-primary">
                                <i class="bi bi-key me-2"></i>Manage API Keys
                            </a>
                            <a href="<?= base_url('dashboard/api/docs') ?>" class="btn btn-outline-success">
                                <i class="bi bi-code me-2"></i>View API Documentation
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
