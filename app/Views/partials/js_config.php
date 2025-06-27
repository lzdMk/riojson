<!-- JavaScript Configuration -->
<script>
    // Global application configuration
    window.appConfig = {
        baseUrl: '<?= rtrim(base_url(), '/') ?>/',
        environment: '<?= ENVIRONMENT ?>',
        csrfToken: '<?= csrf_hash() ?>',
        csrfName: '<?= csrf_token() ?>',
        debug: <?= ENVIRONMENT === 'development' ? 'true' : 'false' ?>
    };
    
    // Backward compatibility for legacy code
    var baseUrl = window.appConfig.baseUrl;
    
    // Helper function to get CSRF data for AJAX requests
    window.getCSRFData = function() {
        return window.appConfig.csrfName ? {
            [window.appConfig.csrfName]: window.appConfig.csrfToken
        } : {};
    };
    
    // Helper function to make AJAX requests with automatic CSRF inclusion
    window.makeAjaxRequest = function(options) {
        if (options.data && typeof options.data === 'object') {
            options.data = { ...options.data, ...window.getCSRFData() };
        }
        return $.ajax(options);
    };
    
    // Debug logging (only in development)
    if (window.appConfig.debug) {
        console.log('App Config:', window.appConfig);
    }
</script>
