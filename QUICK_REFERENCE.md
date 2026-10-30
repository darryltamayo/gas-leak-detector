# Quick Reference Card - Schedule Protection System

## At a Glance

### 🎯 What's New
1. ✅ Bulk schedule creation (select multiple devices)
2. ✅ Schedule protection (users can't interrupt)
3. ✅ Admin confirmation (warning before override)

### 📋 User Actions

| I want to... | As Regular User | As Admin |
|-------------|-----------------|----------|
| Create schedule for 1 device | ✅ Yes | ✅ Yes |
| Create schedule for 5 devices at once | ✅ Yes | ✅ Yes |
| Turn ON anytime | ✅ Yes | ✅ Yes |
| Turn OFF (no schedule) | ✅ Yes | ✅ Yes |
| Turn OFF (schedule active) | ❌ Blocked | ⚠️ Confirm first |

### 🔐 Protection Rules

**When does protection apply?**
- ✅ Schedule is enabled
- ✅ Current time is between ON and OFF time
- ✅ Today is in schedule days
- ✅ Trying to turn OFF (not ON)

**Who is affected?**
- Regular Users: Completely blocked
- Admins: Must confirm before override

---

## Quick Actions

### Create Bulk Schedule
```
1. Go to Schedules page
2. Click "Create Schedule"
3. Check multiple devices (or "Select All")
4. Enter schedule details
5. Submit
→ Creates schedule for each selected device
```

### Override as Admin (NEW)
```
1. Try to turn OFF device during schedule
2. Warning dialog appears
3. Review schedule details
4. Click "OK" to confirm
   OR "Cancel" to abort
5. If confirmed → Device turns OFF
```

### Check if Schedule is Active
```
Active when:
✓ Enabled
✓ Time is between ON-OFF window
✓ Today is in schedule days
✓ (One-time: Date matches)
```

---

## Status Codes

| Code | What it Means | What to Do |
|------|---------------|------------|
| 200 | ✅ Success | Action completed |
| 403 | 🔒 Blocked | Regular user during schedule |
| 409 | ⚠️ Confirm? | Admin needs to confirm |
| 503 | 📡 Offline | Device not connected |

---

## Messages You'll See

### Regular User Blocked
```
🔒 Schedule Protection Active

Cannot turn OFF while schedule is running.

Schedule: Office Hours
Created by: Admin
Ends at: 5:00 PM

Only admins can override.
```

### Admin Confirmation
```
⚠️ ADMIN OVERRIDE WARNING

Active schedule: Office Hours
Created by: John Doe
Ends at: 5:00 PM

This will:
• Turn OFF outlet immediately
• Switch to Manual mode
• Interrupt the schedule

Proceed? [OK] [Cancel]
```

### Bulk Success
```
✅ Schedule created for 3 device(s) successfully
```

### Bulk Control Result
```
✅ Bulk control completed:
   2 successful
   1 blocked by schedule (Lab AC)
```

---

## Files to Know

### Backend
- `app/controllers/ScheduleController.php` - Schedule logic
- `app/controllers/OutletController.php` - Control & protection
- `app/Router.php` - Routes

### Frontend
- `app/views/schedule/index.php` - Schedule page
- `app/views/dashboard/index.php` - Dashboard
- `app/views/outlet/index.php` - Outlet control

### Documentation
- `FINAL_IMPLEMENTATION_SUMMARY.md` - Complete overview
- `ADMIN_OVERRIDE_CONFIRMATION.md` - Admin confirmation
- `TESTING_GUIDE.md` - How to test
- `SCHEDULE_PROTECTION_FLOW.md` - Flow diagrams

---

## Common Questions

**Q: Can users turn ON during a schedule?**
A: Yes! Protection only applies to turning OFF.

**Q: Can admin bypass without confirmation?**
A: No. Admins must confirm every override.

**Q: What if schedule ends while confirming?**
A: Server checks again - if no longer active, proceeds without confirmation.

**Q: Are overrides logged?**
A: Yes. All control actions are logged in activity logs.

**Q: Can I create schedule for offline device?**
A: Yes! When device comes online, it will follow the schedule.

**Q: Does bulk control work with protection?**
A: Yes! Blocked devices are skipped, others proceed.

---

## Testing Quick Commands

### Test Regular User Block
```
1. Login as regular user
2. Create active schedule (now - 2 hours from now)
3. Set device to "Scheduled" mode
4. Try to turn OFF
→ Should be blocked
```

### Test Admin Confirmation
```
1. Login as admin
2. Create active schedule
3. Try to turn OFF
→ Warning appears
4. Click OK
→ Device turns OFF
```

### Test Bulk Schedule
```
1. Go to Schedules
2. Click "Create Schedule"
3. Select 3 devices
4. Submit
→ 3 schedules created
```

---

## Emergency Procedures

### Schedule Stuck - Need Emergency OFF
```
1. Login as admin
2. Go to device control
3. Click "Turn OFF"
4. Confirm override
5. Device turns OFF
```

### Mass Schedule Creation Error
```
1. Check browser console for errors
2. Verify CSRF token not expired
3. Try selecting fewer devices
4. Check database connection
```

### User Locked Out of Control
```
1. Verify user role in database
2. Check if schedule is actually active
3. Check device mode is "Scheduled"
4. Verify current time is within schedule
```

---

## Database Queries

### Find Active Schedules
```sql
SELECT s.*, d.device_name
FROM iot_outlet_schedules s
JOIN iot_outlet_devices d ON s.device_id = d.id
WHERE s.is_enabled = 1
  AND TIME(NOW()) >= s.time_on
  AND TIME(NOW()) < s.time_off;
```

### Check User Role
```sql
SELECT username, email, role
FROM iot_outlet_users
WHERE id = ?;
```

### View Recent Overrides
```sql
SELECT l.*, u.username, d.device_name
FROM iot_outlet_activity_logs l
JOIN iot_outlet_users u ON l.user_id = u.id
JOIN iot_outlet_devices d ON l.device_id = d.id
WHERE l.action_type = 'outlet_off'
  AND u.role = 'admin'
ORDER BY l.created_at DESC
LIMIT 10;
```

---

## Key Takeaways

✅ **For Users**
- Can create schedules faster (bulk selection)
- Cannot accidentally break schedules
- Clear feedback when blocked

✅ **For Admins**
- Full control preserved
- Must confirm before override
- Aware of what's being interrupted

✅ **For System**
- Schedules execute reliably
- Better audit trail
- Professional workflow

---

## Support Contacts

**Technical Issues:**
- Check browser console (F12)
- Review activity logs in admin panel
- Verify device online status

**Access Issues:**
- Verify user role in database
- Check session not expired
- Try logout/login

**Schedule Issues:**
- Verify schedule enabled
- Check time window correct
- Confirm days selected
- Ensure device in "Scheduled" mode

---

## Version Info

**Version:** 1.0
**Release Date:** August 2, 2026
**Features:** 3 major features
**Files Modified:** 6
**Database Changes:** 0
**Status:** Production Ready ✅

---

**Remember:**
- 🔒 Regular users protected from breaking schedules
- ⚠️ Admins see warning before overriding
- ✅ All actions logged for accountability
- 🚀 System works reliably

