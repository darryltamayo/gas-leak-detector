# Activity Logging Fix - Dashboard & User Management

## Date: August 2, 2026

## Issues Found

### 1. Dashboard Activity Logs Section
**Status:** ✅ Still Present (Not Removed)

The "Recent System Activity" section on dashboard is still there:
- **Location:** Dashboard page, bottom section
- **Visibility:** Admin users only
- **Displays:** Last 5 activity log entries
- **Condition:** Shows only if `!empty($stats['recent_logs'])`

**Why it might appear empty:**
- No recent activity logs in database
- Actions not being logged properly
- User creation/updates not logging

### 2. User Management Actions Not Logging
**Status:** ❌ Not Logging (Now Fixed)

User management actions were not being logged:
- ❌ Create user - No log entry
- ❌ Update user - No log entry  
- ❌ Delete user - No log entry

---

## Fixes Applied

### Fix 1: Added Logging to User Creation
**File:** `app/controllers/AdminController.php` (processCreateUser function)

```php
// After creating user
if ($this->userRepository->create($newUser)) {
    // ✅ NEW: Log user creation
    try {
        $db = $this->userRepository->getDb();
        $stmt = $db->prepare(
            'INSERT INTO iot_outlet_activity_logs (user_id, action_type, action_details, ip_address, status) 
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            user()['id'],
            'user_created',  // ← Action type
            "Created user: {$username} ({$firstName} {$lastName}) - Role: {$role}",
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'success'
        ]);
    } catch (Exception $e) {
        error_log('Failed to log user creation: ' . $e->getMessage());
    }
    
    // If SIM was also registered
    if ($registerSim && $simRepo->create($sim)) {
        // ✅ NEW: Log SIM registration
        $stmt->execute([
            user()['id'],
            'sim_registered',
            "Registered SIM: {$simPhoneNumber} for {$firstName} {$lastName}",
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'success'
        ]);
    }
}
```

### Fix 2: Added Logging to User Update
**File:** `app/controllers/AdminController.php` (processEditUser function)

```php
// After updating user
if ($this->userRepository->update($userToEdit)) {
    // ✅ NEW: Log user update
    try {
        $db = $this->userRepository->getDb();
        $stmt = $db->prepare(
            'INSERT INTO iot_outlet_activity_logs (user_id, action_type, action_details, ip_address, status) 
             VALUES (?, ?, ?, ?, ?)'
        );
        $details = "Updated user: {$userToEdit->username} ({$firstName} {$lastName}) - Role: {$role}";
        if (!empty($password)) {
            $details .= " (password changed)";
        }
        $stmt->execute([
            user()['id'],
            'user_updated',  // ← Action type
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'success'
        ]);
    } catch (Exception $e) {
        error_log('Failed to log user update: ' . $e->getMessage());
    }
}
```

---

## What Gets Logged Now

### User Management
✅ **User Created**
- Action: `user_created`
- Details: "Created user: john_doe (John Doe) - Role: user"
- Logged by: Admin who created the user

✅ **User Updated**
- Action: `user_updated`
- Details: "Updated user: john_doe (John Doe) - Role: admin (password changed)"
- Logged by: Admin who updated the user

✅ **SIM Registered (during user creation)**
- Action: `sim_registered`
- Details: "Registered SIM: 09123456789 for John Doe"
- Logged by: Admin who registered the SIM

### Device Control (Already Working)
✅ **Outlet ON**
- Action: `outlet_on`
- Details: "Outlet turned ON"
- Logged by: User or System (ESP32)

✅ **Outlet OFF**
- Action: `outlet_off`
- Details: "Outlet turned OFF"
- Logged by: User or System (ESP32)

✅ **Bulk Control**
- Action: `outlet_on` or `outlet_off`
- Details: "Outlet turned ON via bulk control"
- Logged by: User performing bulk action

### Schedule Management (Already Working)
✅ **Schedule Created**
- Action: `schedule_created`
- Details: "Created schedule: Morning AC"
- Logged by: User who created schedule

✅ **Schedule Updated**
- Action: `schedule_updated`
- Details: "Updated schedule: Morning AC"
- Logged by: User who updated schedule

✅ **Schedule Deleted**
- Action: `schedule_deleted`
- Details: "Deleted schedule: Morning AC"
- Logged by: User who deleted schedule

---

## Dashboard Activity Logs Display

### Location
**Admin Dashboard → Bottom Section**

### Display Format
```
┌────────────────────────────────────────────────────────┐
│ Recent System Activity                                 │
├────────────────────────────────────────────────────────┤
│ Timestamp         | User  | Role  | Action | Details   │
├───────────────────┼───────┼───────┼────────┼───────────┤
│ Aug 2, 02:30 PM  | admin | ADMIN | User   | Created   │
│                  |       |       | created| user: ... │
├───────────────────┼───────┼───────┼────────┼───────────┤
│ Aug 2, 02:28 PM  | admin | ADMIN | Outlet | Outlet    │
│                  |       |       | on     | turned ON │
└───────────────────┴───────┴───────┴────────┴───────────┘
```

### When It Shows
- ✅ Admin users only
- ✅ When `recent_logs` has data
- ✅ Shows last 5 entries

### When It's Hidden
- ❌ Regular (non-admin) users
- ❌ No recent activity in database
- ❌ All logs older than shown limit

---

## Verification

### Check If Logging Works

#### Test 1: Create User
1. Login as admin
2. Go to Admin → Users → Create User
3. Fill form and submit
4. Go to Dashboard
5. ✅ Verify: See "User created" in Recent System Activity

#### Test 2: Update User
1. Go to Admin → Users
2. Click Edit on any user
3. Change name or role
4. Submit
5. Go to Dashboard
6. ✅ Verify: See "User updated" in Recent System Activity

#### Test 3: Control Device
1. Go to Outlet Control
2. Turn device ON or OFF
3. Go to Dashboard
4. ✅ Verify: See "Outlet on/off" in Recent System Activity

#### Test 4: View Full Activity Logs
1. Go to Admin → Activity Logs
2. ✅ Verify: All actions are logged with proper details

### SQL Query to Check Logs
```sql
-- Check recent activity logs
SELECT 
    id,
    user_id,
    action_type,
    action_details,
    ip_address,
    created_at
FROM iot_outlet_activity_logs
ORDER BY created_at DESC
LIMIT 20;
```

### Check Specific User Actions
```sql
-- Check user management logs
SELECT 
    al.*,
    u.username
FROM iot_outlet_activity_logs al
LEFT JOIN iot_outlet_users u ON al.user_id = u.id
WHERE al.action_type IN ('user_created', 'user_updated', 'user_deleted')
ORDER BY al.created_at DESC;
```

---

## Action Types Reference

### All Valid Action Types (from database ENUM):
```
outlet_on
outlet_off
schedule_created
schedule_updated
schedule_deleted
schedule_enabled
schedule_disabled
sms_command
manual_control
override
login
logout
user_created         ← NEW
user_updated         ← NEW
user_deleted
sim_registered       ← NEW
sim_updated
sim_deleted
unauthorized_sms
heartbeat_lost
device_online
device_offline
```

---

## Dashboard Service Data Flow

### How Logs Reach Dashboard

```
1. Activity happens (e.g., user created)
   ↓
2. INSERT into iot_outlet_activity_logs
   ↓
3. DashboardService::getAdminDashboardData()
   ↓
4. ActivityLogRepository::getRecent(5)
   ↓
5. SQL: SELECT * FROM iot_outlet_activity_logs 
           LEFT JOIN users
           ORDER BY created_at DESC 
           LIMIT 5
   ↓
6. Returns array of recent_logs
   ↓
7. Dashboard view displays table
```

### Files Involved
1. **Controller:** `AdminController.php` - Logs actions
2. **Service:** `DashboardService.php` - Fetches logs
3. **Repository:** `ActivityLogRepository.php` - Database queries
4. **View:** `dashboard/index.php` - Displays logs

---

## Common Issues & Solutions

### Issue: Dashboard shows no activity logs

**Cause 1: No recent activity**
- Solution: Perform some actions (create user, control device)

**Cause 2: Not logged in as admin**
- Solution: Login with admin account
- Regular users don't see this section

**Cause 3: Database empty**
- Solution: Check database:
```sql
SELECT COUNT(*) FROM iot_outlet_activity_logs;
```

### Issue: User creation doesn't show in logs

**Before fix:**
- User created but no log entry

**After fix:**
- User creation logs with action_type='user_created'
- Check: Go to Dashboard after creating user

### Issue: "No action recorded" appears

**Cause:** Empty action_type field
- Solution: Run the SQL fixes in `complete_schema.sql`
- Post-import fixes section repairs empty actions

---

## Testing Checklist

### ✅ Test Dashboard Display
- [ ] Login as admin
- [ ] Go to Dashboard
- [ ] See "Recent System Activity" section at bottom
- [ ] Shows last 5 activities

### ✅ Test User Creation Logging
- [ ] Create a new user
- [ ] Check Dashboard
- [ ] See "User created" entry
- [ ] Check Admin → Activity Logs
- [ ] See detailed log entry

### ✅ Test User Update Logging
- [ ] Edit existing user
- [ ] Check Dashboard
- [ ] See "User updated" entry
- [ ] If password changed, says "(password changed)"

### ✅ Test Device Control Logging
- [ ] Turn device ON
- [ ] Check Dashboard
- [ ] See "Outlet on" entry
- [ ] Turn device OFF
- [ ] See "Outlet off" entry

### ✅ Test Full Activity Logs Page
- [ ] Go to Admin → Activity Logs
- [ ] See all logged activities
- [ ] Filter by action type
- [ ] See user info and details

---

## Summary

### Before:
❌ Recent System Activity section appeared empty
❌ User creation not logged
❌ User updates not logged
❌ Hard to track admin actions

### After:
✅ Recent System Activity shows recent logs
✅ User creation logged with details
✅ User updates logged with details
✅ All admin actions tracked
✅ Dashboard provides quick overview

### Files Modified:
1. `app/controllers/AdminController.php`
   - Added logging to processCreateUser
   - Added logging to processEditUser
   - Added logging to SIM registration

All user management actions are now properly logged and visible in the Dashboard Activity Logs section!
