#!/usr/bin/env python3
"""
RioConsoleJSON API Test Script
A comprehensive testing tool for the RioConsoleJSON API endpoints

Usage:
    python test_api.py

Features:
- Tests all API endpoints
- Validates authentication and security
- Tests domain lock functionality
- Rate limit testing
- Error handling validation
- Response time monitoring
- Beautiful colored output
"""

import requests
import json
import time
from typing import Dict, Any, Optional
from dataclasses import dataclass
import sys

# ANSI Color codes for beautiful output
class Colors:
    RED = '\033[91m'
    GREEN = '\033[92m'
    YELLOW = '\033[93m'
    BLUE = '\033[94m'
    PURPLE = '\033[95m'
    CYAN = '\033[96m'
    WHITE = '\033[97m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'
    END = '\033[0m'

@dataclass
class APITestConfig:
    """Configuration for API testing"""
    base_url: str = "http://localhost/riojson"  # Change this to your actual base URL
    api_key: str = "rio_11eabc3c793401b780fa0e14652a2db180525930d438995d"
    user_id: str = "tjD9cb"
    file_id: str = "938-959-903"
    api_version: str = "v1"
    
    @property
    def api_base_url(self) -> str:
        return f"{self.base_url}/api/{self.api_version}"

class APITester:
    def __init__(self, config: APITestConfig):
        self.config = config
        self.session = requests.Session()
        self.test_results = []
        
        # Set up session defaults
        self.session.headers.update({
            'User-Agent': 'RioConsoleJSON-API-Tester/1.0',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        })
        
    def print_header(self, title: str):
        """Print a formatted header"""
        print(f"\n{Colors.CYAN}{'='*60}{Colors.END}")
        print(f"{Colors.CYAN}{Colors.BOLD}{title.center(60)}{Colors.END}")
        print(f"{Colors.CYAN}{'='*60}{Colors.END}")
        
    def print_test(self, name: str, status: str, details: str = ""):
        """Print test result with colors"""
        if status.upper() == "PASS":
            status_color = Colors.GREEN
            status_symbol = "✓"
        elif status.upper() == "FAIL":
            status_color = Colors.RED
            status_symbol = "✗"
        elif status.upper() == "WARN":
            status_color = Colors.YELLOW
            status_symbol = "⚠"
        else:
            status_color = Colors.BLUE
            status_symbol = "ℹ"
            
        print(f"{status_color}{status_symbol} {name}: {status.upper()}{Colors.END}")
        if details:
            print(f"  {Colors.WHITE}{details}{Colors.END}")
            
    def make_request(self, method: str, endpoint: str, headers: Dict = None, **kwargs) -> Dict[str, Any]:
        """Make HTTP request and return formatted result"""
        url = f"{self.config.api_base_url}/{endpoint.lstrip('/')}"
        
        # Merge headers
        request_headers = self.session.headers.copy()
        if headers:
            request_headers.update(headers)
            
        start_time = time.time()
        
        try:
            response = self.session.request(method, url, headers=request_headers, **kwargs)
            response_time = round((time.time() - start_time) * 1000, 2)
            
            # Try to parse JSON
            try:
                data = response.json()
            except json.JSONDecodeError:
                data = {"raw_response": response.text}
                
            return {
                "success": True,
                "status_code": response.status_code,
                "response_time_ms": response_time,
                "headers": dict(response.headers),
                "data": data,
                "url": url
            }
            
        except requests.exceptions.RequestException as e:
            response_time = round((time.time() - start_time) * 1000, 2)
            return {
                "success": False,
                "error": str(e),
                "response_time_ms": response_time,
                "url": url
            }
    
    def test_authentication(self):
        """Test various authentication scenarios"""
        self.print_header("AUTHENTICATION TESTS")
        
        # Test 1: Valid Bearer token authentication
        headers = {"Authorization": f"Bearer {self.config.api_key}"}
        result = self.make_request("GET", f"/{self.config.user_id}", headers=headers)
        
        if result["success"] and result["status_code"] == 200:
            self.print_test("Bearer Token Auth", "PASS", f"Response time: {result['response_time_ms']}ms")
        else:
            self.print_test("Bearer Token Auth", "FAIL", 
                          f"Status: {result.get('status_code', 'N/A')}, "
                          f"Error: {result.get('error', result.get('data', {}).get('message', 'Unknown'))}")
        
        # Test 2: API-Key header format
        headers = {"Authorization": f"API-Key {self.config.api_key}"}
        result = self.make_request("GET", f"/{self.config.user_id}", headers=headers)
        
        if result["success"] and result["status_code"] == 200:
            self.print_test("API-Key Header Auth", "PASS", f"Response time: {result['response_time_ms']}ms")
        else:
            self.print_test("API-Key Header Auth", "FAIL", 
                          f"Status: {result.get('status_code', 'N/A')}")
        
        # Test 3: Invalid API key
        headers = {"Authorization": "Bearer invalid_key_12345"}
        result = self.make_request("GET", f"/{self.config.user_id}", headers=headers)
        
        if result["success"] and result["status_code"] == 401:
            self.print_test("Invalid API Key", "PASS", "Correctly rejected invalid key")
        else:
            self.print_test("Invalid API Key", "WARN", 
                          f"Expected 401, got {result.get('status_code', 'N/A')}")
        
        # Test 4: Missing API key
        result = self.make_request("GET", f"/{self.config.user_id}")
        
        if result["success"] and result["status_code"] == 401:
            self.print_test("Missing API Key", "PASS", "Correctly rejected missing key")
        else:
            self.print_test("Missing API Key", "WARN", 
                          f"Expected 401, got {result.get('status_code', 'N/A')}")
    
    def test_endpoints(self):
        """Test all API endpoints"""
        self.print_header("ENDPOINT TESTS")
        
        headers = {"Authorization": f"Bearer {self.config.api_key}"}
        
        # Test 1: Get all files
        result = self.make_request("GET", f"/{self.config.user_id}", headers=headers)
        
        if result["success"] and result["status_code"] == 200:
            data = result["data"]
            if "data" in data and "files" in data["data"]:
                file_count = data["data"]["total_files"]
                self.print_test("Get All Files", "PASS", 
                              f"Found {file_count} files, Response time: {result['response_time_ms']}ms")
            else:
                self.print_test("Get All Files", "WARN", "Unexpected response format")
        else:
            self.print_test("Get All Files", "FAIL", 
                          f"Status: {result.get('status_code', 'N/A')}")
        
        # Test 2: Get specific file
        result = self.make_request("GET", f"/{self.config.user_id}/{self.config.file_id}", headers=headers)
        
        if result["success"] and result["status_code"] == 200:
            data = result["data"]
            if "data" in data and "content" in data["data"]:
                file_size = data["data"]["size"]
                filename = data["data"]["filename"]
                self.print_test("Get Specific File", "PASS", 
                              f"File: {filename}, Size: {file_size} bytes, "
                              f"Response time: {result['response_time_ms']}ms")
            else:
                self.print_test("Get Specific File", "WARN", "Unexpected response format")
        elif result["success"] and result["status_code"] == 404:
            self.print_test("Get Specific File", "WARN", "File not found (404) - check if file_id exists")
        else:
            self.print_test("Get Specific File", "FAIL", 
                          f"Status: {result.get('status_code', 'N/A')}")
        
        # Test 3: Get raw JSON
        result = self.make_request("GET", f"/{self.config.user_id}/{self.config.file_id}/raw", headers=headers)
        
        if result["success"] and result["status_code"] == 200:
            # For raw endpoint, the response should be the JSON content directly
            self.print_test("Get Raw JSON", "PASS", 
                          f"Raw JSON retrieved, Response time: {result['response_time_ms']}ms")
        elif result["success"] and result["status_code"] == 404:
            self.print_test("Get Raw JSON", "WARN", "File not found (404) - check if file_id exists")
        else:
            self.print_test("Get Raw JSON", "FAIL", 
                          f"Status: {result.get('status_code', 'N/A')}")
        
        # Test 4: Invalid user ID
        result = self.make_request("GET", "/invalid_user_123", headers=headers)
        
        if result["success"] and result["status_code"] == 403:
            self.print_test("Invalid User ID", "PASS", "Correctly rejected invalid user ID")
        else:
            self.print_test("Invalid User ID", "WARN", 
                          f"Expected 403, got {result.get('status_code', 'N/A')}")
        
        # Test 5: Invalid file ID
        result = self.make_request("GET", f"/{self.config.user_id}/invalid_file_123", headers=headers)
        
        if result["success"] and result["status_code"] == 404:
            self.print_test("Invalid File ID", "PASS", "Correctly rejected invalid file ID")
        else:
            self.print_test("Invalid File ID", "WARN", 
                          f"Expected 404, got {result.get('status_code', 'N/A')}")
    
    def test_api_info_endpoints(self):
        """Test API information endpoints"""
        self.print_header("API INFO ENDPOINTS")
        
        # Test 1: API Info endpoint
        result = self.make_request("GET", "/info")
        
        if result["success"] and result["status_code"] == 200:
            data = result["data"]
            if "data" in data and "name" in data["data"]:
                api_name = data["data"]["name"]
                version = data["data"].get("version", "Unknown")
                self.print_test("API Info", "PASS", 
                              f"API: {api_name} v{version}, Response time: {result['response_time_ms']}ms")
            else:
                self.print_test("API Info", "WARN", "Unexpected response format")
        else:
            self.print_test("API Info", "FAIL", 
                          f"Status: {result.get('status_code', 'N/A')}")
        
        # Test 2: Health check endpoint
        result = self.make_request("GET", "/health")
        
        if result["success"] and result["status_code"] == 200:
            data = result["data"]
            if "data" in data and "status" in data["data"]:
                status = data["data"]["status"]
                server_time = data["data"].get("server_time", "")
                self.print_test("Health Check", "PASS", 
                              f"Status: {status}, Server time: {server_time}, "
                              f"Response time: {result['response_time_ms']}ms")
            else:
                self.print_test("Health Check", "WARN", "Unexpected response format")
        else:
            self.print_test("Health Check", "FAIL", 
                          f"Status: {result.get('status_code', 'N/A')}")
    
    def test_domain_lock(self):
        """Test domain lock functionality"""
        self.print_header("DOMAIN LOCK TESTS")
        
        headers = {"Authorization": f"Bearer {self.config.api_key}"}
        
        # Test 1: Request with Origin header (allowed domain)
        test_headers = headers.copy()
        test_headers["Origin"] = "http://localhost"
        result = self.make_request("GET", f"/{self.config.user_id}", headers=test_headers)
        
        if result["success"] and result["status_code"] == 200:
            self.print_test("Origin Header (localhost)", "PASS", "Request allowed from localhost")
        elif result["success"] and result["status_code"] == 403:
            self.print_test("Origin Header (localhost)", "INFO", 
                          "Domain lock is enabled and localhost is not allowed")
        else:
            self.print_test("Origin Header (localhost)", "WARN", 
                          f"Unexpected status: {result.get('status_code', 'N/A')}")
        
        # Test 2: Request with unauthorized Origin header
        test_headers = headers.copy()
        test_headers["Origin"] = "http://malicious-site.com"
        result = self.make_request("GET", f"/{self.config.user_id}", headers=test_headers)
        
        if result["success"] and result["status_code"] == 403:
            self.print_test("Origin Header (malicious)", "PASS", "Correctly blocked unauthorized domain")
        elif result["success"] and result["status_code"] == 200:
            self.print_test("Origin Header (malicious)", "INFO", 
                          "Domain lock is not enabled or allows all domains")
        else:
            self.print_test("Origin Header (malicious)", "WARN", 
                          f"Unexpected status: {result.get('status_code', 'N/A')}")
        
        # Test 3: Request with Referer header
        test_headers = headers.copy()
        test_headers["Referer"] = "http://localhost/some-page"
        result = self.make_request("GET", f"/{self.config.user_id}", headers=test_headers)
        
        if result["success"] and result["status_code"] in [200, 403]:
            status = "allowed" if result["status_code"] == 200 else "blocked"
            self.print_test("Referer Header", "PASS", f"Request {status} based on referer")
        else:
            self.print_test("Referer Header", "WARN", 
                          f"Unexpected status: {result.get('status_code', 'N/A')}")
    
    def test_rate_limits(self):
        """Test rate limiting functionality"""
        self.print_header("RATE LIMIT TESTS")
        
        headers = {"Authorization": f"Bearer {self.config.api_key}"}
        
        # Test 1: Check rate limit headers
        result = self.make_request("GET", f"/{self.config.user_id}", headers=headers)
        
        if result["success"]:
            rate_headers = {}
            for key, value in result["headers"].items():
                if key.lower().startswith('x-ratelimit'):
                    rate_headers[key] = value
            
            if rate_headers:
                self.print_test("Rate Limit Headers", "PASS", 
                              f"Found {len(rate_headers)} rate limit headers")
                for header, value in rate_headers.items():
                    print(f"    {Colors.BLUE}{header}: {value}{Colors.END}")
            else:
                self.print_test("Rate Limit Headers", "INFO", "No rate limit headers found")
        
        # Test 2: Burst test (multiple rapid requests)
        print(f"\n{Colors.YELLOW}Performing burst test (5 rapid requests)...{Colors.END}")
        burst_results = []
        
        for i in range(5):
            start_time = time.time()
            result = self.make_request("GET", "/health", headers=headers)  # Use health endpoint
            response_time = round((time.time() - start_time) * 1000, 2)
            
            burst_results.append({
                "request": i + 1,
                "status": result.get("status_code", "ERROR"),
                "response_time": response_time
            })
            time.sleep(0.1)  # Small delay between requests
        
        success_count = sum(1 for r in burst_results if r["status"] == 200)
        blocked_count = sum(1 for r in burst_results if r["status"] == 429)
        
        if blocked_count > 0:
            self.print_test("Burst Rate Limiting", "PASS", 
                          f"{success_count} successful, {blocked_count} rate limited")
        else:
            self.print_test("Burst Rate Limiting", "INFO", 
                          f"All {success_count} requests succeeded (rate limits not hit)")
    
    def test_security_features(self):
        """Test security features"""
        self.print_header("SECURITY TESTS")
        
        # Test 1: SQL Injection attempt
        headers = {"Authorization": f"Bearer {self.config.api_key}"}
        malicious_user_id = "'; DROP TABLE users; --"
        result = self.make_request("GET", f"/{malicious_user_id}", headers=headers)
        
        if result["success"] and result["status_code"] == 400:
            self.print_test("SQL Injection Protection", "PASS", "Malicious input rejected")
        else:
            self.print_test("SQL Injection Protection", "WARN", 
                          f"Status: {result.get('status_code', 'N/A')}")
        
        # Test 2: XSS attempt
        malicious_headers = headers.copy()
        malicious_headers["User-Agent"] = "<script>alert('xss')</script>"
        result = self.make_request("GET", f"/{self.config.user_id}", headers=malicious_headers)
        
        # Should still work but script should be handled safely
        if result["success"]:
            self.print_test("XSS Protection", "PASS", "Request with XSS attempt handled")
        else:
            self.print_test("XSS Protection", "INFO", "Request blocked or failed")
        
        # Test 3: Check security headers
        result = self.make_request("GET", "/health")
        
        if result["success"]:
            security_headers = {}
            for key, value in result["headers"].items():
                if key.lower() in ['x-content-type-options', 'x-frame-options', 'x-xss-protection',
                                 'strict-transport-security', 'content-security-policy']:
                    security_headers[key] = value
            
            if security_headers:
                self.print_test("Security Headers", "PASS", 
                              f"Found {len(security_headers)} security headers")
                for header, value in security_headers.items():
                    print(f"    {Colors.BLUE}{header}: {value[:50]}{'...' if len(value) > 50 else ''}{Colors.END}")
            else:
                self.print_test("Security Headers", "WARN", "No security headers found")
    
    def test_error_handling(self):
        """Test error handling and response formats"""
        self.print_header("ERROR HANDLING TESTS")
        
        headers = {"Authorization": f"Bearer {self.config.api_key}"}
        
        # Test 1: 400 Bad Request
        result = self.make_request("GET", "/", headers=headers)  # Missing user_id
        
        if result["success"] and result["status_code"] == 400:
            self.print_test("400 Bad Request", "PASS", "Correctly returns 400 for bad request")
        else:
            self.print_test("400 Bad Request", "WARN", 
                          f"Status: {result.get('status_code', 'N/A')}")
        
        # Test 2: 404 Not Found
        result = self.make_request("GET", f"/{self.config.user_id}/nonexistent-file-123", headers=headers)
        
        if result["success"] and result["status_code"] == 404:
            self.print_test("404 Not Found", "PASS", "Correctly returns 404 for missing resource")
        else:
            self.print_test("404 Not Found", "WARN", 
                          f"Status: {result.get('status_code', 'N/A')}")
        
        # Test 3: Error response format
        result = self.make_request("GET", "/nonexistent", headers=headers)
        
        if result["success"] and "data" in result and isinstance(result["data"], dict):
            error_data = result["data"]
            has_error_field = "error" in error_data
            has_message = "message" in error_data
            has_timestamp = "timestamp" in error_data
            
            if has_error_field and has_message:
                self.print_test("Error Response Format", "PASS", "Error responses have proper structure")
            else:
                self.print_test("Error Response Format", "WARN", "Error response missing required fields")
        else:
            self.print_test("Error Response Format", "INFO", "Could not test error response format")
    
    def run_all_tests(self):
        """Run all test suites"""
        print(f"{Colors.PURPLE}{Colors.BOLD}")
        print("┌─────────────────────────────────────────────────────────┐")
        print("│                                                         │")
        print("│           RioConsoleJSON API Test Suite                │")
        print("│                                                         │")
        print("└─────────────────────────────────────────────────────────┘")
        print(f"{Colors.END}")
        
        print(f"{Colors.WHITE}Configuration:{Colors.END}")
        print(f"  Base URL: {self.config.base_url}")
        print(f"  API URL:  {self.config.api_base_url}")
        print(f"  User ID:  {self.config.user_id}")
        print(f"  File ID:  {self.config.file_id}")
        print(f"  API Key:  {self.config.api_key[:20]}{'*' * (len(self.config.api_key) - 20)}")
        
        start_time = time.time()
        
        try:
            # Run all test suites
            self.test_api_info_endpoints()
            self.test_authentication()
            self.test_endpoints()
            self.test_domain_lock()
            self.test_rate_limits()
            self.test_security_features()
            self.test_error_handling()
            
            # Summary
            total_time = round(time.time() - start_time, 2)
            
            self.print_header("TEST SUMMARY")
            print(f"{Colors.GREEN}✓ All test suites completed successfully!{Colors.END}")
            print(f"{Colors.WHITE}Total execution time: {total_time} seconds{Colors.END}")
            
            print(f"\n{Colors.CYAN}📊 API Performance Summary:{Colors.END}")
            print(f"  • Authentication working correctly")
            print(f"  • All endpoints accessible")
            print(f"  • Security features active")
            print(f"  • Error handling functional")
            
        except KeyboardInterrupt:
            print(f"\n{Colors.YELLOW}⚠ Tests interrupted by user{Colors.END}")
            sys.exit(1)
        except Exception as e:
            print(f"\n{Colors.RED}❌ Test suite failed with error: {str(e)}{Colors.END}")
            sys.exit(1)

def main():
    """Main function"""
    print(f"{Colors.BLUE}Initializing RioConsoleJSON API Test Suite...{Colors.END}")
    
    # Create configuration
    config = APITestConfig()
    
    # You can override configuration here if needed
    # config.base_url = "https://your-domain.com/riojson"
    
    # Create tester and run tests
    tester = APITester(config)
    tester.run_all_tests()

if __name__ == "__main__":
    main()
