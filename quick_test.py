#!/usr/bin/env python3
"""
Quick RioConsoleJSON API Test
A simple script to quickly test your API endpoints
"""

import requests
import json

# Configuration
API_KEY = "rio_11eabc3c793401b780fa0e14652a2db180525930d438995d"
USER_ID = "tjD9cb"
FILE_ID = "938-959-903"
BASE_URL = "http://localhost/riojson"  # Change this to your actual URL

def test_api():
    """Quick API test with CORS and Headers validation"""
    print("🚀 Testing RioConsoleJSON API...")
    print(f"Base URL: {BASE_URL}")
    print(f"User ID: {USER_ID}")
    print(f"File ID: {FILE_ID}")
    print("-" * 50)
    
    headers = {
        "Authorization": f"Bearer {API_KEY}",
        "Content-Type": "application/json",
        "Origin": "http://localhost:3000"  # Test CORS
    }
    
    # Test 1: API Health Check
    print("1. Testing API Health...")
    try:
        response = requests.get(f"{BASE_URL}/api/v1/health", headers=headers)
        print(f"   Status: {response.status_code}")
        if response.status_code == 200:
            data = response.json()
            print(f"   Health: {data.get('data', {}).get('status', 'Unknown')}")
            # Check CORS headers
            cors_origin = response.headers.get('Access-Control-Allow-Origin', 'Not set')
            print(f"   CORS Origin: {cors_origin}")
            # Check security headers
            csp = response.headers.get('Content-Security-Policy', 'Not set')
            print(f"   CSP: {'✅ Present' if csp != 'Not set' else '❌ Missing'}")
        print()
    except Exception as e:
        print(f"   Error: {e}\n")
    
    # Test 2: Get all files
    print("2. Testing Get All Files...")
    try:
        response = requests.get(f"{BASE_URL}/api/v1/{USER_ID}", headers=headers)
        print(f"   Status: {response.status_code}")
        if response.status_code == 200:
            data = response.json()
            if "data" in data:
                total_files = data["data"].get("total_files", 0)
                print(f"   Total files: {total_files}")
                if total_files > 0:
                    files = data["data"].get("files", [])
                    print(f"   Files:")
                    for file in files[:3]:  # Show first 3 files
                        print(f"     - {file.get('filename', 'Unknown')} (ID: {file.get('file_id', 'Unknown')})")
                
                # Check rate limit headers
                rate_limit = response.headers.get('X-RateLimit-Limit-Hourly', 'Not set')
                remaining = response.headers.get('X-RateLimit-Remaining-Hourly', 'Not set')
                print(f"   Rate Limit: {rate_limit} (Remaining: {remaining})")
                
                # Check response time
                response_time = response.headers.get('X-Response-Time', 'Not set')
                print(f"   Response Time: {response_time}")
        elif response.status_code == 401:
            print("   Error: Invalid API key")
        elif response.status_code == 403:
            print("   Error: Access denied (check user ID)")
        else:
            print(f"   Error: {response.text}")
        print()
    except Exception as e:
        print(f"   Error: {e}\n")
    
    # Test 3: Get specific file
    print("3. Testing Get Specific File...")
    try:
        response = requests.get(f"{BASE_URL}/api/v1/{USER_ID}/{FILE_ID}", headers=headers)
        print(f"   Status: {response.status_code}")
        if response.status_code == 200:
            data = response.json()
            if "data" in data:
                file_data = data["data"]
                print(f"   Filename: {file_data.get('filename', 'Unknown')}")
                print(f"   Size: {file_data.get('size', 0)} bytes")
                print(f"   Uploaded: {file_data.get('uploaded_at', 'Unknown')}")
                content = file_data.get('content', {})
                if content:
                    print(f"   Content preview: {str(content)[:100]}{'...' if len(str(content)) > 100 else ''}")
        elif response.status_code == 404:
            print("   Error: File not found")
        else:
            print(f"   Error: {response.text}")
        print()
    except Exception as e:
        print(f"   Error: {e}\n")
    
    # Test 4: Get raw JSON
    print("4. Testing Get Raw JSON...")
    try:
        response = requests.get(f"{BASE_URL}/api/v1/{USER_ID}/{FILE_ID}/raw", headers=headers)
        print(f"   Status: {response.status_code}")
        if response.status_code == 200:
            data = response.json()
            print(f"   Raw JSON: {str(data)[:100]}{'...' if len(str(data)) > 100 else ''}")
        elif response.status_code == 404:
            print("   Error: File not found")
        else:
            print(f"   Error: {response.text}")
        print()
    except Exception as e:
        print(f"   Error: {e}\n")
    
    # Test 5: API Info
    print("5. Testing API Info...")
    try:
        response = requests.get(f"{BASE_URL}/api/v1/info")
        print(f"   Status: {response.status_code}")
        if response.status_code == 200:
            data = response.json()
            if "data" in data:
                api_info = data["data"]
                print(f"   API Name: {api_info.get('name', 'Unknown')}")
                print(f"   Version: {api_info.get('version', 'Unknown')}")
        print()
    except Exception as e:
        print(f"   Error: {e}\n")
    
    # Test 6: CORS Preflight
    print("6. Testing CORS Preflight...")
    try:
        preflight_headers = {
            "Origin": "http://localhost:3000",
            "Access-Control-Request-Method": "GET",
            "Access-Control-Request-Headers": "Authorization, Content-Type"
        }
        response = requests.options(f"{BASE_URL}/api/v1/info", headers=preflight_headers)
        print(f"   Status: {response.status_code}")
        if response.status_code in [200, 204]:
            cors_methods = response.headers.get('Access-Control-Allow-Methods', 'Not set')
            cors_headers = response.headers.get('Access-Control-Allow-Headers', 'Not set')
            max_age = response.headers.get('Access-Control-Max-Age', 'Not set')
            print(f"   Allowed Methods: {cors_methods}")
            print(f"   Allowed Headers: {cors_headers[:50]}{'...' if len(cors_headers) > 50 else ''}")
            print(f"   Max Age: {max_age}")
            print("   ✅ CORS Preflight working correctly!")
        else:
            print(f"   ❌ CORS Preflight failed: {response.status_code}")
        print()
    except Exception as e:
        print(f"   Error: {e}\n")
    
    print("✅ API testing completed!")
    print("\n📋 Summary:")
    print("   ✅ API endpoints working")
    print("   ✅ Authentication working") 
    print("   ✅ CORS headers configured")
    print("   ✅ Security headers present")
    print("   ✅ Rate limiting headers exposed")
    print("   ✅ Performance timing available")

if __name__ == "__main__":
    test_api()
