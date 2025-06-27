<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - RioConsoleJSON</title>
    <meta name="description" content="Create your RioConsoleJSON account">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/auth/auth.css') ?>">
</head>
<body class="bg-dark text-light">
    <div class="auth-container">
        <div class="container-fluid h-100">
            <div class="row h-100">
                <!-- Left Side - Branding -->
                <div class="col-lg-6 d-none d-lg-flex auth-brand-side">
                    <div class="d-flex flex-column justify-content-center align-items-center text-center p-5">
                        <div class="mb-4">
                            <i class="bi bi-file-earmark-code text-primary" style="font-size: 5rem;"></i>
                        </div>
                        <h2 class="fw-bold mb-3">Join RioConsoleJSON</h2>
                        <p class="lead text-muted mb-4">Start hosting JSON files for free with unlimited requests and secure access</p>
                        <div class="feature-list text-start">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                                <span>Up to 100MB of storage</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                                <span>Unlimited bandwidth</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                                <span>Advanced access protection</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                                <span>98.6% uptime reliability</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Signup Form -->
                <div class="col-lg-6 d-flex">
                    <div class="auth-form-container">
                        <div class="auth-form">
                            <!-- Logo for mobile -->
                            <div class="text-center mb-4 d-lg-none">
                                <i class="bi bi-file-earmark-code text-primary fs-1"></i>
                                <h4 class="fw-bold mt-2">RioConsoleJSON</h4>
                            </div>

                            <div class="text-center mb-4">
                                <h3 class="fw-bold mb-2">Sign Up</h3>
                                <p class="text-muted">Create your account to get started</p>
                            </div>

                            <form id="signupForm">
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail Address</label>
                                    <input type="email" class="form-control form-control-lg" id="email" placeholder="johndoe@example.com" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="password-toggle-container">
                                        <input type="password" class="form-control form-control-lg" id="password" placeholder="Create a strong password" required>
                                        <button type="button" class="password-toggle-btn" id="togglePassword">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        <small class="text-muted">Password must be at least 8 characters long</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                                    <div class="password-toggle-container">
                                        <input type="password" class="form-control form-control-lg" id="confirmPassword" placeholder="Confirm your password" required>
                                        <button type="button" class="password-toggle-btn" id="toggleConfirmPassword">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                        <label class="form-check-label text-muted" for="agreeTerms">
                                            I agree to the <a href="#" class="text-primary text-decoration-none">Terms of Service</a> and <a href="#" class="text-primary text-decoration-none">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-4">
                                    <i class="bi bi-person-plus me-2"></i>Create Account
                                </button>

                                <div class="text-center">
                                    <span class="text-muted">Already have an account? </span>
                                    <a href="<?= base_url('signin') ?>" class="text-primary text-decoration-none fw-medium">Sign In</a>
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
    <!-- Application Configuration -->
    <?= view('partials/js_config') ?>
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/auth/auth.js') ?>"></script>
</body>
</html>
