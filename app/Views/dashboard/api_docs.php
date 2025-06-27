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
    <!-- Prism.js for syntax highlighting -->
    <link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
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
        
        .doc-section {
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        
        .endpoint-card {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .method-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }
        
        .method-get { background: #10b981; color: white; }
        .method-post { background: #3b82f6; color: white; }
        .method-put { background: #f59e0b; color: white; }
        .method-delete { background: #ef4444; color: white; }
        
        .code-block {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            position: relative;
            overflow-x: auto;
        }
        
        .copy-code-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e2e8f0;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .copy-code-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .response-example {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        .error-example {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .toc {
            position: sticky;
            top: 20px;
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
        }
        
        .toc a {
            color: #94a3b8;
            text-decoration: none;
            padding: 0.25rem 0;
            display: block;
            border-left: 2px solid transparent;
            padding-left: 0.5rem;
            margin-left: -0.5rem;
        }
        
        .toc a:hover {
            color: #3b82f6;
            border-left-color: #3b82f6;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin: 16px 8px !important;
                padding: 16px !important;
            }
            
            .toc {
                position: relative;
                margin-bottom: 2rem;
            }
            
            .code-block {
                font-size: 0.8rem;
            }
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
        
        .mobile-menu-btn {
            display: none;
        }
        
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: inline-block;
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
                        <a class="nav-link active" href="<?= base_url('dashboard/api/docs') ?>">
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
                            <h2 class="mb-1"><i class="bi bi-book text-info me-2"></i>API Documentation</h2>
                            <p class="text-muted mb-0">Learn how to use the RioConsoleJSON API</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?= base_url('dashboard/api') ?>" class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-key me-1"></i><span class="d-none d-sm-inline">Manage Keys</span>
                            </a>
                        </div>
                    </div>

            <div class="row">
                <!-- Table of Contents -->
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="toc">
                        <h6 class="text-light mb-3">Table of Contents</h6>
                        <a href="#overview">Overview</a>
                        <a href="#authentication">Authentication</a>
                        <a href="#domain-lock">Domain Lock Security</a>
                        <a href="#endpoints">API Endpoints</a>
                        <a href="#get-files">Get All Files</a>
                        <a href="#get-file">Get Specific File</a>
                        <a href="#get-raw">Get Raw JSON</a>
                        <a href="#examples">Code Examples</a>
                        <a href="#errors">Error Handling</a>
                        <a href="#rate-limits">Rate Limits</a>
                    </div>
                </div>

                <!-- Documentation Content -->
                <div class="col-lg-9">
                    <!-- Overview -->
                    <section id="overview" class="doc-section p-4">
                        <h3><i class="bi bi-info-circle text-info me-2"></i>Overview</h3>
                        <p>The RioConsoleJSON API allows you to programmatically access your JSON silos. All API requests require authentication using an API key.</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Base URL</h6>
                                <div class="code-block">
                                    <?= $base_url ?>api/v1/
                                    <button class="copy-code-btn" onclick="copyText('<?= $base_url ?>api/v1/')">Copy</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Your User ID</h6>
                                <div class="code-block">
                                    <?= $user_id ?>
                                    <button class="copy-code-btn" onclick="copyText('<?= $user_id ?>')">Copy</button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Authentication -->
                    <section id="authentication" class="doc-section p-4">
                        <h3><i class="bi bi-shield-check text-warning me-2"></i>Authentication</h3>
                        <p>All API requests must include your API key in the Authorization header. You can use either format:</p>
                        
                        <h6>Bearer Token (Recommended)</h6>
                        <div class="code-block">
Authorization: Bearer your-api-key-here
<button class="copy-code-btn" onclick="copyText('Authorization: Bearer your-api-key-here')">Copy</button>
                        </div>
                        
                        <h6 class="mt-3">API Key Header</h6>
                        <div class="code-block">
Authorization: API-Key your-api-key-here
<button class="copy-code-btn" onclick="copyText('Authorization: API-Key your-api-key-here')">Copy</button>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle me-2"></i>
                            You can create and manage your API keys in the <a href="<?= base_url('dashboard/api') ?>" class="text-info">API Keys section</a>.
                        </div>
                    </section>

                    <!-- Domain Lock Security -->
                    <section id="domain-lock" class="doc-section p-4">
                        <h3><i class="bi bi-shield-lock text-warning me-2"></i>Domain Lock Security</h3>
                        <p>Protect your API keys with domain-based access restrictions. When enabled, your API key will only accept requests from specified domains.</p>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Security Feature:</strong> Domain lock prevents unauthorized domains from using your API key, even if it gets exposed.
                        </div>

                        <h5>How Domain Lock Works</h5>
                        <p>When domain lock is enabled, the API validates incoming requests using these headers (in order of priority):</p>
                        
                        <ol>
                            <li><code>Origin</code> header (for CORS requests)</li>
                            <li><code>Referer</code> header (for same-origin requests)</li>
                            <li><code>Host</code> header (fallback validation)</li>
                        </ol>

                        <h6>Enabling Domain Lock</h6>
                        <p>You can enable domain lock and configure allowed domains in the <a href="<?= base_url('dashboard/api') ?>" class="text-warning">API Keys management</a> section.</p>

                        <h6>Example: Allowed Domains Configuration</h6>
                        <div class="code-block">
<pre class="language-json"><code>[
  "localhost",
  "127.0.0.1",
  "mydomain.com",
  "www.mydomain.com",
  "app.mydomain.com"
]</code></pre>
<button class="copy-code-btn" onclick="copyCodeBlock(this)">Copy</button>
                        </div>

                        <h6 class="mt-3">Testing Domain Lock</h6>
                        <p>When making requests with domain lock enabled, ensure your client sends appropriate headers:</p>
                        
                        <div class="code-block">
<pre class="language-javascript"><code>// JavaScript - Origin header automatically set by browser
fetch('<?= $base_url ?>api/v1/<?= $user_id ?>', {
  headers: {
    'Authorization': 'Bearer your-api-key-here'
  }
});

// cURL - Manually set Origin header
curl -H "Authorization: Bearer your-api-key-here" \
     -H "Origin: https://mydomain.com" \
     <?= $base_url ?>api/v1/<?= $user_id ?></code></pre>
<button class="copy-code-btn" onclick="copyCodeBlock(this)">Copy</button>
                        </div>

                        <h6 class="mt-3">Domain Lock Responses</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success">✅ Allowed Domain</h6>
                                <div class="code-block response-example">
<pre class="language-json"><code>HTTP/1.1 200 OK
{
  "success": true,
  "message": "Request successful",
  "data": { ... }
}</code></pre>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-danger">🚫 Blocked Domain</h6>
                                <div class="code-block error-example">
<pre class="language-json"><code>HTTP/1.1 403 Forbidden
{
  "error": true,
  "message": "Domain not allowed for this API key",
  "timestamp": "2025-06-26T08:11:30+00:00"
}</code></pre>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>Best Practices:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Include both www and non-www versions of your domain</li>
                                <li>Add localhost and 127.0.0.1 for local development</li>
                                <li>Use specific subdomains instead of wildcards for better security</li>
                                <li>Regularly review and update your allowed domains list</li>
                            </ul>
                        </div>
                    </section>

                    <!-- API Endpoints -->
                    <section id="endpoints" class="doc-section p-4">
                        <h3><i class="bi bi-list text-primary me-2"></i>API Endpoints</h3>
                        
                        <!-- Get All Files -->
                        <div id="get-files" class="endpoint-card p-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="method-badge method-get me-2">GET</span>
                                <code>/api/v1/{user_id}</code>
                            </div>
                            <p>Retrieve all JSON files for the authenticated user.</p>
                            
                            <h6>Example Request</h6>
                            <div class="code-block">
GET <?= $base_url ?>api/v1/<?= $user_id ?>
Authorization: Bearer your-api-key-here
<button class="copy-code-btn" onclick="copyText('GET <?= $base_url ?>api/v1/<?= $user_id ?>\nAuthorization: Bearer your-api-key-here')">Copy</button>
                            </div>
                            
                            <h6 class="mt-3">Response</h6>
                            <div class="code-block response-example">
<pre class="language-json"><code>{
  "success": true,
  "message": "User files retrieved successfully",
  "data": {
    "user_id": "<?= $user_id ?>",
    "total_files": 2,
    "files": [
      {
        "file_id": "123-456-789",
        "filename": "sample-data.json",
        "uploaded_at": "2025-06-26 08:11:30",
        "size": 85
      },
      {
        "file_id": "987-654-321", 
        "filename": "config.json",
        "uploaded_at": "2025-06-25 14:22:15",
        "size": 156
      }
    ]
  },
  "timestamp": "2025-06-26T08:11:30+00:00"
}</code></pre>
<button class="copy-code-btn" onclick="copyCodeBlock(this)">Copy</button>
                            </div>
                        </div>

                        <!-- Get Specific File -->
                        <div id="get-file" class="endpoint-card p-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="method-badge method-get me-2">GET</span>
                                <code>/api/v1/{user_id}/{file_id}</code>
                            </div>
                            <p>Retrieve a specific JSON file with metadata.</p>
                            
                            <h6>Example Request</h6>
                            <div class="code-block">
GET <?= $base_url ?>api/v1/<?= $user_id ?>/123-456-789
Authorization: Bearer your-api-key-here
<button class="copy-code-btn" onclick="copyText('GET <?= $base_url ?>api/v1/<?= $user_id ?>/123-456-789\nAuthorization: Bearer your-api-key-here')">Copy</button>
                            </div>
                            
                            <h6 class="mt-3">Response</h6>
                            <div class="code-block response-example">
<pre class="language-json"><code>{
  "success": true,
  "message": "JSON file retrieved successfully",
  "data": {
    "file_id": "123-456-789",
    "filename": "sample-data.json",
    "uploaded_at": "2025-06-26 08:11:30",
    "size": 85,
    "content": {
      "name": "Sample JSON",
      "type": "example",
      "data": [1, 2, 3, 4, 5]
    }
  },
  "timestamp": "2025-06-26T08:11:30+00:00"
}</code></pre>
<button class="copy-code-btn" onclick="copyCodeBlock(this)">Copy</button>
                            </div>
                        </div>

                        <!-- Get Raw JSON -->
                        <div id="get-raw" class="endpoint-card p-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="method-badge method-get me-2">GET</span>
                                <code>/api/v1/{user_id}/{file_id}/raw</code>
                            </div>
                            <p>Retrieve raw JSON content without metadata wrapper.</p>
                            
                            <h6>Example Request</h6>
                            <div class="code-block">
GET <?= $base_url ?>api/v1/<?= $user_id ?>/123-456-789/raw
Authorization: Bearer your-api-key-here
<button class="copy-code-btn" onclick="copyText('GET <?= $base_url ?>api/v1/<?= $user_id ?>/123-456-789/raw\nAuthorization: Bearer your-api-key-here')">Copy</button>
                            </div>
                            
                            <h6 class="mt-3">Response</h6>
                            <div class="code-block response-example">
<pre class="language-json"><code>{
  "name": "Sample JSON",
  "type": "example", 
  "data": [1, 2, 3, 4, 5]
}</code></pre>
<button class="copy-code-btn" onclick="copyCodeBlock(this)">Copy</button>
                            </div>
                        </div>
                    </section>

                    <!-- Code Examples -->
                    <section id="examples" class="doc-section p-4">
                        <h3><i class="bi bi-code-slash text-success me-2"></i>Code Examples</h3>
                        
                        <!-- JavaScript Example -->
                        <h5>JavaScript (fetch)</h5>
                        <div class="code-block">
<pre class="language-javascript"><code>// Get all files
async function getAllFiles() {
  const response = await fetch('<?= $base_url ?>api/v1/<?= $user_id ?>', {
    headers: {
      'Authorization': 'Bearer your-api-key-here'
    }
  });
  
  const data = await response.json();
  console.log(data);
}

// Get specific file
async function getFile(fileId) {
  const response = await fetch(`<?= $base_url ?>api/v1/<?= $user_id ?>/${fileId}`, {
    headers: {
      'Authorization': 'Bearer your-api-key-here'
    }
  });
  
  const data = await response.json();
  return data.data.content;
}</code></pre>
<button class="copy-code-btn" onclick="copyCodeBlock(this)">Copy</button>
                        </div>

                        <!-- cURL Example -->
                        <h5 class="mt-4">cURL</h5>
                        <div class="code-block">
<pre class="language-bash"><code># Get all files
curl -H "Authorization: Bearer your-api-key-here" \
     <?= $base_url ?>api/v1/<?= $user_id ?>

# Get specific file
curl -H "Authorization: Bearer your-api-key-here" \
     <?= $base_url ?>api/v1/<?= $user_id ?>/123-456-789

# Get raw JSON
curl -H "Authorization: Bearer your-api-key-here" \
     <?= $base_url ?>api/v1/<?= $user_id ?>/123-456-789/raw</code></pre>
<button class="copy-code-btn" onclick="copyCodeBlock(this)">Copy</button>
                        </div>

                        <!-- Python Example -->
                        <h5 class="mt-4">Python (requests)</h5>
                        <div class="code-block">
<pre class="language-python"><code>import requests

# API configuration
BASE_URL = "<?= $base_url ?>api/v1"
USER_ID = "<?= $user_id ?>"
API_KEY = "your-api-key-here"

headers = {"Authorization": f"Bearer {API_KEY}"}

# Get all files
response = requests.get(f"{BASE_URL}/{USER_ID}", headers=headers)
files = response.json()

# Get specific file
file_id = "123-456-789"
response = requests.get(f"{BASE_URL}/{USER_ID}/{file_id}", headers=headers)
file_data = response.json()

# Get raw JSON
response = requests.get(f"{BASE_URL}/{USER_ID}/{file_id}/raw", headers=headers)
raw_json = response.json()

# Testing with domain lock (if enabled)
# Add Origin header for domain validation
domain_headers = {
    "Authorization": f"Bearer {API_KEY}",
    "Origin": "https://yourdomain.com"
}
response = requests.get(f"{BASE_URL}/{USER_ID}", headers=domain_headers)
print(f"Domain lock test: {response.status_code}")</code></pre>
<button class="copy-code-btn" onclick="copyCodeBlock(this)">Copy</button>
                        </div>
                    </section>

                    <!-- Error Handling -->
                    <section id="errors" class="doc-section p-4">
                        <h3><i class="bi bi-exclamation-triangle text-danger me-2"></i>Error Handling</h3>
                        <p>The API returns standard HTTP status codes and JSON error responses.</p>
                        
                        <h6>Error Response Format</h6>
                        <div class="code-block error-example">
<pre class="language-json"><code>{
  "error": true,
  "message": "Invalid or missing API key",
  "timestamp": "2025-06-26T08:11:30+00:00"
}</code></pre>
<button class="copy-code-btn" onclick="copyCodeBlock(this)">Copy</button>
                        </div>
                        
                        <h6 class="mt-3">Common HTTP Status Codes</h6>
                        <div class="table-responsive">
                            <table class="table table-dark table-striped">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Status</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-success">200</span></td>
                                        <td>OK</td>
                                        <td>Request successful</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-warning">400</span></td>
                                        <td>Bad Request</td>
                                        <td>Missing or invalid parameters</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-warning">401</span></td>
                                        <td>Unauthorized</td>
                                        <td>Invalid or missing API key</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-warning">403</span></td>
                                        <td>Forbidden</td>
                                        <td>API key doesn't have access to resource or domain not allowed</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-danger">404</span></td>
                                        <td>Not Found</td>
                                        <td>Resource not found</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-danger">500</span></td>
                                        <td>Internal Server Error</td>
                                        <td>Server error occurred</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Rate Limits -->
                    <section id="rate-limits" class="doc-section p-4">
                        <h3><i class="bi bi-speedometer2 text-info me-2"></i>Rate Limits</h3>
                        <p>To ensure fair usage, the API implements the following rate limits:</p>
                        
                        <!-- Current User Rate Limits -->
                        <div class="alert alert-primary">
                            <h5><i class="bi bi-person-badge me-2"></i>Your Current Limits (<?= ucfirst($user_type) ?> User)</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <h6><i class="bi bi-clock me-2"></i>Hourly Limit</h6>
                                    <p class="mb-0 fs-5">
                                        <?= $current_rate_limits['requests_per_hour'] >= 999999 ? 'Unlimited' : number_format($current_rate_limits['requests_per_hour']) ?>
                                        <?= $current_rate_limits['requests_per_hour'] < 999999 ? ' requests/hour' : '' ?>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h6><i class="bi bi-lightning me-2"></i>Burst Limit</h6>
                                    <p class="mb-0 fs-5">
                                        <?= $current_rate_limits['burst_limit'] >= 999999 ? 'Unlimited' : number_format($current_rate_limits['burst_limit']) ?>
                                        <?= $current_rate_limits['burst_limit'] < 999999 ? ' requests/minute' : '' ?>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h6><i class="bi bi-calendar-day me-2"></i>Daily Limit</h6>
                                    <p class="mb-0 fs-5">
                                        <?= $current_rate_limits['daily_limit'] >= 999999 ? 'Unlimited' : number_format($current_rate_limits['daily_limit']) ?>
                                        <?= $current_rate_limits['daily_limit'] < 999999 ? ' requests/day' : '' ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Admin-only: All User Types Rate Limits -->
                        <?php if (isset($is_admin) && $is_admin): ?>
                            <div class="alert alert-danger">
                                <h5><i class="bi bi-shield-check me-2"></i>Admin View: All User Type Limits</h5>
                                <p class="text-muted small">This section is only visible to administrators</p>
                                
                                <div class="row">
                                    <!-- Free Users -->
                                    <div class="col-md-4">
                                        <div class="card bg-secondary">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0"><i class="bi bi-person me-2"></i>Free Users</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled mb-0">
                                                    <li><strong>Hourly:</strong> <?= number_format($all_rate_limits['free']['requests_per_hour']) ?></li>
                                                    <li><strong>Burst:</strong> <?= number_format($all_rate_limits['free']['burst_limit']) ?>/min</li>
                                                    <li><strong>Daily:</strong> <?= number_format($all_rate_limits['free']['daily_limit']) ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Paid Users -->
                                    <div class="col-md-4">
                                        <div class="card bg-info">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0"><i class="bi bi-person-check me-2"></i>Paid Users</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled mb-0">
                                                    <li><strong>Hourly:</strong> <?= number_format($all_rate_limits['paid']['requests_per_hour']) ?></li>
                                                    <li><strong>Burst:</strong> <?= number_format($all_rate_limits['paid']['burst_limit']) ?>/min</li>
                                                    <li><strong>Daily:</strong> <?= number_format($all_rate_limits['paid']['daily_limit']) ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Admin Users -->
                                    <div class="col-md-4">
                                        <div class="card bg-danger">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0"><i class="bi bi-shield-check me-2"></i>Admin Users</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled mb-0">
                                                    <li><strong>Hourly:</strong> Unlimited</li>
                                                    <li><strong>Burst:</strong> Unlimited</li>
                                                    <li><strong>Daily:</strong> Unlimited</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-secondary">
                            <i class="bi bi-info-circle me-2"></i>
                            Rate limit headers are included in all API responses to help you track your usage.
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Prism.js for syntax highlighting -->
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
    
    <script>
        // Copy text to clipboard
        function copyText(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Visual feedback handled by button animation
            });
        }
        
        // Copy code block content
        function copyCodeBlock(button) {
            const codeBlock = button.parentElement.querySelector('pre code');
            const text = codeBlock ? codeBlock.textContent : button.parentElement.textContent.replace('Copy', '').trim();
            
            navigator.clipboard.writeText(text).then(function() {
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.style.background = 'rgba(34, 197, 94, 0.3)';
                
                setTimeout(function() {
                    button.textContent = originalText;
                    button.style.background = 'rgba(255, 255, 255, 0.1)';
                }, 2000);
            });
        }
        
        // Smooth scrolling for TOC links
        document.querySelectorAll('.toc a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
        
        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            } else {
                sidebar.classList.add('show');
                overlay.classList.add('show');
            }
        }
    </script>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
