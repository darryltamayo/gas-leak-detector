# System Test Suite - Quick Start Guide

## 🔍 Comprehensive System Test

A complete test suite has been created to check all functionality of your AC Controller System and identify any bugs or issues.

## How to Run the Test

### Running the Test (Works on Both Local & Online)
1. Make sure your web server is running (XAMPP locally, or online hosting)
2. Open your web browser
3. Navigate to your public folder:
   - **Online**: `https://your-domain.com/public/test-system.php`
   - **Local**: `http://localhost/ac_controller_system/public/test-system.php`
4. Wait for the test to complete (usually 5-10 seconds)

**Note:** The test file automatically detects your server URL, so it works on both local development and online production servers without any configuration changes.

## What the Test Checks

The test suite performs **20 comprehensive test categories**:

### ✅ Core System Tests
1. **Environment & Configuration** - Checks .env file, API keys, database config
2. **Database Connection** - Tests database connectivity and charset
3. **Database Tables** - Verifies all required tables exist
4. **Database Views** - Checks if views are properly created
5. **User Accounts** - Validates admin and user accounts
6. **Devices** - Tests ESP32 device registration and status
7. **Authorized SIM Cards** - Checks SMS control configuration
8. **Schedules** - Validates scheduling system and overlap detection
9. **Activity Logs** - Tests logging functionality
10. **File Permissions** - Checks writable directories

### 🔧 Technical Tests
11. **File Structure** - Verifies all required files exist
12. **API Endpoints** - Tests API configuration
13. **Cron Jobs** - Checks schedule execution setup
14. **Session Configuration** - Validates PHP sessions
15. **PHP Extensions** - Ensures required extensions are loaded
16. **Security Checks** - Tests for common security issues
17. **ESP32 Integration** - Verifies ESP32 code and configuration

### 📦 Code Quality Tests
18. **Repository Classes** - Checks all repository files
19. **Controller Classes** - Validates all controllers exist
20. **View Files** - Tests critical view files
21. **Common Bugs** - Scans for deprecated functions and vulnerabilities

## Understanding the Results

### Test Report Components

**Overall Status:**
- 🟢 **Excellent** - 100% pass rate, system is fully functional
- 🟡 **Good** - 80-99% pass rate, minor issues
- 🟠 **Needs Attention** - 60-79% pass rate, several issues found
- 🔴 **Critical Issues** - <60% pass rate, major problems detected

**Test Results:**
- ✅ **Passed** - Component is working correctly
- ❌ **Failed** - Critical issue detected, requires attention
- ⚠️ **Warning** - Not critical but should be reviewed

### Common Issues and Solutions

#### Database Connection Failed
```
Solution: Check your .env file database credentials
- DB_HOST=localhost
- DB_NAME=u907280979_docrequest
- DB_USER=root
- DB_PASS=your_password
```

#### Missing Tables
```
Solution: Import the database schema
1. Open phpMyAdmin
2. Select your database
3. Import: database/complete_schema.sql
```

#### API Key Not Configured
```
Solution: Add API key to .env file
API_KEY=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
```

#### Device Offline
```
Solution: 
1. Check ESP32 is powered on
2. Verify WiFi credentials in ESP32 code
3. Check API key matches database
4. Review ESP32 serial monitor for errors
```

#### No Admin User
```
Solution: The schema creates default admin
Username: admin
Password: password
(Change password after first login!)
```

## What to Do After Running Tests

### If All Tests Pass ✅
Your system is fully functional! You can:
1. Log in to the web interface
2. Configure devices and schedules
3. Upload ESP32 code to your hardware
4. Start controlling your outlets

### If Tests Fail ❌
1. **Review Failed Tests** - Each failed test includes error details
2. **Check Warnings** - Address warnings to prevent future issues
3. **Fix Issues** - Follow the error messages to resolve problems
4. **Re-run Test** - Refresh the page to verify fixes

### Priority Order for Fixes
1. **Database connection** - Nothing works without this
2. **Missing tables** - Import the schema
3. **No admin user** - Can't access the system
4. **API configuration** - Required for ESP32 communication
5. **File permissions** - Needed for logs and caching

## Security Note

⚠️ **IMPORTANT:** This test file should be **deleted or restricted** in production environments!

To secure it:
```php
// Add to the top of test-system.php
if ($_ENV['APP_ENV'] === 'production') {
    die('Tests are disabled in production');
}
```

Or delete it entirely:
```bash
del public\test-system.php
```

## Need Help?

If tests reveal issues you can't resolve:
1. Check the error details in the test report
2. Review the DATABASE_RESET_INSTRUCTIONS.txt
3. Consult the FINAL_DEPLOYMENT_GUIDE.md
4. Check ESP32 documentation in esp32/README.md

## Advanced: Running Specific Tests

To modify which tests run, edit `public/test-system.php` and comment out test sections you don't need.

---

**Happy Testing! 🚀**
