# 🚀 RioConsoleJSON - Professional JSON Hosting Platform

**Host JSON files like a boss** - A complete, production-ready JSON hosting platform with advanced admin tools and real-time monitoring.

## ✨ Features

### 🔐 **User Management**
- **Secure Authentication** - Login/signup with password hashing
- **User Types** - Free, Paid, and Admin accounts with different limits
- **Account Management** - Profile settings, storage tracking, and usage stats
- **Visual Badges** - Clear user type indicators throughout the interface

### 📁 **JSON File Management** 
- **JSON Silos** - Store and organize JSON files with custom names
- **File Editor** - Built-in editor with syntax highlighting and validation
- **File Upload** - Drag & drop JSON file upload with size validation
- **JSON Tools** - Format, minify, and validate JSON with real-time feedback
- **Storage Display** - Clean MB/GB/TB storage formatting and usage tracking

### 🔑 **API System**
- **REST API** - Full API access for all JSON files
- **API Keys** - Secure API key management with custom names
- **Domain Lock** - Optional domain restrictions for API keys
- **Rate Limiting** - Built-in rate limiting and request monitoring
- **API Documentation** - Interactive API docs with examples

### 👨‍💼 **Admin Panel**
- **Admin Dashboard** - Comprehensive system overview and statistics
- **User Management** - View, edit, and manage all user accounts
- **Live Request Monitor** - Real-time API request monitoring and analytics
- **User Limits** - Bulk update file and storage limits by user type
- **System Backup** - Complete database backup and import functionality

### 🎨 **Modern Interface**
- **Dark Theme** - Professional dark-themed responsive design
- **Real-time Updates** - Live statistics and instant feedback
- **Mobile Friendly** - Responsive design works on all devices
- **Clean UI** - Intuitive navigation and modern components

## 🗄️ Database Structure

The system uses 5 main tables:

- **`accounts`** - User management (4 users)
- **`user_json_files`** - JSON file storage (2 files)
- **`api_keys`** - API key management (7 keys)
- **`api_request_logs`** - Request monitoring (real-time logging)
- **`migrations`** - Framework migration tracking

## 🚀 Quick Setup

### Prerequisites
- XAMPP (Apache + MySQL)
- PHP 7.4+ with MySQLi extension
- Web browser

### Installation Steps

1. **Start XAMPP Services**
   ```
   - Open XAMPP Control Panel
   - Start Apache ✅
   - Start MySQL ✅
   ```

2. **Setup Database**
   ```
   - Go to: http://localhost/phpmyadmin
   - Click "SQL" tab
   - Copy & paste contents from database_setup.sql
   - Click "Go" to execute
   ```

3. **Configure Environment**
   ```
   - Copy .env.example to .env (if needed)
   - Update database settings in .env:
     database.default.hostname = localhost
     database.default.database = riojson_data
     database.default.username = root
     database.default.password = 
   ```

4. **Access Your Platform**
   ```
   - Visit: http://localhost/riojson_1
   - Click "Go to Console"
   - Sign up for a new account or login
   ```

## 🎯 Usage Examples

### Creating JSON Files
1. Login to your dashboard
2. Go to "JSON Silos" section
3. Click "Create New Silo"
4. Either paste JSON directly or upload a .json file
5. Your file gets a unique URL for API access

### Using the API
```bash
# Get your JSON file
curl -H "X-API-Key: rio_your_api_key_here" \
     https://yoursite.com/api/v1/json/your_file_id

# Response
{
  "success": true,
  "data": { your_json_content },
  "info": {
    "file_id": "your_file_id",
    "size": "1.2 MB",
    "last_modified": "2025-06-27 07:04:21"
  }
}
```

### Admin Features
- **Live Monitoring**: View all API requests in real-time
- **User Management**: Edit user types and limits
- **System Backup**: Download complete database backups
- **Analytics**: Track usage patterns and performance metrics

## 🔧 Technical Details

### Built With
- **CodeIgniter 4** - PHP framework
- **Bootstrap 5** - UI framework with dark theme
- **MySQL** - Database with proper indexing
- **jQuery** - Frontend interactions
- **Font Awesome** - Icons and UI elements

### Security Features
- Password hashing with PHP's `password_hash()`
- SQL injection prevention with prepared statements
- CSRF protection on all forms
- API key authentication
- Rate limiting on API endpoints
- Domain lock restrictions for API keys

### Performance
- Database indexes on frequently queried columns
- Efficient storage calculation and caching
- Optimized JSON handling for large files
- Clean URL structure for SEO

## 📊 Current System Stats

Based on the live database scan:
- **Total Users**: 4 (including admin accounts)
- **JSON Files**: 2 files stored
- **API Keys**: 7 active keys
- **Request Logs**: Real-time monitoring enabled
- **Storage**: Efficient LONGTEXT storage for JSON content

## 🛠️ Maintenance

### Cache Cleanup
The platform includes automatic cache management, but you can manually clean:
```bash
# Run the cleanup script
php cleanup_cache.php
```

### Database Backup
Use the built-in admin backup tool or:
```bash
# Run the database scanner
php scan_database.php
```

### Log Management
Logs are automatically rotated, but check:
- `writable/logs/` - Application logs
- `writable/debugbar/` - Debug information (can be cleared)

## 🔒 Admin Access

Default admin credentials (change immediately after setup):
- **Email**: admin@admin.com  
- **Password**: Check your database for the hashed password

Admin features include:
- System dashboard with real-time stats
- User management and limit configuration
- Live API request monitoring
- Complete database backup/import tools

## 📝 API Documentation

Full API documentation is available at:
```
http://localhost/riojson_1/dashboard/api-docs
```

Includes:
- Authentication methods
- All available endpoints
- Request/response examples
- Rate limiting information
- Error codes and handling

---

**RioConsoleJSON** - Professional JSON hosting made simple. Perfect for developers who need reliable JSON storage with powerful admin tools and real-time monitoring capabilities.
