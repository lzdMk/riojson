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
        .main-content {
            background: rgba(30, 41, 59, 0.3);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .stats-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .admin-badge {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .user-type-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .user-type-free { background: #059669; color: white; }
        .user-type-paid { background: #d97706; color: white; }
        .user-type-admin { background: #dc2626; color: white; }
        .file-item {
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .file-item:hover {
            background: rgba(30, 41, 59, 0.6);
            border-color: rgba(59, 130, 246, 0.3);
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
                <a href="<?= base_url('admin') ?>" class="btn btn-outline-danger btn-sm me-2">
                    <i class="bi bi-arrow-left me-1"></i>Back to Admin
                </a>
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="main-content p-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-2">
                        <i class="bi bi-person-circle text-primary me-2"></i>
                        User Details
                    </h1>
                    <p class="text-muted mb-0">Complete information for user account</p>
                </div>
                <span class="user-type-badge user-type-<?= $user['user_type'] ?>">
                    <?= strtoupper($user['user_type']) ?> USER
                </span>
            </div>

            <!-- User Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="stats-card p-4">
                        <h5 class="mb-3">
                            <i class="bi bi-person-badge me-2"></i>Account Information
                        </h5>
                        <div class="row">
                            <div class="col-sm-4"><strong>User ID:</strong></div>
                            <div class="col-sm-8"><code><?= $user['user_id'] ?></code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-sm-4"><strong>Email:</strong></div>
                            <div class="col-sm-8"><?= esc($user['email']) ?></div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-sm-4"><strong>User Type:</strong></div>
                            <div class="col-sm-8">
                                <span class="user-type-badge user-type-<?= $user['user_type'] ?>">
                                    <?= strtoupper($user['user_type']) ?>
                                </span>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-sm-4"><strong>Status:</strong></div>
                            <div class="col-sm-8">
                                <span class="badge <?= $user['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                    <i class="bi bi-circle-fill me-1"></i>
                                    <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-sm-4"><strong>Created:</strong></div>
                            <div class="col-sm-8"><?= date('F j, Y \a\t g:i A', strtotime($user['created_at'])) ?></div>
                        </div>
                        <?php if ($user['last_login_at']): ?>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-sm-4"><strong>Last Login:</strong></div>
                            <div class="col-sm-8"><?= date('F j, Y \a\t g:i A', strtotime($user['last_login_at'])) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="stats-card p-4">
                        <h5 class="mb-3">
                            <i class="bi bi-bar-chart me-2"></i>Usage Statistics
                        </h5>
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="h3 text-info mb-1"><?= $stats['file_count'] ?></div>
                                <small class="text-muted">Files</small>
                            </div>
                            <div class="col-4">
                                <div class="h3 text-warning mb-1"><?= $stats['api_key_count'] ?></div>
                                <small class="text-muted">API Keys</small>
                            </div>
                            <div class="col-4">
                                <div class="h3 text-success mb-1"><?= $stats['storage_mb'] ?>MB</div>
                                <small class="text-muted">Storage Used</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>File Limit:</span>
                                <span><?= $user['max_files'] >= 999999 ? 'Unlimited' : $user['max_files'] ?></span>
                            </div>
                            <?php if ($user['max_files'] >= 999999): ?>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: 100%">Unlimited</div>
                                </div>
                            <?php else: ?>
                                <?php $filePercent = $user['max_files'] > 0 ? round(($stats['file_count'] / $user['max_files']) * 100, 1) : 0; ?>
                                <div class="progress">
                                    <div class="progress-bar bg-info" style="width: <?= min($filePercent, 100) ?>%">
                                        <?= $filePercent ?>%
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Storage Limit:</span>
                                <span><?= $user['max_storage_mb'] >= 999999 ? 'Unlimited' : $user['max_storage_mb'] . 'MB' ?></span>
                            </div>
                            <?php if ($user['max_storage_mb'] >= 999999): ?>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: 100%">Unlimited</div>
                                </div>
                            <?php else: ?>
                                <?php $storagePercent = $user['max_storage_mb'] > 0 ? round(($stats['storage_mb'] / $user['max_storage_mb']) * 100, 1) : 0; ?>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" style="width: <?= min($storagePercent, 100) ?>%">
                                        <?= $storagePercent ?>%
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Files -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="stats-card p-4">
                        <h5 class="mb-3">
                            <i class="bi bi-files me-2"></i>
                            JSON Files (<?= count($files) ?>)
                        </h5>
                        
                        <?php if (!empty($files)): ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover">
                                    <thead>
                                        <tr>
                                            <th>File ID</th>
                                            <th>Filename</th>
                                            <th>Size</th>
                                            <th>Uploaded</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($files as $file): ?>
                                        <tr>
                                            <td><code><?= esc($file['id']) ?></code></td>
                                            <td><?= esc($file['original_filename'] ?? 'Untitled') ?></td>
                                            <td><?= number_format(strlen($file['json_content']) / 1024, 2) ?> KB</td>
                                            <td><?= date('M j, Y', strtotime($file['uploaded_at'])) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('dashboard/silos/view/' . $file['id']) ?>" 
                                                       class="btn btn-outline-primary btn-sm" target="_blank">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <button class="btn btn-outline-danger btn-sm" 
                                                            onclick="deleteFile('<?= $file['id'] ?>', '<?= esc($file['original_filename'] ?? 'Untitled') ?>')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-files display-4 text-muted"></i>
                                <p class="text-muted mt-2">No files uploaded yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- API Keys -->
            <div class="row">
                <div class="col-12">
                    <div class="stats-card p-4">
                        <h5 class="mb-3">
                            <i class="bi bi-key me-2"></i>
                            API Keys (<?= count($apiKeys) ?>)
                        </h5>
                        
                        <?php if (!empty($apiKeys)): ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover">
                                    <thead>
                                        <tr>
                                            <th>Key Name</th>
                                            <th>API Key</th>
                                            <th>Domain Lock</th>
                                            <th>Created</th>
                                            <th>Last Used</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($apiKeys as $key): ?>
                                        <tr>
                                            <td><?= esc($key['key_name']) ?></td>
                                            <td><code><?= $key['api_key'] ?></code></td>
                                            <td>
                                                <?php if (isset($key['domain_lock_enabled']) && $key['domain_lock_enabled'] && isset($key['allowed_domains']) && !empty($key['allowed_domains'])): ?>
                                                    <?php 
                                                    $domains = json_decode($key['allowed_domains'], true);
                                                    if (is_array($domains) && !empty($domains)): ?>
                                                        <span class="badge bg-warning"><?= esc(implode(', ', $domains)) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">No restriction</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No restriction</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M j, Y', strtotime($key['created_at'])) ?></td>
                                            <td>
                                                <?php if (isset($key['last_used_at']) && !empty($key['last_used_at'])): ?>
                                                    <?= date('M j, Y H:i', strtotime($key['last_used_at'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Never used</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-key display-4 text-muted"></i>
                                <p class="text-muted mt-2">No API keys created yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteFile(fileId, title) {
            if (confirm(`Are you sure you want to delete "${title}"?`)) {
                fetch('<?= base_url('admin/deleteUserFile') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'file_id=' + encodeURIComponent(fileId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the file');
                });
            }
        }
    </script>
</body>
</html>
