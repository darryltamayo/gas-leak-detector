# Activity Logs - Quick Troubleshooting Guide

## Issue: "I don't see activity logs on dashboard"

### ✅ Quick Solution Steps

#### 1. **Verify You're Logged in as Admin**
- Activity logs section on dashboard **ONLY shows for admin users**
- Regular users cannot see the activity logs section
- Check your user role in the top right corner

#### 2. **Look at the Correct Location**
- Activity logs are at the **BOTTOM of the dashboard page**
- Scroll down past all device cards
- Look for "Recent System Activity" heading

#### 3. **Check the Full Activity Logs Page**
- Go to: **Admin Menu → Activity Logs**
- URL: `https://isu-documentrequest.online/ac_controller_system/admin/activity-logs`
- This page shows ALL activity logs (50 per page, paginated)
- Dashboard only shows last 5 most recent activities

#### 4. **Refresh the Page**
- Clear your browser cache
- Press Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)
- Log out and log back in

---

## What Activity Logs Show

### Dashboard (Bottom Section)
- **Last 5 activities only**
- Shows: Timestamp, User, Role, Action, Description
- Updates in real-time when you perform actions

### Activity Logs Page (Admin Menu)
- **All activities (paginated)**
- 50 logs per page
- Full search and filter capabilities
- Export options

---

## Actions That Are Logged

### ✅ User Management
- Creating new users
- Updating user information
- Changing user status (activate/deactivate)
- Registering SIM cards with users

### ✅ Device Control
- Turning outlets ON
- Turning outlets OFF
- Bulk control operations
- Override controls

### ✅ Device Management
- Creating new devices
- Updating device information
- Deleting devices
- Regenerating API keys

### ✅ Schedule Management
- Creating schedules
- Updating schedules
- Deleting schedules
- Schedule execution by cron

### ✅ Authentication
- User login
- User logout

### ✅ System Actions
- ESP32 heartbeat updates
- SMS control status changes
- Profile changes
- SIM card management

---

## Testing Activity Logging

### Quick Test (5 minutes)

1. **Log in as admin** to dashboard

2. **Perform a test action** (choose one):
   - Create a test user
   - Control an ESP32 device (turn ON or OFF)
   - Edit a device

3. **Check immediately**:
   - Scroll to bottom of dashboard
   - Look for "Recent System Activity" section
   - Your action should appear in the list

4. **Check database** (if needed):
   ```sql
   SELECT * FROM iot_outlet_activity_logs 
   ORDER BY created_at DESC 
   LIMIT 10;
   ```

---

## Common Issues & Solutions

### Issue 1: "Section Not Visible"
**Cause**: Not logged in as admin
**Solution**: Log in with admin account

### Issue 2: "Section Empty"
**Cause**: No activity in last 5 records
**Solution**: 
- Perform a test action (control device, create user)
- Or check `/admin/activity-logs` for older logs

### Issue 3: "Old Actions Not Showing"
**Cause**: Dashboard only shows last 5
**Solution**: Go to Admin Menu → Activity Logs for full history

### Issue 4: "After Action, Nothing Changes"
**Cause**: Browser cache or page not refreshed
**Solution**: 
- Press F5 to refresh
- Clear browser cache
- Log out and log back in

---

## Where Activity Logs Are Located

### In Code
1. **Dashboard Display**: `app/views/dashboard/index.php` (lines 296-342)
2. **Logging Logic**: 
   - `app/controllers/AdminController.php`
   - `app/controllers/OutletController.php`
   - `app/controllers/DeviceController.php`
   - And all other controllers

3. **Data Service**: `app/services/DashboardService.php`
4. **Repository**: `app/repositories/ActivityLogRepository.php`

### In Database
- **Table**: `iot_outlet_activity_logs`
- **Columns**: id, user_id, device_id, action_type, action_details, ip_address, user_agent, status, created_at, updated_at

---

## Example: Dashboard Activity Logs Section

```
┌─────────────────────────────────────────────────────────┐
│ Recent System Activity                                   │
├───────────────┬──────────┬───────┬─────────┬────────────┤
│ Timestamp     │ User     │ Role  │ Action  │ Description│
├───────────────┼──────────┼───────┼─────────┼────────────┤
│ 2026-08-02    │ admin    │ ADMIN │ Outlet  │ Outlet     │
│ 10:30 AM      │          │       │ on      │ turned ON  │
├───────────────┼──────────┼───────┼─────────┼────────────┤
│ 2026-08-02    │ admin    │ ADMIN │ User    │ Created    │
│ 10:25 AM      │          │       │ created │ user: john │
├───────────────┼──────────┼───────┼─────────┼────────────┤
│ 2026-08-02    │ admin    │ ADMIN │ Device  │ Device     │
│ 10:20 AM      │          │       │ updated │ updated    │
└───────────────┴──────────┴───────┴─────────┴────────────┘
```

---

## Need More Help?

### Debug Mode (for developers)

Add this to `app/views/dashboard/index.php` around line 296:

```php
<?php 
// DEBUG: Check what data is available
echo "<!-- DEBUG INFO -->";
echo "<!-- User role: " . ($user['role'] ?? 'not set') . " -->";
echo "<!-- Recent logs count: " . count($stats['recent_logs'] ?? []) . " -->";
if (!empty($stats['recent_logs'])) {
    echo "<!-- First log: " . print_r($stats['recent_logs'][0], true) . " -->";
}
echo "<!-- END DEBUG -->";
?>
```

Then view page source (Ctrl+U) to see debug output.

---

## ✅ Verification Checklist

- [ ] I am logged in as admin
- [ ] I scrolled to bottom of dashboard
- [ ] I see "Recent System Activity" heading
- [ ] I performed a test action
- [ ] I refreshed the page
- [ ] I checked /admin/activity-logs page
- [ ] I cleared browser cache

If all checked and still no logs appear, check:
- PHP error logs
- Database connection
- Browser console for JavaScript errors

---

**Last Updated**: 2026-08-02
**Status**: All activity logging verified working
**Issue**: User likely checking wrong location or not admin role
