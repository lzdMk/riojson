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
        
        .tab-content {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .nav-tabs .nav-link {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }
        
        .nav-tabs .nav-link.active {
            background: rgba(59, 130, 246, 0.2);
            border-color: #3b82f6;
            color: #3b82f6;
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
        }
        
        .upload-area {
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            background: rgba(30, 41, 59, 0.3);
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .upload-area.dragover {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
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
            <div class="d-flex align-items-center mb-4">
                <a href="<?= base_url('dashboard/silos') ?>" class="btn btn-outline-light btn-sm me-3">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
                <div>
                    <h2 class="mb-1"><i class="bi bi-plus-lg text-primary me-2"></i>Create New JSON Silo</h2>
                    <p class="text-muted mb-0">Create a new JSON file by typing manually or uploading</p>
                </div>
            </div>

            <!-- Form -->
            <form id="createSiloForm" enctype="multipart/form-data">
                <!-- File Name -->
                <div class="mb-4">
                    <label for="filename" class="form-label">Silo Name</label>
                    <input type="text" class="form-control" id="filename" name="filename" 
                           placeholder="my-data.json" required>
                    <div class="form-text">Give your JSON silo a descriptive name (e.g., user-data.json, config.json)</div>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" id="editorTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="manual-tab" data-bs-toggle="tab" 
                                data-bs-target="#manual" type="button" role="tab">
                            <i class="bi bi-code me-1"></i>Manual Editor
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="upload-tab" data-bs-toggle="tab" 
                                data-bs-target="#upload" type="button" role="tab">
                            <i class="bi bi-upload me-1"></i>Upload File
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="editorTabsContent">
                    <!-- Manual Editor Tab -->
                    <div class="tab-pane fade show active p-4" id="manual" role="tabpanel">
                        <label for="json_content" class="form-label">JSON Content</label>
                        <textarea class="form-control json-editor" id="json_content" name="json_content" 
                                  placeholder='{"message": "Hello World", "data": [1, 2, 3]}' rows="20"></textarea>
                        <div class="form-text mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Enter valid JSON content. The editor will validate your JSON syntax.
                        </div>
                        
                        <!-- JSON Templates -->
                        <div class="mt-3">
                            <label class="form-label">Quick Templates:</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="useTemplate('object')">
                                    Empty Object
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="useTemplate('array')">
                                    Empty Array
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="useTemplate('config')">
                                    Config File
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="useTemplate('api')">
                                    API Response
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Upload File Tab -->
                    <div class="tab-pane fade p-4" id="upload" role="tabpanel">
                        <div class="upload-area" id="uploadArea">
                            <i class="bi bi-cloud-upload text-primary" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">Drop your JSON file here</h5>
                            <p class="text-muted">or click to browse and select a file</p>
                            <input type="file" id="json_file" name="json_file" accept=".json" class="d-none">
                            <button type="button" class="btn btn-outline-primary" onclick="$('#json_file').click()">
                                <i class="bi bi-folder2-open me-1"></i>Choose File
                            </button>
                        </div>
                        <div id="fileInfo" class="mt-3 d-none">
                            <div class="alert alert-info">
                                <i class="bi bi-file-earmark-check me-2"></i>
                                <strong>File selected:</strong> <span id="fileName"></span>
                                <br><small class="text-muted">Size: <span id="fileSize"></span></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validation Status -->
                <div id="validationStatus" class="mb-3"></div>

                <!-- Submit Button -->
                <div class="text-end">
                    <button type="button" class="btn btn-outline-secondary me-2" onclick="validateJson()">
                        <i class="bi bi-check-circle me-1"></i>Validate JSON
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Create Silo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // JSON Templates
        const templates = {
            object: '{\n  "key": "value"\n}',
            array: '[\n  {\n    "id": 1,\n    "name": "Item 1"\n  }\n]',
            config: '{\n  "app_name": "My App",\n  "version": "1.0.0",\n  "settings": {\n    "debug": true,\n    "max_users": 100\n  }\n}',
            api: '{\n  "success": true,\n  "message": "Data retrieved successfully",\n  "data": {\n    "users": [],\n    "count": 0\n  }\n}'
        };

        function useTemplate(type) {
            $('#json_content').val(templates[type]);
            validateJson();
        }

        // File upload handling
        $('#json_file').change(function() {
            const file = this.files[0];
            if (file) {
                $('#fileName').text(file.name);
                $('#fileSize').text(formatBytes(file.size));
                $('#fileInfo').removeClass('d-none');
                
                // Auto-fill filename if empty
                if (!$('#filename').val()) {
                    $('#filename').val(file.name);
                }
                
                // Read file content
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#json_content').val(e.target.result);
                    validateJson();
                };
                reader.readAsText(file);
            }
        });

        // Drag and drop
        $('#uploadArea').on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        });

        $('#uploadArea').on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
        });

        $('#uploadArea').on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                $('#json_file')[0].files = files;
                $('#json_file').trigger('change');
            }
        });

        // JSON validation
        function validateJson() {
            const jsonContent = $('#json_content').val().trim();
            const statusDiv = $('#validationStatus');
            
            if (!jsonContent) {
                statusDiv.html('');
                return;
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

        // Real-time validation
        $('#json_content').on('input', function() {
            validateJson();
        });

        // Form submission
        $('#createSiloForm').submit(function(e) {
            e.preventDefault();
            
            const filename = $('#filename').val().trim();
            const jsonContent = $('#json_content').val().trim();
            
            // DEBUG: Log form data
            console.log('DEBUG: Form submission started');
            console.log('DEBUG: Filename:', filename);
            console.log('DEBUG: JSON Content length:', jsonContent.length);
            console.log('DEBUG: JSON Content preview:', jsonContent.substring(0, 100) + '...');
            
            if (!filename) {
                alert('Please enter a filename');
                return;
            }
            
            if (!jsonContent) {
                alert('Please enter JSON content or upload a file');
                return;
            }
            
            if (!validateJson()) {
                alert('Please fix JSON validation errors before submitting');
                return;
            }
            
            const formData = new FormData(this);
            
            // DEBUG: Log FormData contents
            console.log('DEBUG: FormData contents:');
            for (let [key, value] of formData.entries()) {
                if (key === 'json_content') {
                    console.log('DEBUG:', key, ':', value.substring(0, 100) + '...');
                } else {
                    console.log('DEBUG:', key, ':', value);
                }
            }
            
            // Show loading
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="spinner-border spinner-border-sm me-1"></i>Creating...').prop('disabled', true);
            
            $.ajax({
                url: '<?= base_url('dashboard/silos/save') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('DEBUG: Success response:', response);
                    if (response.success) {
                        alert(response.message);
                        window.location.href = '<?= base_url('dashboard/silos') ?>';
                    } else {
                        alert('Error: ' + response.message);
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('DEBUG: Error occurred');
                    console.log('XHR:', xhr);
                    console.log('Status:', status);
                    console.log('Error:', error);
                    console.log('Response Text:', xhr.responseText);
                    alert('An error occurred while creating the silo. Check console for details.');
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Utility function
        function formatBytes(bytes) {
            if (bytes === 0) return '0.00 MB';
            
            // Convert to MB first
            const mb = bytes / (1024 * 1024);
            
            // Smart decimal places based on size
            if (mb < 0.01) {
                // Very small files: show 3 decimal places (e.g., 0.001 MB)
                return parseFloat(mb.toFixed(3)) + ' MB';
            } else if (mb < 1) {
                // Small files under 1 MB: show 2 decimal places (e.g., 0.61 MB)
                return parseFloat(mb.toFixed(2)) + ' MB';
            } else if (mb < 10) {
                // Files 1-10 MB: show 1 decimal place (e.g., 5.2 MB)
                return parseFloat(mb.toFixed(1)) + ' MB';
            }
            
            // For values >= 10 MB, use the standard progression
            const k = 1024;
            const sizes = ['MB', 'GB', 'TB', 'PB'];
            let value = mb;
            let i = 0;
            
            while (value >= k && i < sizes.length - 1) {
                value /= k;
                i++;
            }
            
            // Adjust precision based on unit
            if (sizes[i] === 'MB') {
                // MB: no decimal for large files (e.g., 15 MB, 150 MB)
                return Math.round(value) + ' ' + sizes[i];
            } else {
                // GB, TB: 1 decimal place (e.g., 1.5 GB, 2.3 TB)
                return parseFloat(value.toFixed(1)) + ' ' + sizes[i];
            }
        }

        // Load default template
        $(document).ready(function() {
            useTemplate('object');
        });
    </script>
</body>
</html>
