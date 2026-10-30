# 🌐 Online Deployment Quick Reference

## Important URLs for Your Online Server

### Your Installation Path
Your system is installed at: `/ac_controller_system` subdirectory

### Application URLs
```
Main App:     https://isu-documentrequest.online/ac_controller_system/
Dashboard:    https://isu-documentrequest.online/ac_controller_system/dashboard
Login:        https://isu-documentrequest.online/ac_controller_system/auth/login
Test System:  https://isu-documentrequest.online/ac_controller_system/public/test-system.php
API Test:     https://isu-documentrequest.online/ac_controller_system/public/test-api-connection.php
Outlet Control: https://isu-documentrequest.online/ac_controller_system/outlet
Schedules:    https://isu-documentrequest.online/ac_controller_system/schedule
```

### API Endpoints (for ESP32)
```
Base URL:     https://isu-documentrequest.online/ac_controller_system/
Heartbeat:    https://isu-documentrequest.online/ac_controller_system/api/heartbeat
Status:       https://isu-documentrequest.online/ac_controller_system/api/status
Control:      https://isu-documentrequest.online/ac_controller_system/api/control
```

## ⚙️ Environment Configuration for Online Server

Update your `.env` file with your online server details:

```env
# Application Settings
APP_NAME="IoT Smart Outlet System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://isu-documentrequest.online/ac_controller_system
APP_TIMEZONE=Asia/Manila

# Database Configuration (Get from your hosting provider)
DB_HOST=localhost
DB_PORT=3306
DB_NAME=u907280979_docrequest
DB_USER=your_database_username
DB_PASS=your_database_password

# Security
API_KEY=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
CSRF_TOKEN_LENGTH=32

# Session
SESSION_LIFETIME=7200
SESSION_NAME=IOT_OUTLET_SESSION

# ESP32 Settings
ESP32_HEARTBEAT_TIMEOUT=120

# Logging
LOG_LEVEL=error
LOG_PATH=../storage/logs/
```

## 📤 Upload Checklist

Before uploading to your online server:

### ✅ Files to Upload
- [ ] All `/app` folder contents
- [ ] All `/config` folder contents
- [ ] All `/public` folder contents
- [ ] All `/routes` folder contents
- [ ] All `/storage` folder contents (make sure writable)
- [ ] All `/database` folder contents
- [ ] All `/cron` folder contents
- [ ] All `/esp32` folder contents
- [ ] `.htaccess` file (root directory)
- [ ] `index.php` file (root directory)
- [ ] `.env` file (create from `.env.example`)

### ❌ Files NOT to Upload
- [ ] `.git/` folder
- [ ] `node_modules/` folder (if any)
- [ ] Any local test files
- [ ] `.env.example` can be omitted (optional)

### 🔒 Security After Upload

1. **Set Directory Permissions**
   ```
   /storage/logs - 755 or 775 (must be writable)
   /storage/cache - 755 or 775 (must be writable)
   All other folders - 755
   ```

2. **Set File Permissions**
   ```
   .env - 644 (read-only for web server)
   All .php files - 644
   ```

3. **Protect Sensitive Files**
   Make sure `.htaccess` is uploaded to prevent direct access to:
   - `.env` file
   - `/app` directory
   - `/config` directory
   - `/storage` directory

4. **Test .env Protection**
   Try accessing: `https://your-domain.com/.env`
   Should show: **403 Forbidden** or **404 Not Found**

## 🗄️ Database Setup on Online Server

1. **Login to cPanel/Hosting Panel**
2. **Go to phpMyAdmin**
3. **Create Database** (or use existing: `u907280979_docrequest`)
4. **Import Schema**
   - Select your database
   - Click "Import"
   - Choose file: `database/complete_schema.sql`
   - Click "Go"
5. **Verify Tables Created** (should see 7 tables + 3 views)

## 🔑 Default Admin Credentials

After database import, login with:
```
Username: admin
Password: password
```

**⚠️ IMPORTANT:** Change password immediately after first login!

## 🧪 Run System Test

After deployment:
1. Go to: `https://your-domain.com/public/test-system.php`
2. Review all test results
3. Fix any failed tests
4. **Delete test file when done:** `rm public/test-system.php`

## 📱 Configure ESP32 for Online Server

Update your ESP32 code:

```cpp
// WiFi credentials
const char* ssid = "Your_WiFi_Name";
const char* password = "Your_WiFi_Password";

// Server configuration (CHANGE THESE!)
const char* serverUrl = "isu-documentrequest.online";  // NO http:// or https://
const int serverPort = 443;  // Use 443 for HTTPS, 80 for HTTP
const bool useHTTPS = true;  // Set to true for HTTPS

// API Base Path (IMPORTANT: Include subdirectory!)
const char* basePath = "/ac_controller_system";  // Your installation path

// API credentials (from database)
String API_KEY = "IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T";
String DEVICE_ID = "ESP32_OUTLET_001";
```

## 🔄 Setup Cron Job (Schedule Automation)

### On cPanel Hosting:
1. Go to **Cron Jobs** in cPanel
2. Add new cron job:
   ```
   * * * * * /usr/bin/php /home/username/public_html/cron/execute_schedules.php
   ```
3. Replace `/home/username/public_html` with your actual path

### Command Runs Every Minute:
```
* * * * * = Every minute
/usr/bin/php = PHP binary path (may vary: /usr/local/bin/php)
/path/to/cron/execute_schedules.php = Your cron script
```

### Test Cron Manually:
```bash
php /home/username/public_html/cron/execute_schedules.php
```

## 🐛 Common Online Deployment Issues

### Issue: "Database Connection Failed"
**Solution:** Check database credentials in `.env`. Get correct values from hosting control panel.

### Issue: "500 Internal Server Error"
**Solution:** 
- Check PHP error logs in cPanel
- Verify file permissions (755 for directories, 644 for files)
- Check `.htaccess` syntax

### Issue: "API Not Working"
**Solution:**
- Verify API_KEY in `.env` matches device API key in database
- Check if mod_rewrite is enabled on server
- Test API endpoint directly

### Issue: "Cron Not Running"
**Solution:**
- Verify cron command path is correct
- Check PHP binary path: `which php` in SSH
- Review cron logs in cPanel

### Issue: "ESP32 Can't Connect"
**Solution:**
- Verify domain name is correct (no http://)
- Check if SSL certificate is valid (for HTTPS)
- Ensure server firewall allows incoming connections
- Test API manually: `https://your-domain.com/public/api.php?action=heartbeat`

## 📊 Post-Deployment Verification

Run through this checklist:

- [ ] Can access website at your domain
- [ ] Can login with admin credentials
- [ ] Test system shows all green
- [ ] Can create/edit users
- [ ] Can register devices
- [ ] Can add schedules
- [ ] Can control outlets manually
- [ ] Can add authorized SIM cards
- [ ] Activity logs are recording
- [ ] Cron job is executing (check logs after 1 minute)
- [ ] ESP32 connects successfully
- [ ] ESP32 heartbeat shows in dashboard

## 🆘 Need Help?

1. Run the test system first: `public/test-system.php`
2. Check detailed error messages
3. Review PHP error logs
4. Verify all environment variables in `.env`
5. Test database connection separately

## 🎯 Quick Commands Reference

### Via SSH (if available):
```bash
# Check PHP version
php -v

# Test database connection
php -r "new PDO('mysql:host=localhost;dbname=yourdb', 'user', 'pass');"

# Check permissions
ls -la storage/logs/

# Run cron manually
php cron/execute_schedules.php

# View error logs
tail -f storage/logs/error.log
```

---

**Remember:** The test file (`test-system.php`) automatically detects whether you're on localhost or online server, so it works everywhere without changes! 🚀
