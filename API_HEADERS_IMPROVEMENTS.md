# API Headers & CORS Improvements

## Overview
Enhanced the RioJSON API with comprehensive security headers and proper CORS handling to prevent client-side issues and improve security.

## 🔐 Security Headers Added

### Essential Security Headers
- **X-Content-Type-Options: nosniff** - Prevents MIME type sniffing
- **X-Frame-Options: DENY** - Prevents clickjacking attacks  
- **X-XSS-Protection: 1; mode=block** - XSS protection for older browsers
- **Referrer-Policy: strict-origin-when-cross-origin** - Controls referrer information
- **X-Permitted-Cross-Domain-Policies: none** - Blocks cross-domain policy files
- **X-Download-Options: noopen** - Prevents file execution in IE

### Enhanced Security Headers  
- **Strict-Transport-Security** - Forces HTTPS connections (with preload)
- **Content-Security-Policy** - Strict CSP for API endpoints
- **Server-Timing** - Performance monitoring headers

### API-Specific Headers
- **API-Version: 1.0.0** - API version information
- **Server: RioJSON-API** - Custom server identifier
- **X-API-RateLimit-Policy: user-based** - Rate limiting information
- **X-Response-Time** - Request processing time
- **Vary: Accept, Authorization, Origin** - Proper caching control

## 🌐 CORS Improvements

### Preflight Handling
- Proper OPTIONS method handling
- 24-hour preflight cache (`Access-Control-Max-Age: 86400`)
- Comprehensive request method support
- Enhanced header allowlist

### Headers Configuration
**Allowed Headers:**
- Authorization, Content-Type, Accept, Origin
- X-Requested-With, X-API-Key
- Access-Control-Request-Method/Headers
- Cache-Control, Content-Length

**Exposed Headers:**
- All rate limit headers (hourly/burst)
- Performance headers (X-Response-Time, Server-Timing)
- API metadata (API-Version, X-API-RateLimit-Policy)
- Standard headers (Content-Length, Content-Type)

### Methods Supported
- GET, POST, PUT, DELETE, OPTIONS, HEAD
- Proper preflight validation

## 🚀 Features Added

### 1. Enhanced Header Management
```php
private function setupApiResponse(): void
private function setCorsHeaders(): void
```

### 2. Response Time Tracking
- Automatic timing headers in all responses
- Server-Timing header for performance monitoring

### 3. Preflight Request Logging
- CORS preflight requests are logged for monitoring
- Origin, method, and headers tracked

### 4. Content Disposition Headers
- Proper filename headers for raw JSON downloads
- Inline content disposition for browser display

## 🧪 Testing

### Test File: `test_cors.html`
A comprehensive test page that validates:
- CORS preflight requests
- Security headers presence
- API endpoint functionality
- Authenticated requests

### Usage
1. Access: `http://localhost/riojson/test_cors.html`
2. Test various endpoints automatically
3. View all response headers in detail
4. Test authenticated requests with API keys

## 📋 Configuration Files Updated

### 1. `/app/Controllers/ApiController.php`
- Enhanced `setSecurityHeaders()` method
- New `setCorsHeaders()` method  
- New `setupApiResponse()` method
- Improved response methods with timing

### 2. `/app/Config/Cors.php`
- Updated exposed headers list
- Enhanced allowed headers
- Increased preflight cache time

## 🔧 Benefits

### For Developers
- **No more CORS errors** in browser console
- **Clear error messages** with proper status codes
- **Performance metrics** via response time headers
- **Better debugging** with comprehensive headers

### For Security
- **Enhanced protection** against common web attacks
- **Proper CSP** for API endpoints
- **HTTPS enforcement** in production
- **Request validation** and logging

### For Performance
- **24-hour preflight caching** reduces OPTIONS requests
- **Response time tracking** for performance monitoring
- **Proper cache headers** prevent unwanted caching

## 🚨 Important Notes

1. **API Key Security**: Headers now properly expose rate limit information
2. **Domain Restrictions**: Still enforced through existing domain lock mechanism
3. **Environment Awareness**: HSTS only added in production or HTTPS environments
4. **Backward Compatibility**: All existing API functionality preserved

## 📖 Usage Examples

### JavaScript Fetch with CORS
```javascript
fetch('http://localhost/riojson/api/v1/health', {
    method: 'GET',
    headers: {
        'Authorization': 'Bearer your-api-key',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})
.then(response => {
    // Check rate limit headers
    console.log('Rate Limit:', response.headers.get('X-RateLimit-Remaining-Hourly'));
    console.log('Response Time:', response.headers.get('X-Response-Time'));
    return response.json();
})
.then(data => console.log(data));
```

### cURL Testing
```bash
# Test preflight
curl -X OPTIONS http://localhost/riojson/api/v1/info \
  -H "Origin: http://localhost:3000" \
  -H "Access-Control-Request-Method: GET" \
  -v

# Test actual request  
curl -X GET http://localhost/riojson/api/v1/health \
  -H "Authorization: Bearer your-api-key" \
  -v
```

## ✅ Client Problem Resolution

Your clients should now experience:
- ✅ **No CORS errors** in browsers
- ✅ **Proper preflight handling** for complex requests  
- ✅ **Clear rate limit information** in headers
- ✅ **Better error messages** with proper HTTP status codes
- ✅ **Performance insights** via timing headers
- ✅ **Enhanced security** without breaking functionality

The API now properly handles cross-origin requests while maintaining security and providing comprehensive information to clients about rate limits, performance, and API capabilities.
