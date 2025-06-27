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
        .main-card {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        .stat-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        .upload-zone {
            border: 2px dashed rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .upload-zone.dragover {
            border-color: #0d6efd;
            background: rgba(13, 110, 253, 0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center py-3 mb-4">
                    <div>
                        <h1 class="h3 text-white mb-0">
                            <i class="bi bi-database me-2"></i><?= $title ?>
                        </h1>
                        <p class="text-muted mb-0">Export and import your complete database</p>
                    </div>
                    <div>
                        <a href="<?= base_url('admin') ?>" class="btn btn-outline-light">
                            <i class="bi bi-arrow-left me-2"></i>Back to Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="main-card p-4">
                    <h4 class="text-white mb-3">
                        <i class="bi bi-bar-chart me-2"></i>Database Statistics
                    </h4>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <i class="bi bi-server text-primary fs-1 mb-2"></i>
                                <h5 class="text-primary mb-1"><?= $stats['database_name'] ?></h5>
                                <p class="text-muted mb-0">Database</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <i class="bi bi-table text-success fs-1 mb-2"></i>
                                <h5 class="text-success mb-1"><?= number_format($stats['total_tables']) ?></h5>
                                <p class="text-muted mb-0">Tables</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <i class="bi bi-collection text-warning fs-1 mb-2"></i>
                                <h5 class="text-warning mb-1"><?= number_format($stats['total_rows']) ?></h5>
                                <p class="text-muted mb-0">Total Rows</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <i class="bi bi-hdd text-info fs-1 mb-2"></i>
                                <h5 class="text-info mb-1"><?= $stats['total_size_mb'] ?> MB</h5>
                                <p class="text-muted mb-0">Database Size</p>
                            </div>
                        </div>
                    </div>

                    <!-- Table Details -->
                    <h6 class="text-white mb-3">Table Details</h6>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>Table Name</th>
                                    <th>Rows</th>
                                    <th>Size (MB)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['tables'] as $table): ?>
                                <tr>
                                    <td>
                                        <i class="bi bi-table me-2"></i>
                                        <code><?= $table['name'] ?></code>
                                    </td>
                                    <td><?= number_format($table['rows']) ?></td>
                                    <td><?= $table['size_mb'] ?></td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Ready
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup & Import Actions -->
        <div class="row g-4">
            <!-- Export Database -->
            <div class="col-md-6">
                <div class="main-card p-4 h-100">
                    <h4 class="text-white mb-3">
                        <i class="bi bi-download me-2"></i>Export Database
                    </h4>
                    <p class="text-muted mb-4">
                        Download a complete SQL backup of your database including all tables, structure, and data.
                    </p>
                    
                    <div class="d-grid">
                        <button id="downloadBtn" class="btn btn-primary btn-lg" onclick="downloadBackup()">
                            <i class="bi bi-download me-2"></i>
                            <span id="downloadText">Download SQL Backup</span>
                        </button>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            The backup will include all data and can be used to restore your database completely.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Import Database -->
            <div class="col-md-6">
                <div class="main-card p-4 h-100">
                    <h4 class="text-white mb-3">
                        <i class="bi bi-upload me-2"></i>Import Database
                    </h4>
                    <p class="text-muted mb-4">
                        Upload and restore a SQL backup file. This will validate the structure before importing.
                    </p>
                    
                    <form id="importForm" enctype="multipart/form-data">
                        <div class="upload-zone mb-3" id="uploadZone">
                            <i class="bi bi-cloud-upload fs-1 text-muted mb-3"></i>
                            <p class="text-white mb-2">Drop your SQL file here or click to browse</p>
                            <p class="text-muted small mb-3">Maximum file size: 100MB</p>
                            <input type="file" id="sqlFile" name="sql_file" accept=".sql" class="d-none">
                            <button type="button" class="btn btn-outline-light" onclick="document.getElementById('sqlFile').click()">
                                <i class="bi bi-folder2-open me-2"></i>Choose File
                            </button>
                        </div>
                        
                        <div id="fileInfo" class="d-none mb-3">
                            <div class="alert alert-info">
                                <i class="bi bi-file-earmark-text me-2"></i>
                                <span id="fileName"></span>
                                <small id="fileSize" class="d-block text-muted"></small>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" id="importBtn" class="btn btn-warning btn-lg" disabled>
                                <i class="bi bi-upload me-2"></i>
                                <span id="importText">Import Database</span>
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Warning: This will replace all current data. Make sure to backup first!
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Progress -->
        <div id="importProgress" class="row mt-4 d-none">
            <div class="col-12">
                <div class="main-card p-4">
                    <h5 class="text-white mb-3">
                        <i class="bi bi-gear me-2"></i>Import Progress
                    </h5>
                    <div class="progress mb-3" style="height: 10px;">
                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                    </div>
                    <div id="progressText" class="text-muted small">Preparing import...</div>
                    <div id="importResults" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload handling
        const uploadZone = document.getElementById('uploadZone');
        const sqlFile = document.getElementById('sqlFile');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const importBtn = document.getElementById('importBtn');
        const importForm = document.getElementById('importForm');

        // Drag and drop
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });

        sqlFile.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        function handleFileSelect(file) {
            if (file.type !== 'application/sql' && !file.name.endsWith('.sql')) {
                alert('Please select a SQL file');
                return;
            }

            if (file.size > 100 * 1024 * 1024) { // 100MB
                alert('File too large. Maximum size is 100MB');
                return;
            }

            fileName.textContent = file.name;
            fileSize.textContent = `Size: ${(file.size / 1024 / 1024).toFixed(2)} MB`;
            fileInfo.classList.remove('d-none');
            importBtn.disabled = false;
        }

        // Download backup
        function downloadBackup() {
            const btn = document.getElementById('downloadBtn');
            const text = document.getElementById('downloadText');
            
            btn.disabled = true;
            text.textContent = 'Generating backup...';
            
            // Create a temporary link to trigger download
            const link = document.createElement('a');
            link.href = '<?= base_url('backup/download') ?>';
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Reset button after a delay
            setTimeout(() => {
                btn.disabled = false;
                text.textContent = 'Download SQL Backup';
            }, 3000);
        }

        // Import form submission
        importForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!sqlFile.files[0]) {
                alert('Please select a SQL file');
                return;
            }

            const formData = new FormData();
            formData.append('sql_file', sqlFile.files[0]);

            // Show progress
            document.getElementById('importProgress').classList.remove('d-none');
            document.getElementById('progressBar').style.width = '20%';
            document.getElementById('progressText').textContent = 'Validating SQL file...';

            importBtn.disabled = true;
            document.getElementById('importText').textContent = 'Importing...';

            try {
                const response = await fetch('<?= base_url('backup/import') ?>', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                // Update progress
                document.getElementById('progressBar').style.width = '100%';

                if (result.success) {
                    document.getElementById('progressText').textContent = 'Import completed successfully!';
                    document.getElementById('importResults').innerHTML = `
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Success!</strong> ${result.message}
                            <br><small>Executed ${result.executed_statements} of ${result.total_statements} statements</small>
                            ${result.errors && result.errors.length > 0 ? 
                                '<br><small class="text-warning">Some non-critical errors occurred during import</small>' : ''}
                        </div>
                    `;
                    
                    // Refresh page after successful import
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    document.getElementById('progressText').textContent = 'Import failed';
                    document.getElementById('importResults').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Error:</strong> ${result.message}
                        </div>
                    `;
                }
            } catch (error) {
                document.getElementById('progressText').textContent = 'Import failed';
                document.getElementById('importResults').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> ${error.message}
                    </div>
                `;
            } finally {
                importBtn.disabled = false;
                document.getElementById('importText').textContent = 'Import Database';
            }
        });
    </script>
</body>
</html>
