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
        
        .json-editor {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            min-height: 400px;
            max-height: 500px;
            overflow-y: auto;
            resize: vertical;
        }
        
        .info-card {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
        }
        
        .nav-tabs {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .nav-tabs .nav-link {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
            margin-right: 4px;
        }
        
        .nav-tabs .nav-link:hover {
            background: rgba(30, 41, 59, 0.7);
            color: #3b82f6;
        }
        
        .nav-tabs .nav-link.active {
            background: rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
            color: #3b82f6;
        }
        
        .upload-area {
            background: rgba(30, 41, 59, 0.3);
            border: 2px dashed rgba(255, 255, 255, 0.2) !important;
            transition: all 0.3s ease;
            cursor: pointer;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .upload-area:hover {
            border-color: rgba(59, 130, 246, 0.5) !important;
            background: rgba(59, 130, 246, 0.05);
        }
        
        .upload-area.dragover {
            border-color: #3b82f6 !important;
            background: rgba(59, 130, 246, 0.1);
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin: 16px 8px !important;
                padding: 16px !important;
            }
            
            .json-editor {
                font-size: 12px;
                min-height: 300px;
                max-height: 400px;
            }
            
            .d-flex.gap-2.flex-wrap {
                gap: 0.5rem !important;
            }
            
            .btn-sm {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            
            .navbar .d-flex span {
                display: none;
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
                <span class="text-muted me-3 d-none d-md-inline">
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
                    <i class="bi bi-box-arrow-right me-1"></i><span class="d-none d-sm-inline">Logout</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="main-content p-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <a href="<?= base_url('dashboard/silos') ?>" class="btn btn-outline-light btn-sm me-3">
                        <i class="bi bi-arrow-left me-1"></i><span class="d-none d-sm-inline">Back</span>
                    </a>
                    <div>
                        <h2 class="mb-1"><i class="bi bi-pencil text-warning me-2"></i>Edit JSON Silo</h2>
                        <p class="text-muted mb-0 d-none d-md-block">Modify your JSON file content</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('dashboard/silos/view/' . $silo['id']) ?>" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-eye me-1"></i><span class="d-none d-sm-inline">View</span>
                    </a>
                    <a href="<?= base_url('dashboard/silos/download/' . $silo['id']) ?>" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-download me-1"></i><span class="d-none d-sm-inline">Download</span>
                    </a>
                </div>
            </div>

            <!-- File Info -->
            <div class="info-card p-3 mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <small class="text-muted">File ID</small>
                        <div class="fw-medium"><?= $silo['id'] ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Size</small>
                        <div class="fw-medium"><?= $formatted_size ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Created</small>
                        <div class="fw-medium"><?= date('M j, Y', strtotime($silo['uploaded_at'])) ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Last Modified</small>
                        <div class="fw-medium"><?= date('M j, Y g:i A', strtotime($silo['uploaded_at'])) ?></div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form id="editSiloForm">
                <!-- File Name -->
                <div class="mb-4">
                    <label for="filename" class="form-label">Silo Name</label>
                    <input type="text" class="form-control" id="filename" name="filename" 
                           value="<?= esc($silo['original_filename']) ?>" required>
                    <div class="form-text">Give your JSON silo a descriptive name</div>
                </div>

                <!-- JSON Content -->
                <div class="mb-4">
                    <label for="json_content" class="form-label">JSON Content</label>
                    
                    <!-- Content Source Tabs -->
                    <ul class="nav nav-tabs mb-3" id="contentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="manual-tab" data-bs-toggle="tab" 
                                    data-bs-target="#manual-content" type="button" role="tab">
                                <i class="bi bi-pencil me-1"></i>Manual Editor
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="upload-tab" data-bs-toggle="tab" 
                                    data-bs-target="#upload-content" type="button" role="tab">
                                <i class="bi bi-upload me-1"></i>Upload File
                            </button>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content" id="contentTabsContent">
                        <!-- Manual Editor Tab -->
                        <div class="tab-pane fade show active" id="manual-content" role="tabpanel">
                            <textarea class="form-control json-editor" id="json_content" name="json_content" 
                                      rows="20" required><?= esc($jsonFormatted) ?></textarea>
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Edit your JSON content directly in the editor. The editor will validate your JSON syntax.
                            </div>
                        </div>
                        
                        <!-- Upload File Tab -->
                        <div class="tab-pane fade" id="upload-content" role="tabpanel">
                            <div class="upload-area border rounded p-4 text-center" 
                                 ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragenter="dragEnterHandler(event);" ondragleave="dragLeaveHandler(event);">
                                <i class="bi bi-cloud-upload fs-1 text-muted mb-3"></i>
                                <h5>Upload JSON File</h5>
                                <p class="text-muted mb-3">Drag and drop a JSON file here, or click to browse</p>
                                <input type="file" id="jsonFileInput" accept=".json,application/json" style="display: none;" onchange="handleFileSelect(event)">
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('jsonFileInput').click()">
                                    <i class="bi bi-folder2-open me-1"></i>Choose File
                                </button>
                                <div class="form-text mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Upload a .json file to replace the current content. Maximum file size: 10MB
                                </div>
                            </div>
                            <div id="uploadStatus" class="mt-3"></div>
                        </div>
                    </div>
                </div>

                <!-- JSON Tools -->
                <div class="mb-4">
                    <label class="form-label">JSON Tools:</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="formatJson()">
                            <i class="bi bi-code me-1"></i>Format JSON
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="minifyJson()">
                            <i class="bi bi-dash-square me-1"></i>Minify JSON
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="validateJson()">
                            <i class="bi bi-check-circle me-1"></i>Validate JSON
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="resetContent()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset Changes
                        </button>
                    </div>
                </div>

                <!-- Validation Status -->
                <div id="validationStatus" class="mb-3"></div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" onclick="deleteFile()">
                        <i class="bi bi-trash me-1"></i>Delete Silo
                    </button>
                    <div>
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="previewJson()">
                            <i class="bi bi-eye me-1"></i>Preview
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">JSON Preview</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <pre id="previewContent" class="bg-black p-3 rounded" style="max-height: 400px; overflow-y: auto;"></pre>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // Store original content for reset
        const originalContent = <?= json_encode($silo['json_content']) ?>;

        // File upload handling
        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                processFile(file);
            }
        }

        function processFile(file) {
            const statusDiv = $('#uploadStatus');
            
            // Check file type
            if (!file.type.includes('json') && !file.name.toLowerCase().endsWith('.json')) {
                statusDiv.html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Please select a valid JSON file</div>');
                return;
            }
            
            // Check file size (10MB limit)
            if (file.size > 10 * 1024 * 1024) {
                statusDiv.html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>File size must be less than 10MB</div>');
                return;
            }
            
            statusDiv.html('<div class="alert alert-info"><i class="bi bi-clock me-2"></i>Reading file...</div>');
            
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const content = e.target.result;
                    
                    // Validate JSON
                    JSON.parse(content);
                    
                    // Update the manual editor with the uploaded content
                    $('#json_content').val(content);
                    
                    // Switch to manual editor tab
                    $('#manual-tab').tab('show');
                    
                    statusDiv.html('<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>File uploaded successfully! Content has been loaded into the editor.</div>');
                    
                    // Validate the content
                    validateJson();
                    
                } catch (error) {
                    statusDiv.html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Invalid JSON file: ' + error.message + '</div>');
                }
            };
            
            reader.onerror = function() {
                statusDiv.html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error reading file</div>');
            };
            
            reader.readAsText(file);
        }

        // Drag and drop handlers
        function dragOverHandler(ev) {
            ev.preventDefault();
        }

        function dragEnterHandler(ev) {
            ev.preventDefault();
            ev.currentTarget.classList.add('dragover');
        }

        function dragLeaveHandler(ev) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('dragover');
        }

        function dropHandler(ev) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('dragover');
            
            if (ev.dataTransfer.items) {
                for (let i = 0; i < ev.dataTransfer.items.length; i++) {
                    if (ev.dataTransfer.items[i].kind === 'file') {
                        const file = ev.dataTransfer.items[i].getAsFile();
                        processFile(file);
                        break; // Process only the first file
                    }
                }
            } else {
                for (let i = 0; i < ev.dataTransfer.files.length; i++) {
                    processFile(ev.dataTransfer.files[i]);
                    break; // Process only the first file
                }
            }
        }

        // JSON validation
        function validateJson() {
            const jsonContent = $('#json_content').val().trim();
            const statusDiv = $('#validationStatus');
            
            if (!jsonContent) {
                statusDiv.html('<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>JSON content is empty</div>');
                return false;
            }
            
            try {
                JSON.parse(jsonContent);
                statusDiv.html('<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Valid JSON format</div>');
                return true;
            } catch (error) {
                statusDiv.html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Invalid JSON: ' + error.message + '</div>');
                return false;
            }
        }

        // Format JSON
        function formatJson() {
            const content = $('#json_content').val().trim();
            try {
                const parsed = JSON.parse(content);
                const formatted = JSON.stringify(parsed, null, 2);
                $('#json_content').val(formatted);
                validateJson();
            } catch (error) {
                alert('Cannot format invalid JSON. Please fix errors first.');
            }
        }

        // Minify JSON
        function minifyJson() {
            const content = $('#json_content').val().trim();
            try {
                const parsed = JSON.parse(content);
                const minified = JSON.stringify(parsed);
                $('#json_content').val(minified);
                validateJson();
            } catch (error) {
                alert('Cannot minify invalid JSON. Please fix errors first.');
            }
        }

        // Reset content
        function resetContent() {
            if (confirm('Are you sure you want to reset all changes? This will restore the original content.')) {
                $('#json_content').val(originalContent);
                validateJson();
            }
        }

        // Preview JSON
        function previewJson() {
            const content = $('#json_content').val().trim();
            try {
                const parsed = JSON.parse(content);
                const formatted = JSON.stringify(parsed, null, 2);
                $('#previewContent').text(formatted);
                new bootstrap.Modal(document.getElementById('previewModal')).show();
            } catch (error) {
                alert('Cannot preview invalid JSON. Please fix errors first.');
            }
        }

        // Delete file
        function deleteFile() {
            if (confirm('Are you sure you want to delete this JSON silo? This action cannot be undone.')) {
                $.ajax({
                    url: '<?= base_url('dashboard/silos/delete/' . $silo['id']) ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.href = '<?= base_url('dashboard/silos') ?>';
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

        // Real-time validation
        $('#json_content').on('input', function() {
            validateJson();
        });

        // Form submission
        $('#editSiloForm').submit(function(e) {
            e.preventDefault();
            
            const filename = $('#filename').val().trim();
            const jsonContent = $('#json_content').val().trim();
            
            if (!filename) {
                alert('Please enter a filename');
                return;
            }
            
            if (!jsonContent) {
                alert('JSON content cannot be empty');
                return;
            }
            
            if (!validateJson()) {
                alert('Please fix JSON validation errors before saving');
                return;
            }
            
            // Show loading
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="spinner-border spinner-border-sm me-1"></i>Saving...').prop('disabled', true);
            
            $.ajax({
                url: '<?= base_url('dashboard/silos/update/' . $silo['id']) ?>',
                type: 'POST',
                data: {
                    filename: filename,
                    json_content: jsonContent
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        window.location.href = '<?= base_url('dashboard/silos') ?>';
                    } else {
                        alert('Error: ' + response.message);
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                },
                error: function() {
                    alert('An error occurred while saving the changes');
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Initialize validation on page load
        $(document).ready(function() {
            validateJson();
            
            // Clear upload status when switching tabs
            $('#contentTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (event) {
                if (event.target.id === 'manual-tab') {
                    $('#uploadStatus').empty();
                }
            });
            
            // Make upload area clickable
            $('.upload-area').on('click', function() {
                $('#jsonFileInput').click();
            });
        });
    </script>
</body>
</html>
