# 📤 Upload Checklist for Online Server

## Before Upload

### ✅ Local Preparation
- [ ] Run `fix-permissions.bat` to create missing directories
- [ ] Verify `.env` file exists and is configured
- [ ] Update `.env` with online database credentials
- [ ] Change `APP_URL` to your online domain
- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Run local test to ensure everything works

### ✅ Directory Structure Check
Run this command in project root:
```bash
# Windows
dir storage /s

# Should show:
# storage\
# storage\cache\
# storage\logs\
```

---

## Upload to Server

### ✅ Files to Upload
Upload ALL files and folders:
- [ ] `/app` folder (all controllers, models, views)
- [ ] `/config` folder (all config files)
- [ ] `/cron` folder (schedule execution script)
- [ ] `/database` folder (SQL schema)
- [ ] `/esp32` folder (Arduino code and guides)
- [ ] `/public` folder (web root files)
- [ ] `/routes` folder (API routes)
- [ ] `/storage` folder (**including empty subdirectories!**)
  - [ ] `/storage/cache` folder
  - [ ] `/storage/logs` folder
- [ ] `.env` file (configured for production)
- [ ] `.htaccess` file (important for security!)
- [ ] `index.php` file

### ❌ Files NOT to Upload (Optional)
These are documentation/development files:
- [ ] `.git/` folder (if exists)
- [ ] `README_TEST.md` (optional)
- [ ] `FIX_WARNINGS_GUIDE.md` (optional)
- [ ] `UPLOAD_CHECKLIST.md` (this file)
- [ ] Test files (can delete after testing):
  - `public/test-system.php`
  - `public/test-api-connection.php`

---

## After Upload

### ✅ Fix Permissions (IMPORTANT!)

**Option 1: Via SSH (Recommended)**
```bash
cd /home/u907280979/public_html
bash fix-permissions.sh
```

**Option 2: Via cPanel File Manager**
1. Go to File Manager
2. Navigate to `storage/cache` folder
3. Right-click → Change Permissions → Set to **755** or **775**
4. Repeat for `storage/logs` folder

**Option 3: Manual Commands**
```bash
chmod 755 storage/
chmod 775 storage/cache/
chmod 775 storage/logs/
chmod 644 .env
```

### ✅ Import Database
1. Login to phpMyAdmin
2. Select database: `u907280979_docrequest`
3. Click "Import"
4. Choose file: `database/complete_schema.sql`
5. Click "Go"
6. Verify 7 tables + 3 views created

### ✅ Run System Tests
1. **Full System Test:**
   ```
   https://isu-documentrequest.online/public/test-system.php
   ```
   Should show: ✅ cache directory exists and is writable
   Should show: ✅ logs directory exists and is writable
   Should show: ✅ Password hashing available (Bcrypt working)

2. **API Connection Test:**
   ```
   https://isu-documentrequest.online/public/test-api-connection.php
   ```
   Should return JSON responses

3. **Main Application:**
   ```
   https://isu-documentrequest.online/public/
   ```
   Should show login page

### ✅ Setup Cron Job
1. Login to cPanel
2. Find "Cron Jobs"
3. Add new cron:
   - **Frequency:** Every minute (`* * * * *`)
   - **Command:** `/usr/bin/php /home/u907280979/public_html/cron/execute_schedules.php`
4. Save

### ✅ Security Check
Test these URLs - should be DENIED:
- `https://isu-documentrequest.online/.env` → 403/404
- `https://isu-documentrequest.online/storage/` → 403/404
- `https://isu-documentrequest.online/config/` → 403/404
- `https://isu-documentrequest.online/app/` → 403/404

### ✅ Login Test
1. Go to: `https://isu-documentrequest.online/public/`
2. Login with:
   - **Username:** admin
   - **Password:** password
3. Should see dashboard
4. **IMPORTANT:** Change password immediately!

---

## Common Upload Issues

### Issue: Empty directories not uploaded
**Solution:** 
- Make sure `.gitkeep` files are in each directory
- Or manually create directories after upload
- Or use `fix-permissions.sh` script

### Issue: Permission errors
**Symptoms:**
- "Permission denied" errors
- Can't write to logs
- Cache errors

**Solution:**
```bash
# Via SSH
cd /home/u907280979/public_html
bash fix-permissions.sh

# Or manually
chmod 775 storage/cache/
chmod 775 storage/logs/
```

### Issue: .htaccess not working
**Symptoms:**
- Can access .env file from browser
- 404 errors on all pages
- API not working

**Solution:**
1. Verify `.htaccess` uploaded to root directory
2. Check if mod_rewrite is enabled (ask hosting support)
3. Verify AllowOverride is enabled

### Issue: Database connection failed
**Solution:**
1. Check `.env` database credentials
2. Verify database name matches cPanel
3. Ensure database user has permissions
4. Test connection in phpMyAdmin

### Issue: 500 Internal Server Error
**Solution:**
1. Check PHP error logs in cPanel
2. Verify file permissions (644 for files, 755 for directories)
3. Check `.htaccess` syntax
4. Ensure PHP version is 7.4 or higher

---

## Post-Upload Verification

### ✅ File Structure Check
Your server should have this structure:
```
/home/u907280979/public_html/
├── app/
├── config/
├── cron/
├── database/
├── esp32/
├── public/
├── routes/
├── storage/
│   ├── cache/     ← Must exist and be writable!
│   └── logs/      ← Must exist and be writable!
├── .env           ← Must exist and be configured!
├── .htaccess      ← Must exist for security!
└── index.php
```

### ✅ Test Results Expected
After all fixes, your test should show:

```
Overall Status: Excellent (or Good)
Total Tests: ~80+
Passed: 80+ (95%+)
Failed: 0
Warnings: 0-2 (only ESP32/cron if not setup yet)
```

Specifically:
- ✅ cache directory exists and is writable
- ✅ logs directory exists and is writable  
- ✅ Password hashing available (Bcrypt working)
- ✅ Database connected
- ✅ All tables present
- ✅ .env not web accessible
- ✅ API endpoints working

---

## Final Steps

### ✅ Security Hardening
1. Change admin password
2. Delete test files:
   ```bash
   rm public/test-system.php
   rm public/test-api-connection.php
   ```
3. Set `.env` to read-only:
   ```bash
   chmod 600 .env
   ```
4. Set `APP_DEBUG=false` in `.env`

### ✅ Configure ESP32 (if hardware available)
1. Update `esp32/universal_outlet_controller.ino`
2. Set WiFi credentials
3. Set server URL: `isu-documentrequest.online`
4. Upload to ESP32
5. Verify connection in dashboard

### ✅ Create First Schedule
1. Login to system
2. Go to Schedules
3. Create test schedule
4. Wait for cron to execute
5. Verify in Activity Logs

---

## Quick Commands

### Create missing directories (SSH):
```bash
mkdir -p storage/cache storage/logs
touch storage/cache/.gitkeep storage/logs/.gitkeep
```

### Fix permissions (SSH):
```bash
bash fix-permissions.sh
```

### Test cron manually (SSH):
```bash
php cron/execute_schedules.php
```

### Check permissions (SSH):
```bash
ls -la storage/
```

### View error logs (SSH):
```bash
tail -f storage/logs/error.log
```

---

## Support

If you encounter issues:
1. Run full test: `public/test-system.php`
2. Check detailed error messages
3. Review this checklist
4. See `FIX_WARNINGS_GUIDE.md` for specific fixes
5. Contact hosting support for server-specific issues

---

## ✅ Upload Complete!

When you see all green checkmarks in the test, your system is fully deployed and operational! 🎉

**Test URL:** https://isu-documentrequest.online/public/test-system.php
**App URL:** https://isu-documentrequest.online/public/
