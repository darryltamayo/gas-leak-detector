# Single SQL Import File - Complete Schema

## Date: August 1, 2026

## ✅ Consolidated Database Schema

All SQL changes are now in **ONE file only**:
- `database/complete_schema.sql`

---

## What's Included

### 1. All Tables
- ✅ `iot_outlet_users`
- ✅ `iot_outlet_devices` (with `sim_card_number` field)
- ✅ `iot_outlet_authorized_sims`
- ✅ `iot_outlet_schedules` (with `repeat_type` and `last_executed`)
- ✅ `iot_outlet_activity_logs`
- ✅ `iot_outlet_sessions`
- ✅ `iot_outlet_password_resets`

### 2. All Views
- ✅ `iot_outlet_active_schedules_view`
- ✅ `iot_outlet_recent_activity_view`
- ✅ `iot_outlet_device_status_view`

### 3. All Stored Procedures
- ✅ `sp_iot_outlet_cleanup_old_logs`
- ✅ `sp_iot_outlet_get_user_stats`
- ✅ `sp_iot_outlet_update_heartbeat`

### 4. All Triggers
- ✅ `trg_iot_outlet_user_login_log`
- ✅ `trg_iot_outlet_status_change`
- ✅ `trg_iot_outlet_device_offline_check`

### 5. Sample Data
- ✅ Admin user (username: `admin`, password: `password`)
- ✅ Default device (ESP32_OUTLET_001)
- ✅ Authorized SIM (09554778665)

### 6. ✨ NEW: Post-Import Fixes
- ✅ Fixes for empty `action_type` in activity logs
- ✅ Automatically repairs old/invalid log entries
- ✅ All UPDATE queries included at the end

---

## How to Import

### Option 1: phpMyAdmin (Recommended)
1. Open phpMyAdmin
2. Select database: `u907280979_docrequest`
3. Click "SQL" tab
4. Click "Choose File" or drag-drop
5. Select: `database/complete_schema.sql`
6. Click "Go"
7. Wait for completion message

### Option 2: MySQL Command Line
```bash
mysql -u u907280979_docrequest -p u907280979_docrequest < database/complete_schema.sql
```

### Option 3: cPanel MySQL
1. Login to cPanel
2. Go to phpMyAdmin
3. Select database
4. Import → Choose File
5. Select `complete_schema.sql`
6. Execute

---

## What Happens During Import

### Step 1: Drop Existing Tables (Clean Slate)
```sql
DROP TABLE IF EXISTS iot_outlet_activity_logs;
DROP TABLE IF EXISTS iot_outlet_schedules;
-- ... etc
```
**Warning:** This deletes ALL existing data!

### Step 2: Create All Tables
Creates all 7 tables with proper structure, indexes, and foreign keys.

### Step 3: Create Views
Creates 3 views for common queries.

### Step 4: Create Stored Procedures
Creates 3 stored procedures for maintenance.

### Step 5: Create Triggers
Creates 3 triggers for automatic logging.

### Step 6: Insert Sample Data
- Admin user
- Default device
- Authorized SIM

### Step 7: **NEW** - Fix Empty Activity Logs
Automatically repairs any existing activity logs with empty `action_type`:
- ✅ Fixes outlet ON/OFF logs
- ✅ Fixes schedule logs
- ✅ Fixes login/logout logs
- ✅ Identifies logs by their `action_details` content

---

## Post-Import Fixes Section

At the end of the SQL file, there's an optional section:

```sql
-- ============================================
-- POST-IMPORT FIXES (Optional - Run if needed)
-- ============================================
```

### What it does:
1. **Scans activity logs** for empty or invalid `action_type`
2. **Infers action type** from `action_details` content
3. **Updates records** with correct action type

### Examples:
```sql
-- If action_details contains "turned ON"
UPDATE ... SET action_type = 'outlet_on'

-- If action_details contains "Created schedule"
UPDATE ... SET action_type = 'schedule_created'

-- If action_details contains "logged in"
UPDATE ... SET action_type = 'login'
```

### When to Use:
- ✅ After importing to fix any old data
- ✅ If you see "No action recorded" in Activity Logs page
- ✅ To clean up existing invalid entries

---

## Verification Queries

After import, you can verify everything is correct:

### 1. Check Tables
```sql
SHOW TABLES LIKE 'iot_outlet_%';
```
Should show 7 tables.

### 2. Check Sample Data
```sql
SELECT * FROM iot_outlet_users;
SELECT * FROM iot_outlet_devices;
SELECT * FROM iot_outlet_authorized_sims;
```

### 3. Check for Empty Actions
```sql
SELECT COUNT(*) as invalid_count
FROM iot_outlet_activity_logs
WHERE action_type NOT IN (
    'outlet_on', 'outlet_off', 'schedule_created', 'schedule_updated', 
    'schedule_deleted', 'schedule_enabled', 'schedule_disabled', 
    'sms_command', 'manual_control', 'override', 'login', 'logout',
    'user_created', 'user_updated', 'user_deleted', 'sim_registered',
    'sim_updated', 'sim_deleted', 'unauthorized_sms', 'heartbeat_lost',
    'device_online', 'device_offline'
);
```
Should return 0 after running post-import fixes.

### 4. Check Action Distribution
```sql
SELECT action_type, COUNT(*) as count
FROM iot_outlet_activity_logs
GROUP BY action_type
ORDER BY count DESC;
```

---

## Files Removed

These separate SQL files are **NO LONGER NEEDED** and have been deleted:
- ❌ `database/fix_empty_activity_logs.sql` (now included in main file)
- ❌ Any other migration files (all consolidated)

---

## Backup Before Import

**IMPORTANT:** Always backup your database before importing!

### Via phpMyAdmin:
1. Select database
2. Click "Export"
3. Click "Go"
4. Save the .sql file

### Via Command Line:
```bash
mysqldump -u username -p u907280979_docrequest > backup_$(date +%Y%m%d).sql
```

---

## Summary

### Before (Multiple Files):
```
database/
├── complete_schema.sql
├── fix_empty_activity_logs.sql
├── migration_001.sql
├── migration_002.sql
└── ...
```

### After (Single File):
```
database/
└── complete_schema.sql  ← Everything in ONE file!
```

---

## Benefits

✅ **One-time import** - No need to run multiple scripts
✅ **All-in-one** - Schema + fixes + samples in single file
✅ **Clean slate** - Drops old tables, creates fresh ones
✅ **Auto-repair** - Fixes empty activity logs automatically
✅ **Easy deployment** - Just import one file on new server
✅ **Version controlled** - Single source of truth

---

## Next Steps After Import

1. ✅ Verify tables created successfully
2. ✅ Login with admin account (admin/password)
3. ✅ Check Activity Logs page - should show no empty actions
4. ✅ Configure ESP32 with API key: `IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T`
5. ✅ Add additional users, devices, and schedules as needed

---

## For Fresh Installation:

**Just run this ONE command:**
```bash
mysql -u username -p database_name < database/complete_schema.sql
```

That's it! Everything is set up and ready to use.
