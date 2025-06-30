# RioConsoleJSON API Test Scripts

This directory contains Python scripts to thoroughly test your RioConsoleJSON API endpoints.

## Files Created

- `test_api.py` - Comprehensive API testing suite with beautiful output
- `quick_test.py` - Simple and fast API testing script
- `requirements.txt` - Python dependencies

## Quick Setup

1. **Install Python dependencies:**
   ```bash
   pip install -r requirements.txt
   ```

2. **Run the quick test:**
   ```bash
   python quick_test.py
   ```

3. **Run the comprehensive test suite:**
   ```bash
   python test_api.py
   ```

## Configuration

Both scripts are pre-configured with your API credentials:

```python
API_KEY = "rio_11eabc3c793401b780fa0e14652a2db180525930d438995d"
USER_ID = "tjD9cb"
FILE_ID = "938-959-903"
BASE_URL = "http://localhost/riojson"  # Change this to your domain
```

**Important:** Update the `BASE_URL` in both scripts to match your actual domain if not running locally.

## Test Coverage

### Comprehensive Test Suite (`test_api.py`)

✅ **Authentication Tests**
- Bearer token authentication
- API-Key header format
- Invalid API key rejection
- Missing API key handling

✅ **Endpoint Tests**
- Get all files (`GET /api/v1/{user_id}`)
- Get specific file (`GET /api/v1/{user_id}/{file_id}`)
- Get raw JSON (`GET /api/v1/{user_id}/{file_id}/raw`)
- Invalid user ID handling
- Invalid file ID handling

✅ **API Info Endpoints**
- API information (`GET /api/v1/info`)
- Health check (`GET /api/v1/health`)

✅ **Domain Lock Security**
- Origin header validation
- Referer header validation
- Unauthorized domain blocking

✅ **Rate Limiting**
- Rate limit headers inspection
- Burst testing (rapid requests)
- Rate limit enforcement

✅ **Security Features**
- SQL injection protection
- XSS protection
- Security headers validation

✅ **Error Handling**
- 400 Bad Request responses
- 404 Not Found responses
- Error response format validation

### Quick Test (`quick_test.py`)

🚀 **Basic Functionality**
- API health check
- Get all files
- Get specific file
- Get raw JSON
- API information

## Understanding Your API

Based on the codebase analysis, your RioConsoleJSON API has the following features:

### 🔐 **Authentication**
- Supports both `Bearer {token}` and `API-Key {token}` formats
- API keys follow the format: `rio_[40-48 hex characters]`
- Domain lock security for restricting API access to specific domains

### 🚦 **Rate Limiting**
- **Free Users**: 100 requests/hour, 20 requests/minute burst, 1000/day
- **Paid Users**: 500 requests/hour, 100 requests/minute burst, 10000/day  
- **Admin Users**: Unlimited access
- Rate limit headers included in responses

### 🛡️ **Security Features**
- HTTPS enforcement in production
- Domain-based access restrictions
- Security headers (X-Frame-Options, CSP, etc.)
- Input validation and sanitization
- Attack pattern detection

### 📊 **Endpoints**
1. `GET /api/v1/info` - API information (no auth required)
2. `GET /api/v1/health` - Health check (no auth required)
3. `GET /api/v1/{user_id}` - Get all user files
4. `GET /api/v1/{user_id}/{file_id}` - Get specific file with metadata
5. `GET /api/v1/{user_id}/{file_id}/raw` - Get raw JSON content

### 📈 **Response Format**
**Success Response:**
```json
{
  "success": true,
  "message": "Request successful",
  "data": { ... },
  "timestamp": "2025-06-30T08:11:30+00:00"
}
```

**Error Response:**
```json
{
  "error": true,
  "message": "Error description",
  "timestamp": "2025-06-30T08:11:30+00:00"
}
```

## Running the Tests

### Prerequisites
- Python 3.7+
- `requests` library
- Your RioConsoleJSON server running

### Method 1: Quick Test
```bash
python quick_test.py
```
This runs a simple 5-test suite in about 5 seconds.

### Method 2: Comprehensive Test
```bash
python test_api.py
```
This runs a full test suite with:
- 25+ individual tests
- Beautiful colored output
- Performance timing
- Detailed error reporting
- Security validation

## Sample Output

```
🚀 Testing RioConsoleJSON API...
Base URL: http://localhost/riojson
User ID: tjD9cb
File ID: 938-959-903
--------------------------------------------------
1. Testing API Health...
   Status: 200
   Health: healthy

2. Testing Get All Files...
   Status: 200
   Total files: 3
   Files:
     - sample-data.json (ID: 938-959-903)
     - config.json (ID: 123-456-789)
     - test.json (ID: 987-654-321)

✅ API testing completed!
```

## Troubleshooting

### Common Issues

1. **Connection Refused**
   - Check if your XAMPP/web server is running
   - Verify the BASE_URL is correct
   - Ensure your API is accessible at the specified URL

2. **401 Unauthorized**
   - Verify your API key is correct
   - Check if the API key exists in your dashboard
   - Ensure the API key hasn't been revoked

3. **403 Forbidden**
   - Check if the user_id is correct
   - Verify the API key belongs to the specified user
   - Check domain lock settings if enabled

4. **404 Not Found**
   - Verify the file_id exists for your user
   - Check the file wasn't deleted
   - Ensure the endpoint URL is correct

5. **429 Too Many Requests**
   - You've hit rate limits
   - Wait a moment and try again
   - Check your user type's rate limits

### Debug Tips

1. **Enable verbose output:**
   ```python
   import requests
   import logging
   
   logging.basicConfig(level=logging.DEBUG)
   ```

2. **Check response headers:**
   ```python
   response = requests.get(url, headers=headers)
   print(dict(response.headers))
   ```

3. **Test with curl:**
   ```bash
   curl -H "Authorization: Bearer your-api-key" \
        http://localhost/riojson/api/v1/tjD9cb
   ```

## Customization

You can easily modify the test scripts for your needs:

### Change Base URL
```python
BASE_URL = "https://yourdomain.com/riojson"
```

### Add Custom Headers
```python
headers = {
    "Authorization": f"Bearer {API_KEY}",
    "Origin": "https://yourdomain.com",  # For domain lock testing
    "User-Agent": "My-App/1.0"
}
```

### Test Different Endpoints
```python
# Test a different user
USER_ID = "your_user_id"

# Test a different file
FILE_ID = "your_file_id"
```

## Contributing

Feel free to modify these scripts to add more tests or improve functionality. The test framework is designed to be easily extensible.

---

**Happy Testing! 🎉**
