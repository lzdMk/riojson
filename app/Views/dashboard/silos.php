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
        
        .file-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .file-card:hover {
            background: rgba(30, 41, 59, 0.8);
            border-color: rgba(59, 130, 246, 0.3);
            transform: translateY(-2px);
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
            
            .stats-card {
                margin-bottom: 16px;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            .navbar .d-flex span {
                display: none;
            }
            
            .file-table-responsive {
                font-size: 0.875rem;
            }
            
            .btn-group .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #3b82f6, #1d4ed8);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #1d4ed8, #1e40af);
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
                                <?php if ($is_admin): ?>
                                <a class="nav-link text-danger" href="<?= base_url('admin/live-requests') ?>">
                                    <i class="bi bi-activity me-2"></i>Live Requests
                                </a>
                                <?php endif; ?>
                                <hr class="border-secondary">
                            </div>
                        <?php endif; ?>
                        
                        <!-- Regular Navigation -->
                        <a class="nav-link active" href="<?= base_url('dashboard/silos') ?>">
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
                            <h2 class="mb-1"><i class="bi bi-files text-primary me-2"></i>My JSON Silos</h2>
                            <p class="text-muted mb-0">Manage your JSON files and data</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-light btn-sm" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise me-1"></i><span class="d-none d-sm-inline">Refresh</span>
                            </button>
                            <a href="<?= base_url('dashboard/silos/create') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i><span class="d-none d-sm-inline">Create </span>Silo
                            </a>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle p-2 me-3">
                                        <i class="bi bi-files text-white"></i>
                                    </div>
                                    <div>
                                        <div class="h4 mb-0"><?= $totalFiles ?></div>
                                        <small class="text-muted">Total Files</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle p-2 me-3">
                                        <i class="bi bi-hdd text-white"></i>
                                    </div>
                                    <div>
                                        <div class="h4 mb-0"><?= $storage_used ?></div>
                                        <small class="text-muted">Storage Used</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info rounded-circle p-2 me-3">
                                        <i class="bi bi-globe text-white"></i>
                                    </div>
                                    <div>
                                        <div class="h4 mb-0">Unlimited</div>
                                        <small class="text-muted">Bandwidth</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search Bar -->
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control bg-dark border-secondary text-light" 
                                   placeholder="Search your silos..." id="searchFiles">
                        </div>
                    </div>

                    <!-- Files List -->
                    <?php if (empty($jsonFiles)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-files text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3 text-muted">No JSON silos yet</h4>
                            <p class="text-muted mb-4">Create your first JSON silo to get started</p>
                            <a href="<?= base_url('dashboard/silos/create') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i>Create Your First Silo
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive file-table-responsive">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Size</th>
                                        <th>Created</th>
                                        <th>Last Updated</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($jsonFiles as $file): ?>
                                    <tr class="file-row">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-code text-warning me-2"></i>
                                                <div>
                                                    <div class="fw-medium"><?= esc($file['original_filename'] ?: 'Untitled') ?></div>
                                                    <small class="text-muted">ID: <?= $file['id'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= $file['formatted_size'] ?></small>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= date('M j, Y', strtotime($file['uploaded_at'])) ?></small>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= date('M j, Y g:i A', strtotime($file['uploaded_at'])) ?></small>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('dashboard/silos/view/' . $file['id']) ?>" 
                                                   class="btn btn-outline-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('dashboard/silos/edit/' . $file['id']) ?>" 
                                                   class="btn btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?= base_url('dashboard/silos/download/' . $file['id']) ?>" 
                                                   class="btn btn-outline-success" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <button class="btn btn-outline-danger" 
                                                        onclick="deleteFile('<?= $file['id'] ?>')" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // Simple search functionality
        $('#searchFiles').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('.file-row').each(function() {
                var fileName = $(this).find('.fw-medium').text().toLowerCase();
                if (fileName.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Delete file function
        function deleteFile(fileId) {
            if (confirm('Are you sure you want to delete this JSON silo? This action cannot be undone.')) {
                $.ajax({
                    url: '<?= base_url('dashboard/silos/delete/') ?>' + fileId,
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
                        alert('An error occurred while deleting the file.');
                    }
                });
            }
        }

        // Show success/error messages
        <?php if (session()->getFlashdata('message')): ?>
            alert('<?= session()->getFlashdata('message') ?>');
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            alert('Error: <?= session()->getFlashdata('error') ?>');
        <?php endif; ?>
        
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
