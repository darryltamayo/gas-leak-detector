# Storage Directory

This directory contains application data that needs to be writable by the web server.

## Directory Structure

```
storage/
├── cache/          # Application cache files
├── logs/           # Application and error logs
└── README.md       # This file
```

## Required Permissions

### On Linux/Unix hosting:
```bash
chmod 755 storage/
chmod 775 storage/cache/
chmod 775 storage/logs/
```

### On cPanel:
1. Go to File Manager
2. Right-click on `storage/cache` folder
3. Select "Change Permissions"
4. Set to: 755 or 775 (must be writable)
5. Repeat for `storage/logs` folder

## What Each Directory Does

### `/cache`
- Stores temporary cache files
- Improves application performance
- Can be safely deleted (will be regenerated)
- **Must be writable by web server**

### `/logs`
- Stores application logs
- Stores error logs
- Helps with debugging
- **Must be writable by web server**

## Troubleshooting

### "Permission denied" errors:
```bash
# Fix permissions (Linux/SSH)
chmod -R 755 storage/
chmod -R 775 storage/cache/ storage/logs/
```

### Empty directories not uploading:
- Each directory has a `.gitkeep` file
- This ensures directories exist in version control
- Upload these files to your server

### Cache directory missing:
If you see "cache directory missing" error:
1. Create the directory: `storage/cache/`
2. Set permissions to 755 or 775
3. Add `.gitkeep` file (or any file to preserve it)

## Security Note

⚠️ **Important:** These directories should NOT be directly accessible from the web!

The `.htaccess` file in the root directory already denies access to:
- `/storage` directory
- `/config` directory  
- `/app` directory

Verify by trying to access:
```
https://your-domain.com/storage/
```

Should return: **403 Forbidden** or **404 Not Found**

## Maintenance

### Clear Cache:
```bash
# Via SSH
rm -rf storage/cache/*
# Or via File Manager: Delete contents of cache folder
```

### Clear Old Logs (older than 30 days):
```bash
# Via SSH
find storage/logs/ -name "*.log" -mtime +30 -delete
```

### Check Log Size:
```bash
# Via SSH
du -sh storage/logs/
```

If logs are too large (>100MB), consider:
1. Rotating logs (move old logs to archive)
2. Reducing log level in `.env` (set `LOG_LEVEL=error`)
3. Setting up automatic log rotation

## For Developers

When adding new features that need file storage:
1. Create subdirectories under `storage/`
2. Add `.gitkeep` files to preserve empty directories
3. Update this README with new directory purpose
4. Update `.htaccess` if needed to deny web access
5. Document required permissions

## After Upload to Server

Check these directories exist and are writable:
- [ ] `storage/cache/` exists
- [ ] `storage/cache/` is writable (755 or 775)
- [ ] `storage/logs/` exists
- [ ] `storage/logs/` is writable (755 or 775)
- [ ] Both directories are NOT accessible from web
- [ ] Test system shows no warnings about these directories

Run the system test to verify:
```
https://your-domain.com/public/test-system.php
```

Should show:
- ✅ cache directory: Directory exists and is writable
- ✅ logs directory: Directory exists and is writable
