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
            
            .col-mobile-full {
                margin-left: 0 !important;
                padding-left: 15px !important;
            }
        }
        
        .nav-link {
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .nav-link:hover {
            background: rgba(59, 130, 246, 0.1);
            color: rgba(59, 130, 246, 1);
        }
        
        .nav-link.active {
            background: rgba(59, 130, 246, 0.2);
            color: rgba(59, 130, 246, 1);
        }
        
        /* Custom modal - positioned more to the left */
        .custom-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: transparent;
            z-index: 9999;
            pointer-events: none;
        }
        
        .custom-modal.show {
            display: flex;
            align-items: center;
            justify-content: flex-start; /* Change from center to flex-start for left positioning */
            padding-left: 25%; /* Add left padding to position modal more to the left */
            pointer-events: auto;
        }
        
        .custom-modal-content {
            background: rgba(30, 41, 59, 0.98);
            border: 1px solid rgba(59, 130, 246, 0.4);
            border-radius: 16px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6), 0 10px 20px rgba(0, 0, 0, 0.4);
            animation: modalSlideIn 0.3s ease-out;
            position: relative;
            pointer-events: auto;
            margin: 0;
        }
        
        /* Responsive sizing and positioning */
        @media (max-width: 767px) {
            .custom-modal.show {
                justify-content: center; /* Center on mobile */
                padding-left: 0;
            }
            .custom-modal-content {
                width: 95%;
                max-width: 400px;
            }
        }
        
        @media (min-width: 768px) {
            .custom-modal-content {
                width: 85%;
                max-width: 500px;
            }
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .custom-modal-header {
            background: rgba(15, 23, 42, 0.9);
            border-bottom: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 16px 16px 0 0;
            padding: 1.5rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .custom-modal-body {
            background: rgba(30, 41, 59, 0.8);
            padding: 1.5rem;
            color: #e2e8f0;
        }
        
        .custom-modal-footer {
            background: rgba(15, 23, 42, 0.9);
            border-top: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 0 0 16px 16px;
            padding: 1.5rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }
        
        .custom-modal-title {
            color: #e2e8f0;
            font-weight: 600;
            margin: 0;
            flex-grow: 1;
        }
        
        .custom-modal-close {
            background: none;
            border: none;
            color: #e2e8f0;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.8;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .custom-modal-close:hover {
            opacity: 1;
        }
        
        /* Ensure sidebar doesn't interfere with modal */
        .sidebar {
            z-index: 1040;
        }
        
        .sidebar-overlay {
            z-index: 1045;
        }
        
        /* Fix any overlay conflicts */
        .sidebar-overlay.show {
            display: block;
            z-index: 1045;
        }
        
        .main-content {
            background: rgba(30, 41, 59, 0.3);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .stats-card {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        .api-key-card {
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .api-key-card:hover {
            border-color: rgba(59, 130, 246, 0.3);
            transform: translateY(-2px);
        }
        
        .key-value {
            font-family: 'Courier New', monospace;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .copy-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .form-control, .form-select {
            background: rgba(30, 41, 59, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #e2e8f0 !important;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(30, 41, 59, 0.9) !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25) !important;
            color: #e2e8f0 !important;
        }
        
        .alert-info {
            background: rgba(59, 130, 246, 0.1) !important;
            border: 1px solid rgba(59, 130, 246, 0.3) !important;
            color: #93c5fd !important;
        }
        
        .alert-success {
            background: rgba(34, 197, 94, 0.1) !important;
            border: 1px solid rgba(34, 197, 94, 0.3) !important;
            color: #86efac !important;
        }
        
        .alert-warning {
            background: rgba(245, 158, 11, 0.1) !important;
            border: 1px solid rgba(245, 158, 11, 0.3) !important;
            color: #fcd34d !important;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #3b82f6, #1d4ed8) !important;
            border: none !important;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #1d4ed8, #1e40af) !important;
        }
        
        .btn-secondary {
            background: rgba(75, 85, 99, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #e2e8f0 !important;
        }
        
        .btn-secondary:hover {
            background: rgba(75, 85, 99, 1) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-active {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        .status-inactive {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .api-endpoint {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
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
        
        .mobile-menu-btn {
            display: none;
        }
        
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: inline-block;
            }
            
            .main-content {
                margin: 16px 8px !important;
                padding: 16px !important;
            }
            
            .col-mobile-full {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }
            
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .api-key-card {
                margin-bottom: 1rem;
            }
            
            .key-value {
                font-size: 0.8rem;
                word-break: break-all;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            .navbar .d-flex span {
                display: none;
            }
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
                    Welcome, <?= esc(session()->get('email')) ?>
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
                                <?php if ($is_admin): ?>
                                <a class="nav-link text-danger" href="<?= base_url('admin/live-requests') ?>">
                                    <i class="bi bi-activity me-2"></i>Live Requests
                                </a>
                                <?php endif; ?>
                                <hr class="border-secondary">
                            </div>
                        <?php endif; ?>
                        
                        <!-- Regular Navigation -->
                        <a class="nav-link" href="<?= base_url('dashboard/silos') ?>">
                            <i class="bi bi-files me-2"></i>My Silos
                        </a>
                        <a class="nav-link active" href="<?= base_url('dashboard/api') ?>">
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
                            <h2 class="mb-1"><i class="bi bi-key text-warning me-2"></i>API Management</h2>
                            <p class="text-muted mb-0">Manage your API keys and access documentation</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?= base_url('dashboard/api/docs') ?>" class="btn btn-info btn-sm">
                                <i class="bi bi-book me-1"></i><span class="d-none d-sm-inline">Full API Docs</span>
                            </a>
                            <button type="button" class="btn btn-primary btn-sm" onclick="showCreateModal()">
                                <i class="bi bi-plus-lg me-1"></i><span class="d-none d-sm-inline">New API Key</span>
                            </button>
                        </div>
                    </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card p-3 text-center">
                        <i class="bi bi-key-fill text-primary fs-1 mb-2"></i>
                        <h4 class="mb-1"><?= $stats['active'] ?></h4>
                        <small class="text-muted">Active Keys</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card p-3 text-center">
                        <i class="bi bi-clock-fill text-success fs-1 mb-2"></i>
                        <h4 class="mb-1"><?= $stats['recently_used'] ?></h4>
                        <small class="text-muted">Used (7 days)</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card p-3 text-center">
                        <i class="bi bi-graph-up text-info fs-1 mb-2"></i>
                        <h4 class="mb-1"><?= $stats['total'] ?></h4>
                        <small class="text-muted">Total Created</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card p-3 text-center">
                        <i class="bi bi-shield-check text-warning fs-1 mb-2"></i>
                        <h4 class="mb-1"><?= $rate_limit_display ?></h4>
                        <small class="text-muted">Rate Limit</small>
                        <?php if ($user_type === 'admin'): ?>
                            <div class="mt-1">
                                <span class="badge bg-danger">Unlimited Access</span>
                            </div>
                        <?php elseif ($user_type === 'paid'): ?>
                            <div class="mt-1">
                                <small class="text-info">Daily: <?= $rate_limits['daily_limit'] ?></small>
                            </div>
                        <?php else: ?>
                            <div class="mt-1">
                                <small class="text-muted">Daily: <?= $rate_limits['daily_limit'] ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- User ID Information -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="stats-card p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-info">
                                    <i class="bi bi-person-badge me-1"></i>Your User ID: <code class="text-warning"><?= session()->get('user_id') ?></code>
                                </small>
                            </div>
                            <div>
                                <small class="text-muted">Use this ID in API documentation examples</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Keys List - PRIORITIZED -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-key-fill text-warning me-2"></i>Your API Keys</h5>
                <small class="text-muted"><?= count($apiKeys) ?> of 10 keys used</small>
            </div>

            <?php if (empty($apiKeys)): ?>
                <div class="text-center py-5 mb-4" style="background: rgba(30, 41, 59, 0.4); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <i class="bi bi-key text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">No API Keys Yet</h4>
                    <p class="text-muted mb-4">Create your first API key to start accessing your JSON data programmatically</p>
                    <button type="button" class="btn btn-primary" onclick="showCreateModal()">
                        <i class="bi bi-plus me-2"></i>Create Your First API Key
                    </button>
                </div>
            <?php else: ?>
                <div class="row g-3 mb-4">
                    <?php foreach ($apiKeys as $key): ?>
                        <div class="col-lg-6">
                            <div class="api-key-card p-3">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold"><?= esc($key['key_name']) ?></h6>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Created <?= date('M j, Y', strtotime($key['created_at'])) ?>
                                        </small>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark">
                                            <li><a class="dropdown-item" href="#" onclick="copyToClipboard('<?= esc($key['api_key']) ?>')">
                                                <i class="bi bi-clipboard me-2"></i>Copy Key
                                            </a></li>
                                            <li><a class="dropdown-item" href="<?= base_url('dashboard/api/details/' . $key['id']) ?>">
                                                <i class="bi bi-info-circle me-2"></i>View Details
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="#" onclick="manageDomainLock(<?= $key['id'] ?>, '<?= esc($key['key_name']) ?>', <?= isset($key['domain_lock_enabled']) && $key['domain_lock_enabled'] ? 'true' : 'false' ?>, '<?= isset($key['allowed_domains']) ? esc($key['allowed_domains']) : '' ?>')">
                                                <i class="bi bi-shield-lock me-2"></i>Domain Lock
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="confirmRevoke(<?= $key['id'] ?>, '<?= esc($key['key_name']) ?>')">
                                                <i class="bi bi-trash me-2"></i>Revoke Key
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small text-muted">API Key:</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control key-value" value="<?= str_repeat('*', strlen($key['api_key']) - 8) . substr($key['api_key'], -8) ?>" readonly data-full-key="<?= esc($key['api_key']) ?>">
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('<?= esc($key['api_key']) ?>')" title="Copy full API key">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                        <button class="btn btn-outline-info" type="button" onclick="toggleApiKeyVisibility(this)" title="Show/hide full key">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <?php if (isset($key['domain_lock_enabled']) && $key['domain_lock_enabled'] && !empty($key['allowed_domains'])): ?>
                                    <div class="mt-2">
                                        <small class="text-muted d-block">Allowed Domains:</small>
                                        <div class="small">
                                            <?php 
                                            $domains = json_decode($key['allowed_domains'], true);
                                            if (is_array($domains)):
                                                foreach ($domains as $domain): ?>
                                                    <span class="badge bg-secondary me-1 mb-1">
                                                        <i class="bi bi-globe2 me-1"></i><?= esc($domain) ?>
                                                    </span>
                                                <?php endforeach;
                                            endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-<?= $key['is_active'] ? 'success' : 'secondary' ?>">
                                            <i class="bi bi-<?= $key['is_active'] ? 'check-circle' : 'x-circle' ?> me-1"></i>
                                            <?= $key['is_active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                        <?php if (isset($key['domain_lock_enabled']) && $key['domain_lock_enabled']): ?>
                                            <span class="badge bg-warning text-dark" title="Domain restrictions enabled">
                                                <i class="bi bi-shield-lock me-1"></i>Domain Lock
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">
                                        <?php if ($key['last_used_at']): ?>
                                            Last used <?= date('M j, Y', strtotime($key['last_used_at'])) ?>
                                        <?php else: ?>
                                            Never used
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Quick API Reference - SECONDARY PRIORITY -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5><i class="bi bi-book text-info me-2"></i>Quick API Reference</h5>
                        <a href="<?= base_url('dashboard/api/docs') ?>" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-book me-1"></i>Full Documentation
                        </a>
                    </div>
                    
                    <div class="alert alert-info d-flex align-items-center mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <div>
                            <strong>Base URL:</strong> <code><?= base_url('api/v1') ?></code>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <div class="api-endpoint p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-success me-2">GET</span>
                                    <strong>List Files</strong>
                                </div>
                                <code class="d-block mb-2 text-primary">/api/v1/{user_id}</code>
                                <small class="text-muted">Get all your JSON files</small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="api-endpoint p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-success me-2">GET</span>
                                    <strong>Get File</strong>
                                </div>
                                <code class="d-block mb-2 text-primary">/api/v1/{user_id}/{file_id}</code>
                                <small class="text-muted">Get specific file with metadata</small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="api-endpoint p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-success me-2">GET</span>
                                    <strong>Raw JSON</strong>
                                </div>
                                <code class="d-block mb-2 text-primary">/api/v1/{user_id}/{file_id}/raw</code>
                                <small class="text-muted">Get raw JSON content only</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 p-3" style="background: rgba(59, 130, 246, 0.1); border-radius: 8px; border: 1px solid rgba(59, 130, 246, 0.3);">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-shield-check text-info me-2"></i>
                            <strong>Authentication</strong>
                        </div>
                        <code class="small">Authorization: Bearer your-api-key</code>
                        <span class="d-block small text-muted mt-1">Include this header in all API requests</span>
                    </div>
                </div>
            </div>

            <!-- Custom Create API Key Modal -->
            <div class="custom-modal" id="createKeyModal">
                <div class="custom-modal-content">
                    <div class="custom-modal-header">
                        <h5 class="custom-modal-title">
                            <i class="bi bi-key text-warning me-2"></i>Create New API Key
                        </h5>
                        <button type="button" class="custom-modal-close" onclick="hideCreateModal()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form id="createKeyForm">
                        <div class="custom-modal-body">
                            <div class="mb-3">
                                <label for="keyName" class="form-label">Key Name</label>
                                <input type="text" class="form-control" id="keyName" name="key_name" 
                                       placeholder="e.g., My App API Key" required>
                                <div class="form-text">Choose a descriptive name to identify this API key</div>
                            </div>
                            
                            <!-- Domain Restriction Section -->
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-shield-lock text-warning me-2"></i>
                                    <label class="form-label mb-0">Domain Restrictions</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="enableDomainLock" name="enable_domain_lock">
                                    <label class="form-check-label" for="enableDomainLock">
                                        Enable domain restrictions for this API key
                                    </label>
                                </div>
                                <div class="form-text">When enabled, this API key will only work from specified domains</div>
                            </div>
                            
                            <!-- Domain List (hidden by default) -->
                            <div class="mb-3" id="domainListSection" style="display: none;">
                                <label for="allowedDomains" class="form-label">
                                    <i class="bi bi-globe me-1"></i>Allowed Domains
                                </label>
                                <textarea class="form-control" id="allowedDomains" name="allowed_domains" rows="3" 
                                          placeholder="localhost&#10;example.com&#10;*.mydomain.com&#10;app.mydomain.com"></textarea>
                                <div class="form-text">
                                    <small>
                                        <strong>One domain per line.</strong> Examples:<br>
                                        • <code>localhost</code> - Local development<br>
                                        • <code>example.com</code> - Specific domain<br>
                                        • <code>*.example.com</code> - All subdomains<br>
                                        • <code>app.example.com</code> - Specific subdomain
                                    </small>
                                </div>
                            </div>
                            
                            <div class="alert alert-info d-flex align-items-start">
                                <i class="bi bi-info-circle me-2 mt-1"></i>
                                <div>
                                    <strong>Important:</strong> Save your API key securely. You won't be able to see it again after creation.
                                </div>
                            </div>
                        </div>
                        <div class="custom-modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="hideCreateModal()">
                                <i class="bi bi-x me-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-key me-1"></i>Create API Key
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Custom New API Key Modal -->
            <div class="custom-modal" id="newKeyModal">
                <div class="custom-modal-content">
                    <div class="custom-modal-header">
                        <h5 class="custom-modal-title">
                            <i class="bi bi-check-circle text-success me-2"></i>New API Key Created
                        </h5>
                        <button type="button" class="custom-modal-close" onclick="hideNewKeyModal()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="custom-modal-body">
                        <div class="alert alert-success d-flex align-items-start">
                            <i class="bi bi-check-circle me-2 mt-1"></i>
                            <div>
                                <strong>API key created successfully!</strong><br>
                                Please copy and save this API key now. You won't be able to see it again.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Your new API key:</label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="newApiKey" readonly>
                                <button class="btn btn-outline-primary" type="button" onclick="copyNewKey()">
                                    <i class="bi bi-clipboard me-1"></i>Copy
                                </button>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning d-flex align-items-start">
                            <i class="bi bi-exclamation-triangle me-2 mt-1"></i>
                            <div>
                                <strong>Important:</strong> Save this key securely. This is the only time you'll see it.
                            </div>
                        </div>
                    </div>
                    <div class="custom-modal-footer">
                        <button type="button" class="btn btn-primary" onclick="hideNewKeyModal()">
                            <i class="bi bi-check me-1"></i>I've Saved the Key
                        </button>
                    </div>
                </div>
            </div>

            <!-- Domain Lock Management Modal -->
            <div class="custom-modal" id="domainLockModal">
                <div class="custom-modal-content">
                    <div class="custom-modal-header">
                        <h5 class="custom-modal-title">
                            <i class="bi bi-shield-lock text-warning me-2"></i>Manage Domain Lock
                        </h5>
                        <button type="button" class="custom-modal-close" onclick="hideDomainLockModal()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form id="domainLockForm">
                        <input type="hidden" id="domainLockKeyId" name="key_id">
                        <div class="custom-modal-body">
                            <div class="alert alert-info d-flex align-items-start">
                                <i class="bi bi-info-circle me-2 mt-1"></i>
                                <div>
                                    <strong>API Key:</strong> <span id="domainLockKeyName"></span><br>
                                    <small>Configure which domains can use this API key</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="domainLockEnabled" name="domain_lock_enabled">
                                    <label class="form-check-label" for="domainLockEnabled">
                                        <i class="bi bi-shield-lock me-1"></i>Enable domain restrictions for this API key
                                    </label>
                                </div>
                                <div class="form-text">When enabled, this API key will only work from specified domains</div>
                            </div>
                            
                            <!-- Domain List Section -->
                            <div class="mb-3" id="domainLockDomainsSection" style="display: none;">
                                <label for="domainLockDomains" class="form-label">
                                    <i class="bi bi-globe me-1"></i>Allowed Domains
                                </label>
                                <textarea class="form-control" id="domainLockDomains" name="allowed_domains" rows="4" 
                                          placeholder="localhost&#10;example.com&#10;*.mydomain.com&#10;app.mydomain.com"></textarea>
                                <div class="form-text">
                                    <small>
                                        <strong>One domain per line.</strong> Examples:<br>
                                        • <code>localhost</code> - Local development<br>
                                        • <code>example.com</code> - Specific domain<br>
                                        • <code>*.example.com</code> - All subdomains<br>
                                        • <code>app.example.com</code> - Specific subdomain
                                    </small>
                                </div>
                            </div>
                            
                            <div class="alert alert-warning d-flex align-items-start">
                                <i class="bi bi-exclamation-triangle me-2 mt-1"></i>
                                <div>
                                    <strong>Warning:</strong> Enabling domain restrictions will immediately block requests from non-allowed domains. Make sure to include all domains you need.
                                </div>
                            </div>
                        </div>
                        <div class="custom-modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="hideDomainLockModal()">
                                <i class="bi bi-x me-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-shield-lock me-1"></i>Update Domain Lock
                            </button>
                        </div>
                    </form>
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
        
        // Custom modal functions
        function showCreateModal() {
            // Close sidebar if open
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            if (sidebar && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
            
            const modal = document.getElementById('createKeyModal');
            modal.classList.add('show');
            // Don't disable body scroll - let user scroll normally while modal is open
        }
        
        function hideCreateModal() {
            const modal = document.getElementById('createKeyModal');
            modal.classList.remove('show');
            // Reset form
            document.getElementById('createKeyForm').reset();
        }
        
        function showNewKeyModal() {
            const modal = document.getElementById('newKeyModal');
            modal.classList.add('show');
            // Don't disable body scroll
        }
        
        function hideNewKeyModal() {
            const modal = document.getElementById('newKeyModal');
            modal.classList.remove('show');
            // Reload page to show the new key in the list
            location.reload();
        }
        
        // Domain Lock Management
        function manageDomainLock(keyId, keyName, isEnabled, allowedDomains) {
            // Close sidebar if open
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            if (sidebar && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
            
            // Populate modal with current data
            document.getElementById('domainLockKeyId').value = keyId;
            document.getElementById('domainLockKeyName').textContent = keyName;
            document.getElementById('domainLockEnabled').checked = isEnabled;
            
            // Parse and display current domains
            let domainsText = '';
            if (allowedDomains && allowedDomains !== '') {
                try {
                    const domains = JSON.parse(allowedDomains);
                    if (Array.isArray(domains)) {
                        domainsText = domains.join('\n');
                    }
                } catch (e) {
                    console.warn('Failed to parse allowed domains:', e);
                }
            }
            document.getElementById('domainLockDomains').value = domainsText;
            
            // Show/hide domains section based on current state
            const domainsSection = document.getElementById('domainLockDomainsSection');
            const domainsTextarea = document.getElementById('domainLockDomains');
            if (isEnabled) {
                domainsSection.style.display = 'block';
                domainsTextarea.required = true;
            } else {
                domainsSection.style.display = 'none';
                domainsTextarea.required = false;
            }
            
            // Show modal
            const modal = document.getElementById('domainLockModal');
            modal.classList.add('show');
        }
        
        function hideDomainLockModal() {
            const modal = document.getElementById('domainLockModal');
            modal.classList.remove('show');
        }
        
        // Toggle domain lock domains section
        document.getElementById('domainLockEnabled').addEventListener('change', function() {
            const domainsSection = document.getElementById('domainLockDomainsSection');
            const domainsTextarea = document.getElementById('domainLockDomains');
            
            if (this.checked) {
                domainsSection.style.display = 'block';
                domainsTextarea.required = true;
                // Add current domain as default if empty
                if (!domainsTextarea.value.trim()) {
                    domainsTextarea.value = window.location.hostname;
                }
            } else {
                domainsSection.style.display = 'none';
                domainsTextarea.required = false;
            }
        });
        
        // Create new API key
        $('#createKeyForm').submit(function(e) {
            e.preventDefault();
            
            const keyName = $('#keyName').val().trim();
            if (!keyName) {
                alert('Please enter a key name');
                return;
            }
            
            const enableDomainLock = $('#enableDomainLock').is(':checked');
            let allowedDomains = [];
            
            if (enableDomainLock) {
                const domainsText = $('#allowedDomains').val().trim();
                if (!domainsText) {
                    alert('Please enter at least one allowed domain');
                    return;
                }
                
                allowedDomains = domainsText.split('\n')
                    .map(domain => domain.trim())
                    .filter(domain => domain.length > 0);
                
                if (allowedDomains.length === 0) {
                    alert('Please enter at least one valid domain');
                    return;
                }
            }
            
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="spinner-border spinner-border-sm me-1"></i>Creating...').prop('disabled', true);
            
            $.ajax({
                url: '<?= base_url('dashboard/api/create') ?>',
                type: 'POST',
                data: { 
                    key_name: keyName,
                    enable_domain_lock: enableDomainLock,
                    allowed_domains: allowedDomains
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Hide create modal
                        hideCreateModal();
                        
                        // Show the new API key
                        $('#newApiKey').val(response.data.api_key);
                        showNewKeyModal();
                        
                        // Reset form
                        $('#createKeyForm')[0].reset();
                        $('#domainListSection').hide();
                        $('#allowedDomains').prop('required', false);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while creating the API key');
                },
                complete: function() {
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });
        
        // Update domain lock settings
        $('#domainLockForm').submit(function(e) {
            e.preventDefault();
            
            const keyId = $('#domainLockKeyId').val();
            const enabled = $('#domainLockEnabled').is(':checked');
            let allowedDomains = [];
            
            if (enabled) {
                const domainsText = $('#domainLockDomains').val().trim();
                if (!domainsText) {
                    alert('Please enter at least one allowed domain');
                    return;
                }
                
                allowedDomains = domainsText.split('\n')
                    .map(domain => domain.trim())
                    .filter(domain => domain.length > 0);
                
                if (allowedDomains.length === 0) {
                    alert('Please enter at least one valid domain');
                    return;
                }
            }
            
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="spinner-border spinner-border-sm me-1"></i>Updating...').prop('disabled', true);
            
            $.ajax({
                url: '<?= base_url('dashboard/api/update-domain-lock') ?>',
                type: 'POST',
                data: {
                    key_id: keyId,
                    enable_domain_lock: enabled,
                    allowed_domains: allowedDomains
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Hide modal
                        hideDomainLockModal();
                        
                        // Show success message
                        const toast = document.createElement('div');
                        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
                        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
                        toast.innerHTML = `
                            <div class="d-flex">
                                <div class="toast-body">
                                    <i class="bi bi-check-circle me-2"></i>Domain lock settings updated successfully!
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                            </div>
                        `;
                        document.body.appendChild(toast);
                        
                        const bsToast = new bootstrap.Toast(toast);
                        bsToast.show();
                        
                        // Remove toast after it's hidden
                        toast.addEventListener('hidden.bs.toast', function() {
                            document.body.removeChild(toast);
                        });
                        
                        // Reload page to show updated API key
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        alert('Error: ' + response.message);
                    }
                    submitBtn.html(originalText).prop('disabled', false);
                },
                error: function() {
                    alert('An error occurred while updating domain lock settings');
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });
        
        // Revoke API key
        function revokeKey(keyId, keyName) {
            if (confirm(`Are you sure you want to revoke the API key "${keyName}"? This action cannot be undone.`)) {
                $.ajax({
                    url: '<?= base_url('dashboard/api/revoke/') ?>' + keyId,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while revoking the API key');
                    }
                });
            }
        }
        
        // Copy new API key to clipboard
        function copyNewKey() {
            const input = document.getElementById('newApiKey');
            const button = document.querySelector('#newKeyModal .btn-outline-primary');
            
            input.select();
            input.setSelectionRange(0, 99999);
            
            navigator.clipboard.writeText(input.value).then(function() {
                const icon = button.querySelector('i');
                const originalText = button.innerHTML;
                
                button.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-success');
                
                setTimeout(function() {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-primary');
                }, 2000);
            }).catch(function() {
                alert('Failed to copy to clipboard');
            });
        }
        
        // Copy to clipboard - simplified and fixed
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success feedback
                const toast = document.createElement('div');
                toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-check-circle me-2"></i>API key copied to clipboard!
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                document.body.appendChild(toast);
                
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
                
                // Remove toast after it's hidden
                toast.addEventListener('hidden.bs.toast', function() {
                    document.body.removeChild(toast);
                });
            }).catch(function() {
                alert('Failed to copy to clipboard');
            });
        }
        
        // Confirm revoke function
        function confirmRevoke(keyId, keyName) {
            if (confirm(`Are you sure you want to revoke the API key "${keyName}"? This action cannot be undone.`)) {
                revokeKey(keyId, keyName);
            }
        }
        
        // Toggle API key visibility
        function toggleApiKeyVisibility(button) {
            const inputGroup = button.closest('.input-group');
            const input = inputGroup.querySelector('.key-value');
            const icon = button.querySelector('i');
            const fullKey = input.getAttribute('data-full-key');
            
            if (icon.classList.contains('bi-eye')) {
                // Show full key
                input.value = fullKey;
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
                button.title = 'Hide full key';
            } else {
                // Hide key (show masked version)
                const maskedKey = '*'.repeat(fullKey.length - 8) + fullKey.slice(-8);
                input.value = maskedKey;
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
                button.title = 'Show full key';
            }
        }
        
        // Toggle domain list visibility
        document.getElementById('enableDomainLock').addEventListener('change', function() {
            const domainSection = document.getElementById('domainListSection');
            const domainTextarea = document.getElementById('allowedDomains');
            
            if (this.checked) {
                domainSection.style.display = 'block';
                domainTextarea.required = true;
                // Add current domain as default
                if (!domainTextarea.value.trim()) {
                    domainTextarea.value = window.location.hostname;
                }
            } else {
                domainSection.style.display = 'none';
                domainTextarea.required = false;
                domainTextarea.value = '';
            }
        });
        
        // Handle escape key for modals
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const createModal = document.getElementById('createKeyModal');
                const newKeyModal = document.getElementById('newKeyModal');
                const domainLockModal = document.getElementById('domainLockModal');
                
                if (createModal && createModal.classList.contains('show')) {
                    hideCreateModal();
                } else if (newKeyModal && newKeyModal.classList.contains('show')) {
                    hideNewKeyModal();
                } else if (domainLockModal && domainLockModal.classList.contains('show')) {
                    hideDomainLockModal();
                }
            }
        });
    </script>
</body>
</html>
