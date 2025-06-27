// Simple Authentication JavaScript with jQuery
$(document).ready(function() {
    
    // Ensure baseUrl is available
    if (typeof baseUrl === 'undefined') {
        console.error('BaseURL not defined. Please ensure the configuration script is loaded.');
        baseUrl = window.location.origin + '/';
    }
    
    // Ensure baseUrl ends with a slash
    if (!baseUrl.endsWith('/')) {
        baseUrl += '/';
    }
    
    // Password toggle functionality
    $('.password-toggle-btn').click(function() {
        let input = $(this).siblings('input');
        let icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
    
    // Password strength checker
    $('#password').on('input', function() {
        let password = $(this).val();
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
        
        let indicator = $('.password-strength');
        if (indicator.length === 0) {
            indicator = $('<div class="password-strength"></div>');
            $(this).parent().append(indicator);
        }
        
        indicator.removeClass('weak medium strong');
        if (password.length === 0) {
            indicator.removeClass('weak medium strong');
        } else if (strength <= 2) {
            indicator.addClass('weak');
        } else if (strength <= 4) {
            indicator.addClass('medium');
        } else {
            indicator.addClass('strong');
        }
    });
    
    // Password confirmation validation
    $('#confirmPassword').on('input', function() {
        let password = $('#password').val();
        let confirmPassword = $(this).val();
        
        if (confirmPassword === '') {
            $(this).removeClass('is-valid is-invalid');
            return;
        }
        
        if (password === confirmPassword) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    });
    
    // Login form submission
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        
        let email = $('#email').val();
        let password = $('#password').val();
        let remember = $('#rememberMe').is(':checked');
        let submitBtn = $(this).find('button[type="submit"]');
        
        // Basic validation
        if (!email || !password) {
            showAlert('Please fill in all fields', 'danger');
            return;
        }
        
        // Show loading state
        setLoadingState(submitBtn, true);
        
        // Make AJAX request
        $.ajax({
            url: baseUrl + 'auth/login',
            type: 'POST',
            data: {
                email: email,
                password: password,
                remember: remember ? 1 : 0,
                // Add CSRF protection if available
                ...(window.appConfig && window.appConfig.csrfName ? {
                    [window.appConfig.csrfName]: window.appConfig.csrfToken
                } : {})
            },
            dataType: 'json',
            success: function(response) {
                setLoadingState(submitBtn, false);
                
                if (response.success) {
                    showAlert(response.message, 'success');
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function() {
                setLoadingState(submitBtn, false);
                showAlert('An error occurred. Please try again.', 'danger');
            }
        });
    });
    
    // Signup form submission
    $('#signupForm').submit(function(e) {
        e.preventDefault();
        
        let email = $('#email').val().trim();
        let password = $('#password').val().trim();
        let confirmPassword = $('#confirmPassword').val().trim();
        let agreeTerms = $('#agreeTerms').is(':checked');
        let submitBtn = $(this).find('button[type="submit"]');
        
        // Basic validation
        if (!email || !password || !confirmPassword) {
            showAlert('Please fill in all fields', 'danger');
            return;
        }
        
        if (!isValidEmail(email)) {
            showAlert('Please enter a valid email address', 'danger');
            return;
        }
        
        if (password.length < 8) {
            showAlert('Password must be at least 8 characters long', 'danger');
            return;
        }
        
        if (password !== confirmPassword) {
            showAlert('Passwords do not match', 'danger');
            return;
        }
        
        if (!agreeTerms) {
            showAlert('Please agree to the Terms of Service and Privacy Policy', 'danger');
            return;
        }
        
        // Show loading state
        setLoadingState(submitBtn, true);
        
        // Make AJAX request
        $.ajax({
            url: baseUrl + 'auth/register',
            type: 'POST',
            data: {
                email: email,
                password: password,
                confirm_password: confirmPassword,
                agree_terms: agreeTerms ? 1 : 0,
                // Add CSRF protection if available
                ...(window.appConfig && window.appConfig.csrfName ? {
                    [window.appConfig.csrfName]: window.appConfig.csrfToken
                } : {})
            },
            dataType: 'json',
            success: function(response) {
                setLoadingState(submitBtn, false);
                
                if (response.success) {
                    showAlert(response.message, 'success');
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 2000);
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function() {
                setLoadingState(submitBtn, false);
                showAlert('An error occurred. Please try again.', 'danger');
            }
        });
    });
    
    // Forgot password functionality
    $('.forgot-password-link').click(function(e) {
        e.preventDefault();
        let email = $('#email').val();
        
        if (!email) {
            showAlert('Please enter your email address first', 'warning');
            return;
        }
        
        $.ajax({
            url: baseUrl + 'auth/forgot-password',
            type: 'POST',
            data: { 
                email: email,
                // Add CSRF protection if available
                ...(window.appConfig && window.appConfig.csrfName ? {
                    [window.appConfig.csrfName]: window.appConfig.csrfToken
                } : {})
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function() {
                showAlert('An error occurred. Please try again.', 'danger');
            }
        });
    });
    
    // Helper functions
    function isValidEmail(email) {
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function showAlert(message, type) {
        // Remove existing alerts
        $('.alert').remove();
        
        let alertClass = 'alert-' + type;
        let alert = $('<div class="alert ' + alertClass + ' alert-dismissible fade show">' +
                     message +
                     '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                     '</div>');
        
        $('form').before(alert);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            alert.fadeOut();
        }, 5000);
    }
    
    function setLoadingState(button, loading) {
        if (loading) {
            button.prop('disabled', true);
            button.html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');
        } else {
            button.prop('disabled', false);
            // Restore original button text
            if (button.closest('#loginForm').length) {
                button.html('<i class="bi bi-box-arrow-in-right me-2"></i>Sign In');
            } else {
                button.html('<i class="bi bi-person-plus me-2"></i>Create Account');
            }
        }
    }
});
