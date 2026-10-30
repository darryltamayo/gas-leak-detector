# Testing Guide: Bulk Scheduling & Schedule Protection

## Quick Test Instructions

### Test 1: Bulk Schedule Creation
1. Go to **Schedules** page
2. Click **"Create Schedule"** button
3. ✅ Verify device list shows checkboxes (not dropdown)
4. ✅ Check "Select All Devices" → All devices selected
5. ✅ Uncheck one device → "Select All" shows indeterminate state
6. Select 2-3 devices
7. Fill in schedule details:
   - Name: "Test Bulk Schedule"
   - Turn ON: 08:00
   - Turn OFF: 17:00
   - Days: Select Monday, Tuesday, Wednesday
8. Click **"Create"**
9. ✅ Verify success message shows count (e.g., "Schedule created for 3 device(s)")
10. ✅ Check Schedules page → 3 separate schedules created (one per device)

### Test 2: Schedule Protection (Regular User)

**Setup:**
1. Login as **regular user** (not admin)
2. Create a schedule:
   - Time: Current time to 2 hours from now (e.g., 10:00 AM - 12:00 PM)
   - Days: Include today
   - Enable immediately
3. Go to **Outlet Control** page
4. Set device to **"Scheduled"** mode
5. Wait for schedule to turn ON the device (or manually turn it ON)

**Test Protection:**
1. Try to turn OFF the device
2. ✅ Should be **BLOCKED**
3. ✅ Message should show:
   - "🔒 Schedule Protection Active"
   - Schedule name
   - Creator name
   - End time
   - "Only admins can override"

**Test Outside Schedule:**
1. Wait for schedule to pass end time OR edit schedule to past time
2. Try to turn OFF the device
3. ✅ Should be **ALLOWED**

### Test 3: Schedule Protection (Admin Override)

1. Login as **admin**
2. With active schedule running (device ON, within schedule time)
3. Try to turn OFF the device
4. ✅ Should be **ALLOWED** (admin bypass)
5. ✅ Device turns OFF successfully

### Test 4: Bulk Control with Schedule Protection

**Setup:**
1. Have 3 devices:
   - Device 1: No active schedule
   - Device 2: Active schedule (10:00 AM - 5:00 PM)
   - Device 3: No active schedule
2. All devices currently ON
3. Login as **regular user**

**Test:**
1. Go to **Outlet Control** page
2. Select all 3 devices (click cards to select)
3. Click **"Turn OFF"** in bulk actions bar
4. ✅ Verify result message:
   - "2 successful, 1 blocked by active schedule (Device 2 name)"
5. ✅ Device 1: Turned OFF
6. ✅ Device 2: Still ON (blocked)
7. ✅ Device 3: Turned OFF

### Test 5: Dashboard Device Control

1. Go to **Dashboard** page
2. Find device card with active schedule
3. Device should be ON
4. Click **"Turn OFF"** button on device card
5. ✅ Should show schedule protection message
6. ✅ Should NOT reload page
7. Find device with no active schedule
8. Click **"Turn OFF"** button
9. ✅ Should work normally

---

## Expected Behaviors

### JavaScript Console (No Errors)
Open browser DevTools (F12) → Console tab should show:
- No JavaScript errors
- Successful fetch requests
- JSON responses with expected data

### Form Validation
- Empty device selection → "Please select at least one device"
- Turn ON time >= Turn OFF time → Error message
- No days selected (repeat schedule) → Error message
- No date selected (one-time schedule) → Error message

### Activity Logs
Check **System Activity** (Admin only):
- Schedule creation logs show device names
- Outlet control logs show which device
- Schedule protection blocks are NOT logged (no action taken)

---

## Common Issues & Solutions

### Issue 1: "Select All" not working
**Solution:** Clear browser cache, refresh page (Ctrl+F5)

### Issue 2: Schedule protection not blocking
**Check:**
- Is schedule enabled?
- Is device in "Scheduled" mode?
- Is current time within schedule time?
- Is today in schedule days?
- Is user NOT admin? (admins bypass)

### Issue 3: Can't turn OFF even without schedule
**Check:**
- Device online status
- Network connection
- Browser console for errors
- Try logout/login

### Issue 4: Bulk control not working
**Check:**
- Devices selected (click cards to select)
- Devices online
- CSRF token valid (refresh if page been open long time)

---

## Admin vs Regular User Differences

| Action | Regular User | Admin |
|--------|-------------|-------|
| Create bulk schedules | ✅ Yes | ✅ Yes |
| Turn OFF during schedule | ❌ Blocked | ✅ Allowed (override) |
| Turn ON anytime | ✅ Yes | ✅ Yes |
| Delete schedules | Only own | ✅ All schedules |
| Edit schedules | Only own | ✅ All schedules |

---

## Browser Testing

Test on multiple browsers:
- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari (if available)

All features should work consistently across browsers.

---

## Mobile Testing

Test on mobile devices:
- [ ] Device selection (checkboxes tappable)
- [ ] Schedule form scrollable
- [ ] Protection messages readable
- [ ] Bulk actions bar usable

---

## Performance Testing

With 10+ devices:
- [ ] Schedule creation completes within 5 seconds
- [ ] Bulk control responds within 3 seconds
- [ ] No browser lag when selecting devices
- [ ] Page reload after control is smooth

---

## Security Testing

1. **Try to bypass protection:**
   - Modify POST request in DevTools
   - Try to send direct API call
   - ✅ Should still be blocked server-side

2. **Session expiry:**
   - Let session expire
   - Try to control device
   - ✅ Should redirect to login

3. **Invalid device IDs:**
   - Try to control non-existent device
   - ✅ Should return 404 error

---

## Success Criteria

All features working when:
- [x] Bulk schedule creation works for 1-10 devices
- [x] Regular users blocked during active schedules
- [x] Admins can always override schedules
- [x] Bulk control correctly handles blocked devices
- [x] Dashboard control respects schedule protection
- [x] Outlet page control respects schedule protection
- [x] No JavaScript errors in console
- [x] Activity logs show device names
- [x] Messages are clear and user-friendly
- [x] Mobile responsive and usable

---

## Need Help?

**Common Test User Accounts:**
- Admin: username from database with role='admin'
- Regular: username from database with role='user'

**Quick Device Check:**
```sql
SELECT id, device_name, device_id, outlet_status, control_mode, is_online 
FROM iot_outlet_devices;
```

**Quick Schedule Check:**
```sql
SELECT s.id, s.schedule_name, s.device_id, d.device_name, 
       s.time_on, s.time_off, s.is_enabled, s.days_of_week
FROM iot_outlet_schedules s
JOIN iot_outlet_devices d ON s.device_id = d.id
WHERE s.is_enabled = 1;
```

