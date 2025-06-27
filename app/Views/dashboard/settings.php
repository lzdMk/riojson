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
        
        .settings-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .settings-card:hover {
            background: rgba(30, 41, 59, 0.8);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        .form-control, .form-select {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(30, 41, 59, 0.9);
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
            color: #e2e8f0;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #3b82f6, #1d4ed8);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #1d4ed8, #1e40af);
        }
        
        .btn-danger {
            background: linear-gradient(45deg, #dc2626, #b91c1c);
            border: none;
        }
        
        .btn-danger:hover {
            background: linear-gradient(45deg, #b91c1c, #991b1b);
        }
        
        .danger-zone {
            border: 1px solid #dc2626;
            border-radius: 12px;
            background: rgba(220, 38, 38, 0.1);
        }
        
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
            
            .settings-card {
                margin-bottom: 20px;
            }
            
            .row > .col-lg-6 {
                padding-left: 8px;
                padding-right: 8px;
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
                                <a class="nav-link text-danger" href="<?= base_url('admin/live-requests') ?>">
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
                        <a class="nav-link active" href="<?= base_url('dashboard/settings') ?>">
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
                            <h2 class="mb-1"><i class="bi bi-gear text-primary me-2"></i>Account Settings</h2>
                            <p class="text-muted mb-0">Manage your account preferences and security</p>
                        </div>
                        <a href="<?= base_url('dashboard/silos') ?>" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i><span class="d-none d-sm-inline">Back to Dashboard</span>
                        </a>
                    </div>

                    <!-- Account Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle p-2 me-3">
                                        <i class="bi bi-person text-white"></i>
                                    </div>
                                    <div>
                                        <div class="h6 mb-0">Account</div>
                                        <small class="text-muted" id="accountAge">Loading...</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle p-2 me-3">
                                        <i class="bi bi-files text-white"></i>
                                    </div>
                                    <div>
                                        <div class="h6 mb-0">JSON Files</div>
                                        <small class="text-muted" id="totalFiles">Loading...</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info rounded-circle p-2 me-3">
                                        <i class="bi bi-hdd text-white"></i>
                                    </div>
                                    <div>
                                        <div class="h6 mb-0">Storage Used</div>
                                        <small class="text-muted" id="storageUsed">Loading...</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Sections -->
                    <div class="row">
                        <!-- Change Password -->
                        <div class="col-lg-6 mb-4">
                            <div class="settings-card p-4">
                                <h5 class="mb-3">
                                    <i class="bi bi-lock text-warning me-2"></i>Change Password
                                </h5>
                                <form id="changePasswordForm">
                                    <div class="mb-3">
                                        <label for="currentPassword" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="newPassword" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                        <div class="form-text">Must be at least 8 characters long</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirmNewPassword" name="confirm_password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>Change Password
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Change Email -->
                        <div class="col-lg-6 mb-4">
                            <div class="settings-card p-4">
                                <h5 class="mb-3">
                                    <i class="bi bi-envelope text-info me-2"></i>Change Email Address
                                </h5>
                                <form id="changeEmailForm">
                                    <div class="mb-3">
                                        <label class="form-label">Current Email</label>
                                        <input type="email" class="form-control" value="<?= esc($user_email) ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="newEmail" class="form-label">New Email Address</label>
                                        <input type="email" class="form-control" id="newEmail" name="new_email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmNewEmail" class="form-label">Confirm New Email</label>
                                        <input type="email" class="form-control" id="confirmNewEmail" name="confirm_email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="emailPassword" class="form-label">Your Password</label>
                                        <input type="password" class="form-control" id="emailPassword" name="password" required>
                                        <div class="form-text">Enter your password to confirm this change</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>Change Email
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="danger-zone p-4 mt-4">
                        <h5 class="text-danger mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>Danger Zone
                        </h5>
                        <p class="text-muted mb-3">
                            Once you delete your account, there is no going back. This will permanently delete your account and all your JSON files.
                        </p>
                        
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="bi bi-trash me-1"></i>Delete My Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header border-danger">
                    <h5 class="modal-title text-danger" id="deleteAccountModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Delete Account Forever
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action cannot be undone. This will permanently delete your account and all your JSON files.
                    </div>
                    
                    <form id="deleteAccountForm">
                        <div class="mb-3">
                            <label for="deletePassword" class="form-label">Enter your password to confirm</label>
                            <input type="password" class="form-control" id="deletePassword" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmText" class="form-label">Type "delete my account forever" to confirm</label>
                            <input type="text" class="form-control" id="confirmText" name="confirm_text" 
                                   placeholder="delete my account forever" required>
                        </div>
                        <div class="text-muted small mb-3">
                            This will delete:
                            <ul class="mt-2">
                                <li>Your user account</li>
                                <li>All your JSON files</li>
                                <li>All account settings</li>
                                <li>All associated data</li>
                            </ul>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-danger">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="deleteAccount()">
                        <i class="bi bi-trash me-1"></i>Delete Account Forever
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // Load account stats
        function loadStats() {
            $.ajax({
                url: '<?= base_url('dashboard/settings/stats') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#totalFiles').text(response.stats.total_files + ' files');
                        $('#storageUsed').text(response.stats.storage_used);
                        
                        // Calculate account age
                        const created = new Date(response.stats.account_created);
                        const now = new Date();
                        const diffTime = Math.abs(now - created);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        $('#accountAge').text(diffDays + ' days old');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load account stats:', error);
                    $('#totalFiles').text('Unable to load');
                    $('#storageUsed').text('Unable to load');
                    $('#accountAge').text('Unable to load');
                    
                    // Retry after 3 seconds
                    setTimeout(function() {
                        loadStats();
                    }, 3000);
                }
            });
        }

        // Change password form
        $('#changePasswordForm').submit(function(e) {
            e.preventDefault();
            
            const currentPassword = $('#currentPassword').val();
            const newPassword = $('#newPassword').val();
            const confirmPassword = $('#confirmNewPassword').val();
            
            if (!currentPassword || !newPassword || !confirmPassword) {
                alert('Please fill in all fields');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                alert('New passwords do not match');
                return;
            }
            
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="spinner-border spinner-border-sm me-1"></i>Changing...').prop('disabled', true);
            
            $.ajax({
                url: '<?= base_url('dashboard/settings/change-password') ?>',
                type: 'POST',
                data: {
                    current_password: currentPassword,
                    new_password: newPassword,
                    confirm_password: confirmPassword
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#changePasswordForm')[0].reset();
                    } else {
                        alert('Error: ' + response.message);
                    }
                    submitBtn.html(originalText).prop('disabled', false);
                },
                error: function() {
                    alert('An error occurred while changing password');
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Change email form
        $('#changeEmailForm').submit(function(e) {
            e.preventDefault();
            
            const newEmail = $('#newEmail').val();
            const confirmEmail = $('#confirmNewEmail').val();
            const password = $('#emailPassword').val();
            
            if (!newEmail || !confirmEmail || !password) {
                alert('Please fill in all fields');
                return;
            }
            
            if (newEmail !== confirmEmail) {
                alert('Email addresses do not match');
                return;
            }
            
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="spinner-border spinner-border-sm me-1"></i>Changing...').prop('disabled', true);
            
            $.ajax({
                url: '<?= base_url('dashboard/settings/change-email') ?>',
                type: 'POST',
                data: {
                    new_email: newEmail,
                    confirm_email: confirmEmail,
                    password: password
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload(); // Reload to update email in navbar
                    } else {
                        alert('Error: ' + response.message);
                    }
                    submitBtn.html(originalText).prop('disabled', false);
                },
                error: function() {
                    alert('An error occurred while changing email');
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Delete account function
        function deleteAccount() {
            const password = $('#deletePassword').val();
            const confirmText = $('#confirmText').val();
            
            if (!password || !confirmText) {
                alert('Please fill in all fields');
                return;
            }
            
            if (confirmText.toLowerCase().trim() !== 'delete my account forever') {
                alert('Please type exactly: "delete my account forever"');
                return;
            }
            
            if (!confirm('Are you absolutely sure you want to delete your account forever? This cannot be undone!')) {
                return;
            }
            
            $.ajax({
                url: '<?= base_url('dashboard/settings/delete-account') ?>',
                type: 'POST',
                data: {
                    password: password,
                    confirm_text: confirmText
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting account');
                }
            });
        }

        // Load stats when page loads
        $(document).ready(function() {
            loadStats();
        });
        
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
