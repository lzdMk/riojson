<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RioConsoleJSON - Host JSON files like a boss</title>
    <meta name="description" content="Store and access up to 100MB of JSON data with unlimited bandwidth">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/landing_page/style.css') ?>">
</head>
<body class="bg-dark text-light">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top border-bottom border-secondary">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="#">
                <i class="bi bi-file-earmark-code text-primary me-2"></i>
                RioConsoleJSON
            </a>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="bi bi-moon-fill"></i>
                </button>
                <a href="<?= base_url('signin') ?>" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-terminal me-2"></i>Go to Console
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section bg-gradient py-5" style="margin-top: 76px; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
        <div class="container py-5">
            <div class="text-center">
                <div class="mb-4">
                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2">
                        <i class="bi bi-geo-alt me-1"></i>Current Region: UK (United Kingdom)
                    </span>
                </div>
                <h1 class="display-2 fw-bold mb-4">
                    Host <span class="text-primary">.json</span> files like a boss
                </h1>
                <p class="lead text-light-emphasis mb-5 mx-auto" style="max-width: 600px;">
                    Store and access up to 100MB of JSON data with unlimited bandwidth
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mb-4">
                    <button class="btn btn-outline-light btn-lg rounded-pill px-4">
                        <i class="bi bi-lightning-charge me-2"></i>Quick Host
                    </button>
                    <button class="btn btn-primary btn-lg rounded-pill px-4" onclick="window.location.href='<?= base_url('signup') ?>'">
                        Get Started <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
                <div class="text-muted">
                    <i class="bi bi-credit-card me-2"></i>No credit card required
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5 bg-dark">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Performance at a Glance</h2>
                <p class="lead text-muted">Insights into reliability</p>
            </div>
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <div class="text-center p-4">
                        <div class="display-4 fw-bold text-primary mb-2">1k+</div>
                        <div class="text-muted">JSON files hosted</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-center p-4">
                        <div class="display-4 fw-bold text-success mb-2">11k+</div>
                        <div class="text-muted">API Requests</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-center p-4">
                        <div class="display-4 fw-bold text-warning mb-2">98.6%</div>
                        <div class="text-muted">Uptime</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-center p-4">
                        <div class="display-4 fw-bold text-info mb-2">1</div>
                        <div class="text-muted">Region</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Why Choose Us</h2>
                <p class="lead text-muted">The difference we make</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-dark border-secondary h-100 shadow-lg hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-arrow-repeat text-white fs-4"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Unlimited Bandwidth</h5>
                            <p class="card-text text-muted">Access your JSON files without any bandwidth limits - transfer as much data as you need.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-dark border-secondary h-100 shadow-lg hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-shield-check text-white fs-4"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Access Protection</h5>
                            <p class="card-text text-muted">Choose to keep your JSON files private or share them publicly with our Access Protection feature.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-dark border-secondary h-100 shadow-lg hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-hdd text-white fs-4"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Up to 100 MB of Storage</h5>
                            <p class="card-text text-muted">Store JSON files up to 100 MB in size on our secure platform.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-dark border-secondary h-100 shadow-lg hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-code-slash text-white fs-4"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">API Integration</h5>
                            <p class="card-text text-muted">Elevate your project with seamless RioConsoleJSON API integration.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- In Action Section -->
    <section class="py-5 bg-dark">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">In Action</h2>
                <p class="lead text-muted">See how it works in the real world</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card bg-dark border-secondary">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i class="bi bi-bar-chart text-primary" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="card-title text-muted">Interactive JSON hosting demo coming soon</h5>
                            <p class="card-text text-muted">Experience the power of RioConsoleJSON with our live demo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black text-light py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-file-earmark-code text-primary me-2 fs-3"></i>
                        <h4 class="fw-bold mb-0">RioConsoleJSON</h4>
                    </div>
                    <p class="text-muted">Host JSON files like a boss</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-muted fs-5"><i class="bi bi-github"></i></a>
                        <a href="#" class="text-muted fs-5"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-muted fs-5"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="fw-bold mb-3">Product</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Features</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">API</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Console</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="fw-bold mb-3">Company</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">About</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Contact</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Support</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-bold mb-3">Stay Updated</h6>
                    <p class="text-muted mb-3">Get the latest updates and news about RioConsoleJSON</p>
                    <div class="input-group">
                        <input type="email" class="form-control bg-dark border-secondary text-light" placeholder="Enter your email">
                        <button class="btn btn-primary" type="button">Subscribe</button>
                    </div>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; 2025 RioConsoleJSON. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-muted text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-muted text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>