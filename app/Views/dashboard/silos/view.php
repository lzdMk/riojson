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
        
        .main-content {
            background: rgba(30, 41, 59, 0.3);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .info-card {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
        }
        
        .json-viewer {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.5;
            color: #e2e8f0;
            overflow-x: auto;
            max-height: 600px;
            overflow-y: auto;
        }
        
        .json-viewer pre {
            margin: 0;
            padding: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #3b82f6, #1d4ed8);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #1d4ed8, #1e40af);
        }
        
        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        
        .json-container {
            position: relative;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin: 16px 8px !important;
                padding: 16px !important;
            }
            
            .json-viewer {
                font-size: 12px;
                max-height: 400px;
            }
            
            .navbar .d-flex span {
                display: none;
            }
            
            .btn-sm {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }
            
            .d-flex.justify-content-between > div {
                display: flex;
                justify-content: center;
                gap: 0.5rem;
            }
        }
        
        /* JSON syntax highlighting */
        .json-key { color: #79c0ff; }
        .json-string { color: #a5d6ff; }
        .json-number { color: #79c0ff; }
        .json-boolean { color: #ffab70; }
        .json-null { color: #ffa657; }
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
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="main-content p-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <a href="<?= base_url('dashboard/silos') ?>" class="btn btn-outline-light btn-sm me-3">
                        <i class="bi bi-arrow-left me-1"></i>Back to Silos
                    </a>
                    <div>
                        <h2 class="mb-1">
                            <i class="bi bi-file-earmark-code text-info me-2"></i>
                            <?= esc($silo['original_filename'] ?: 'Untitled Silo') ?>
                        </h2>
                        <p class="text-muted mb-0">JSON Silo Viewer</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" onclick="copyToClipboard()">
                        <i class="bi bi-clipboard me-1"></i>Copy JSON
                    </button>
                    <a href="<?= base_url('dashboard/silos/edit/' . $silo['id']) ?>" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="<?= base_url('dashboard/silos/download/' . $silo['id']) ?>" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-download me-1"></i>Download
                    </a>
                </div>
            </div>

            <!-- File Info -->
            <div class="info-card p-3 mb-4">
                <div class="row">
                    <div class="col-md-2">
                        <small class="text-muted">File ID</small>
                        <div class="fw-medium"><?= $silo['id'] ?></div>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted">Size</small>
                        <div class="fw-medium"><?= $formatted_size ?></div>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted">Created</small>
                        <div class="fw-medium"><?= date('M j, Y', strtotime($silo['uploaded_at'])) ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Last Modified</small>
                        <div class="fw-medium"><?= date('M j, Y g:i A', strtotime($silo['uploaded_at'])) ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">API Access</small>
                        <div class="fw-medium">
                            <a href="<?= base_url('dashboard/api/docs') ?>" class="text-info text-decoration-none">
                                <i class="bi bi-link-45deg me-1"></i>View API Docs
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- JSON Viewer -->
            <div class="json-container">
                <button class="btn btn-outline-secondary btn-sm copy-btn" onclick="copyToClipboard()" title="Copy to clipboard">
                    <i class="bi bi-clipboard"></i>
                </button>
                <div class="json-viewer">
                    <pre id="jsonContent"><?= esc($jsonFormatted) ?></pre>
                </div>
            </div>

            <!-- JSON Stats -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="info-card p-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle p-2 me-3">
                                <i class="bi bi-braces text-white"></i>
                            </div>
                            <div>
                                <div class="h6 mb-0">JSON Type</div>
                                <small class="text-muted" id="jsonType">-</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card p-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success rounded-circle p-2 me-3">
                                <i class="bi bi-list-ul text-white"></i>
                            </div>
                            <div>
                                <div class="h6 mb-0">Properties</div>
                                <small class="text-muted" id="propertyCount">-</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card p-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-info rounded-circle p-2 me-3">
                                <i class="bi bi-layers text-white"></i>
                            </div>
                            <div>
                                <div class="h6 mb-0">Depth</div>
                                <small class="text-muted" id="jsonDepth">-</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-4 text-center">
                <div class="btn-group">
                    <a href="<?= base_url('dashboard/silos/edit/' . $silo['id']) ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit This Silo
                    </a>
                    <a href="<?= base_url('dashboard/silos/create') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-plus-lg me-1"></i>Create New Silo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast for copy feedback -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="copyToast" class="toast bg-success text-white" role="alert">
            <div class="toast-body">
                <i class="bi bi-check-circle me-2"></i>JSON content copied to clipboard!
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // JSON data for analysis
        const jsonData = <?= $silo['json_content'] ?>;

        // Copy to clipboard function
        function copyToClipboard() {
            const jsonText = $('#jsonContent').text();
            navigator.clipboard.writeText(jsonText).then(function() {
                // Show toast
                const toast = new bootstrap.Toast(document.getElementById('copyToast'));
                toast.show();
            }, function() {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = jsonText;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                const toast = new bootstrap.Toast(document.getElementById('copyToast'));
                toast.show();
            });
        }

        // Analyze JSON structure
        function analyzeJson(obj) {
            const stats = {
                type: Array.isArray(obj) ? 'Array' : typeof obj,
                properties: 0,
                depth: 0
            };

            if (typeof obj === 'object' && obj !== null) {
                stats.properties = Object.keys(obj).length;
                stats.depth = getJsonDepth(obj);
            }

            return stats;
        }

        // Get JSON depth
        function getJsonDepth(obj) {
            if (typeof obj !== 'object' || obj === null) return 0;
            
            let maxDepth = 0;
            for (let key in obj) {
                if (obj.hasOwnProperty(key)) {
                    const depth = getJsonDepth(obj[key]);
                    maxDepth = Math.max(maxDepth, depth);
                }
            }
            return maxDepth + 1;
        }

        // Update JSON stats
        function updateStats() {
            try {
                const stats = analyzeJson(jsonData);
                $('#jsonType').text(stats.type);
                $('#propertyCount').text(stats.properties);
                $('#jsonDepth').text(stats.depth + ' levels');
            } catch (error) {
                $('#jsonType').text('Invalid');
                $('#propertyCount').text('-');
                $('#jsonDepth').text('-');
            }
        }

        // Initialize on page load
        $(document).ready(function() {
            updateStats();
            
            // Add syntax highlighting (basic)
            highlightJson();
        });

        // Basic JSON syntax highlighting
        function highlightJson() {
            let content = $('#jsonContent').html();
            
            // Highlight strings
            content = content.replace(/"([^"\\]*(\\.[^"\\]*)*)"/g, '<span class="json-string">"$1"</span>');
            
            // Highlight numbers
            content = content.replace(/: (-?\d+\.?\d*)/g, ': <span class="json-number">$1</span>');
            
            // Highlight booleans
            content = content.replace(/: (true|false)/g, ': <span class="json-boolean">$1</span>');
            
            // Highlight null
            content = content.replace(/: (null)/g, ': <span class="json-null">$1</span>');
            
            $('#jsonContent').html(content);
        }
    </script>
</body>
</html>
