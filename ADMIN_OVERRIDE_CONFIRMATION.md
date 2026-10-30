# Admin Override Confirmation Feature

## Overview
Added confirmation warning for admins before they override active schedules. This ensures even administrators are fully aware they're interrupting a scheduled operation.

---

## Feature Details

### What Changed
Previously:
- ❌ Admins could override schedules without any warning
- ❌ No confirmation required
- ❌ Easy to accidentally interrupt schedules

Now:
- ✅ Admins see detailed warning before override
- ✅ Must explicitly confirm the action
- ✅ Shows schedule details and consequences
- ✅ Prevents accidental interruptions

---

## User Experience

### Regular User Flow (Unchanged)
```
Regular User → Try to turn OFF during schedule
↓
🔒 BLOCKED IMMEDIATELY
↓
Shows: "Only admins can override"
```

### Admin User Flow (NEW)
```
Admin → Try to turn OFF during schedule
↓
⚠️ WARNING DIALOG SHOWS
↓
Admin sees:
- Schedule name
- Who created it
- When it ends
- What will happen if overridden
↓
Admin must click "OK" to confirm
OR
Admin clicks "Cancel" to abort
↓
If confirmed:
  ✅ Override proceeds
  Device turns OFF
  Mode switches to Manual
  Activity logged
```

---

## Warning Dialog Content

### Admin Override Warning Message
```
⚠️ ADMIN OVERRIDE WARNING

An active schedule is currently running and will be interrupted.

Schedule: Office Hours Schedule
Created by: John Doe
Scheduled end time: 5:00 PM

Are you sure you want to override this schedule?

This action will:
• Turn OFF the outlet immediately
• Switch mode to Manual
• Interrupt the scheduled operation
```

### User Choices
- **OK** - Confirm override → Schedule interrupted, device controlled
- **Cancel** - Abort action → Device stays in scheduled mode

---

## API Flow

### Step 1: Initial Request (Without Confirmation)
```http
POST /outlet/control-device
Body: {
  device_id: 1,
  action: "off",
  csrf_token: "xxx"
}

Response (409 Conflict):
{
  "success": false,
  "requires_confirmation": true,
  "is_admin_override": true,
  "message": "⚠️ Admin Override Required...",
  "schedule_info": {
    "name": "Office Hours Schedule",
    "created_by": "John Doe",
    "time_off": "5:00 PM"
  }
}
```

### Step 2: Request With Confirmation
```http
POST /outlet/control-device
Body: {
  device_id: 1,
  action: "off",
  confirm_override: "1",  ← CONFIRMATION FLAG
  csrf_token: "xxx"
}

Response (200 Success):
{
  "success": true,
  "message": "✅ Device turned OFF successfully",
  "status": "off",
  "mode": "manual"
}
```

---

## Implementation Details

### Backend Changes

#### OutletController.php
Three methods updated with admin confirmation logic:

**1. turnOff() Method**
```php
if ($isAdmin) {
    $requireConfirmation = $_POST['confirm_override'] ?? false;
    
    if (!$requireConfirmation) {
        // First attempt - require confirmation
        jsonResponse([
            'success' => false,
            'requires_confirmation' => true,
            'schedule_info' => [...]
        ], 409);
        return;
    }
    // If confirmation provided, proceed
}
```

**2. toggle() Method**
- Same confirmation logic as turnOff()
- Only triggered when toggling from ON to OFF

**3. controlDevice() Method**
- Same confirmation logic
- Used by dashboard and outlet pages

### Frontend Changes

#### Dashboard View (dashboard/index.php)
**New Functions:**
1. `controlDeviceOutlet()` - Updated to handle confirmation response
2. `controlDeviceOutletWithConfirm()` - NEW function for confirmed retry

**Logic:**
```javascript
if (data.requires_confirmation && data.is_admin_override) {
    // Show confirmation dialog
    if (confirm(warningMessage)) {
        // Call with confirmation flag
        controlDeviceOutletWithConfirm(deviceId, action, isOnline);
    }
}
```

#### Outlet View (outlet/index.php)
**New Functions:**
1. `controlSingleDevice()` - Updated to handle confirmation response
2. `controlSingleDeviceWithConfirm()` - NEW function for confirmed retry

Same logic as dashboard view.

---

## Response Codes

| Code | Meaning | Action |
|------|---------|--------|
| 200 | Success | Action completed |
| 403 | Forbidden | Regular user blocked by schedule |
| 409 | Conflict | Admin needs to confirm override |
| 500 | Error | Server error |
| 503 | Service Unavailable | Device offline |

**409 Conflict** is specifically used for admin confirmation requests.

---

## Activity Logging

### When Admin Overrides
Activity log entry includes:
```
Action: outlet_off
Details: "Device 'ESP32_OUTLET_001' turned OFF via manual control"
User: Admin username
Device: Specific device ID
```

**Note:** Override is logged as normal control action, not special "override" type. This maintains consistency with other manual controls.

---

## Security Considerations

### Protection Remains Strong
✅ Regular users still completely blocked
✅ Confirmation is server-side enforced
✅ Cannot bypass with modified POST requests
✅ CSRF protection still active

### Admin Accountability
✅ Confirmation creates deliberate action
✅ All overrides are logged
✅ Schedule info shown before override
✅ Reduces accidental interruptions

---

## Benefits

### For Admins
✅ **Awareness** - Clearly see what schedule will be interrupted
✅ **Accountability** - Deliberate confirmation required
✅ **Information** - See who created schedule and when it ends
✅ **Flexibility** - Can still override for emergencies

### For Organizations
✅ **Audit Trail** - All overrides logged in activity logs
✅ **Reduced Errors** - Prevents accidental interruptions
✅ **Better Governance** - Admins more careful with overrides
✅ **Compliance** - Shows proper controls in place

### For System
✅ **Data Integrity** - Fewer unintended schedule interruptions
✅ **Consistent Behavior** - Predictable schedule execution
✅ **User Confidence** - Schedules less likely to be broken
✅ **Better Documentation** - Clear record of overrides

---

## Testing Scenarios

### Test 1: Admin First Attempt
1. Login as admin
2. Schedule active, device ON
3. Try to turn OFF
4. ✅ Warning dialog appears
5. ✅ Shows schedule details
6. Click Cancel
7. ✅ Device stays ON
8. ✅ No activity logged

### Test 2: Admin Confirms Override
1. Login as admin
2. Schedule active, device ON
3. Try to turn OFF
4. ✅ Warning dialog appears
5. Click OK
6. ✅ Device turns OFF
7. ✅ Mode switches to Manual
8. ✅ Activity logged
9. ✅ Success message shown

### Test 3: Regular User (Unchanged)
1. Login as regular user
2. Schedule active, device ON
3. Try to turn OFF
4. ✅ Immediately blocked
5. ✅ No confirmation option
6. ✅ Clear error message

### Test 4: Network Issues
1. Admin confirms override
2. Network error occurs
3. ✅ Error message shown
4. ✅ Button re-enabled
5. ✅ Can retry

---

## Edge Cases Handled

### Multiple Rapid Clicks
✅ Button disabled during processing
✅ Only one request sent at a time
✅ Re-enabled on error or success

### Browser Back Button
✅ CSRF token validated
✅ Stale requests rejected
✅ Page state refreshed on success

### Session Expiry
✅ Redirects to login if session expired
✅ No partial state updates
✅ User must re-authenticate

### Schedule Ends During Confirmation
✅ Server checks schedule status again
✅ If no longer active, proceeds normally
✅ No confirmation needed

---

## Files Modified

1. ✅ `app/controllers/OutletController.php`
   - Updated turnOff() method
   - Updated toggle() method
   - Updated controlDevice() method

2. ✅ `app/views/dashboard/index.php`
   - Updated controlDeviceOutlet() function
   - Added controlDeviceOutletWithConfirm() function

3. ✅ `app/views/outlet/index.php`
   - Updated controlSingleDevice() function
   - Added controlSingleDeviceWithConfirm() function

---

## Comparison: Before vs After

### Before This Update
| Scenario | Admin Action | Result |
|----------|--------------|--------|
| Active schedule | Turn OFF | ✅ Immediate override |
| Active schedule | Turn ON | ✅ Immediate control |
| No schedule | Turn OFF | ✅ Immediate control |

**Issue:** Admins could accidentally interrupt schedules

### After This Update
| Scenario | Admin Action | Result |
|----------|--------------|--------|
| Active schedule | Turn OFF | ⚠️ **WARNING → Confirm → Override** |
| Active schedule | Turn ON | ✅ Immediate control (no warning) |
| No schedule | Turn OFF | ✅ Immediate control (no warning) |

**Benefit:** Admins must confirm before interrupting schedules

---

## Error Handling

### Invalid Confirmation Flag
- Server validates confirmation flag
- If invalid format, treats as not confirmed
- Returns 409 status with confirmation request

### Schedule Becomes Inactive
- Server re-checks schedule status
- If no longer active, proceeds without confirmation
- Normal control flow resumes

### Device Goes Offline
- Check happens before schedule check
- Returns 503 if device offline
- No confirmation dialog shown

---

## Future Enhancements (Optional)

Possible improvements:
- 📊 Dashboard showing override statistics
- 📧 Email notification when admin overrides
- 📝 Require override reason (text field)
- ⏱️ Rate limiting for overrides
- 👥 Multi-admin approval for critical schedules

---

## Summary

✅ **Admin Confirmation Added** - Admins now see warning before overriding active schedules

✅ **Two-Step Process** - First attempt shows warning, second attempt with confirmation proceeds

✅ **Detailed Information** - Shows schedule name, creator, end time, and consequences

✅ **Backward Compatible** - Regular users still blocked, admin power preserved

✅ **Security Maintained** - Server-side enforcement, cannot be bypassed

✅ **Better UX** - Prevents accidental overrides, maintains emergency access

---

**Date Implemented:** August 2, 2026
**Files Modified:** 3
**Database Changes:** 0
**Breaking Changes:** None

This feature completes the schedule protection system by ensuring ALL users (including admins) are aware when they're interrupting scheduled operations.

