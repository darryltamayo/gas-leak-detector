# Device Display in Activity Logs - Enhancement Complete

## Date: 2026-08-02

## Enhancement Overview
Added device name and ID display to activity logs, so users can easily see which specific ESP32 device was controlled for each action.

---

## ✅ What Was Changed

### 1. **ActivityLogRepository** - Updated SQL Queries
**File**: `app/repositories/ActivityLogRepository.php`

#### getRecent() Method
**Before**:
```php
SELECT al.*, u.username, u.full_name, u.role 
FROM iot_outlet_activity_logs al 
LEFT JOIN iot_outlet_users u ON al.user_id = u.id
```

**After**:
```php
SELECT al.*, u.username, u.full_name, u.role,
       d.device_name, d.device_id as device_identifier
FROM iot_outlet_activity_logs al 
LEFT JOIN iot_outlet_users u ON al.user_id = u.id 
LEFT JOIN iot_outlet_devices d ON al.device_id = d.id
```

#### getRecentByUser() Method
Same enhancement - now includes device information.

---

### 2. **Dashboard Activity Logs** - Added Device Column
**File**: `app/views/dashboard/index.php`

#### Table Header (line ~306-312)
**Added new column**:
```html
<th>Device</th>
```

Between "User" and "Action" columns.

#### Table Body (line ~315-333)
**Before**:
```php
<td><?= e($log['username'] ?? 'System') ?></td>
<td><?= e($log['role'] ?? 'ADMIN') ?></td>  ← Role column
<td><?= ucfirst(str_replace('_', ' ', $log['action_type'])) ?></td>
```

**After**:
```php
<td>
    <?= e($log['username'] ?? 'System') ?>
    <div class="text-xs"><?= e($log['role'] ?? 'ADMIN') ?></div>  ← Role moved under username
</td>
<td>  ← NEW DEVICE COLUMN
    <?php if (!empty($log['device_name'])): ?>
        <div class="font-medium" style="color: #2D7A5B;"><?= e($log['device_name']) ?></div>
        <div class="text-xs" style="color: #86868B;"><?= e($log['device_identifier']) ?></div>
    <?php else: ?>
        <span style="color: #86868B;">-</span>
    <?php endif; ?>
</td>
<td><?= ucfirst(str_replace('_', ' ', $log['action_type'])) ?></td>
```

---

### 3. **Full Activity Logs Page** - Added Device Column
**File**: `app/views/admin/activity-logs.php`

#### Table Header (line ~105-112)
**Added new column**:
```html
<th>Device</th>
```

Between "User" and "Action" columns.

#### Table Body (line ~120-135)
**Added device cell**:
```php
<td class="px-4 py-4 whitespace-nowrap">
    <div class="flex items-center gap-3">
        <!-- User avatar and name with role underneath -->
    </div>
</td>
<td class="px-4 py-4 whitespace-nowrap">  ← NEW DEVICE COLUMN
    <?php if (!empty($log['device_name'])): ?>
        <div>
            <p class="text-sm font-semibold" style="color: #2D7A5B;">
                <?= e($log['device_name']) ?>
            </p>
            <p class="text-xs" style="color: #86868B;">
                <?= e($log['device_identifier']) ?>
            </p>
        </div>
    <?php else: ?>
        <span class="text-sm" style="color: #86868B;">-</span>
    <?php endif; ?>
</td>
```

---

## 📊 How It Looks Now

### Dashboard - Recent System Activity

**Before**:
```
┌──────────────┬────────┬───────┬───────────┬──────────────────────────────┐
│ Timestamp    │ User   │ Role  │ Action    │ Description                   │
├──────────────┼────────┼───────┼───────────┼──────────────────────────────┤
│ 2026-08-02   │ john   │ USER  │ Outlet on │ Outlet turned ON via manual   │
│ 10:45 AM     │        │       │           │ control                       │
└──────────────┴────────┴───────┴───────────┴──────────────────────────────┘
```

**After**:
```
┌──────────────┬────────────┬─────────────────┬───────────┬──────────────────────────────┐
│ Timestamp    │ User       │ Device          │ Action    │ Description                   │
│              │ (Role)     │                 │           │                               │
├──────────────┼────────────┼─────────────────┼───────────┼──────────────────────────────┤
│ 2026-08-02   │ john       │ Living Room AC  │ Outlet on │ Outlet turned ON via manual   │
│ 10:45 AM     │ USER       │ ESP32_OUTLET_001│           │ control                       │
├──────────────┼────────────┼─────────────────┼───────────┼──────────────────────────────┤
│ 2026-08-02   │ admin      │ Bedroom AC      │ Outlet    │ Outlet turned OFF via bulk    │
│ 10:40 AM     │ ADMIN      │ ESP32_OUTLET_002│ off       │ control                       │
├──────────────┼────────────┼─────────────────┼───────────┼──────────────────────────────┤
│ 2026-08-02   │ admin      │ -               │ User      │ Created user: john (John Doe) │
│ 10:30 AM     │ ADMIN      │                 │ created   │ - Role: user                  │
└──────────────┴────────────┴─────────────────┴───────────┴──────────────────────────────┘
```

### Full Activity Logs Page

**New Device Column Shows**:
- **Device Name** (green text): "Living Room AC"
- **Device ID** (gray text): "ESP32_OUTLET_001"
- **No Device** (gray dash): "-" for non-device actions like user creation, login, etc.

---

## 🎯 Benefits

### 1. **Easy Device Identification**
Users can immediately see which ESP32 device was controlled without guessing or checking descriptions.

### 2. **Audit Trail**
Clear tracking of which specific device was affected by each action:
- "Living Room AC (ESP32_OUTLET_001) turned ON by john at 10:45 AM"
- "Bedroom AC (ESP32_OUTLET_002) turned OFF by admin at 10:40 AM"

### 3. **Multi-Device Support**
Essential for systems with multiple ESP32 devices - users can:
- Track activity per device
- See device-specific control history
- Identify which device was affected by bulk operations

### 4. **Visual Clarity**
- Device info displayed in green (stands out)
- Device ID in smaller gray text (secondary info)
- Empty cells show "-" for clarity

---

## 📝 Data Displayed

### For Device Control Actions
| Field | Example | Color | Description |
|-------|---------|-------|-------------|
| Device Name | Living Room AC | Green (#2D7A5B) | Friendly device name |
| Device ID | ESP32_OUTLET_001 | Gray (#86868B) | Unique device identifier |

### For Non-Device Actions
| Field | Display | Description |
|-------|---------|-------------|
| Device | - | Gray dash indicates no device involved |

**Non-Device Actions Include**:
- User creation/update/deletion
- SIM registration/update
- User login/logout
- Schedule creation/update/deletion
- Password changes
- Profile updates

---

## 🔍 Which Actions Show Device?

### ✅ Actions That Display Device
- `outlet_on` - Manual ON control
- `outlet_off` - Manual OFF control
- `outlet_on` - Bulk control ON
- `outlet_off` - Bulk control OFF
- `mode_changed` - Control mode changes
- Any device-specific action where `device_id` is logged

### ❌ Actions Without Device Display
- `user_created` - User management
- `user_updated` - User management
- `sim_registered` - SIM management
- `login` - Authentication
- `logout` - Authentication
- `schedule_created` - Schedule management
- `password_changed` - Profile management

---

## 💾 Database Structure

### iot_outlet_activity_logs Table
```sql
CREATE TABLE iot_outlet_activity_logs (
    id INT PRIMARY KEY,
    user_id INT,              -- Who performed the action
    device_id INT,            -- Which device was affected (can be NULL)
    action_type VARCHAR(50),  -- Type of action
    action_details TEXT,      -- Description
    ip_address VARCHAR(45),
    user_agent TEXT,
    status VARCHAR(20),
    created_at TIMESTAMP
);
```

### Devices Table Join
```sql
LEFT JOIN iot_outlet_devices d ON al.device_id = d.id
```

**Returns**:
- `d.device_name` - "Living Room AC"
- `d.device_id` - "ESP32_OUTLET_001"

---

## 🧪 Testing

### Test Case 1: Control Single Device
1. Go to dashboard or outlets page
2. Turn ON "Living Room AC"
3. Scroll to "Recent System Activity"
4. **Verify**: Device column shows "Living Room AC" and "ESP32_OUTLET_001"

### Test Case 2: Bulk Control Multiple Devices
1. Select 3 devices in dashboard
2. Click "Turn OFF"
3. Go to Admin → Activity Logs
4. **Verify**: Each log entry shows the specific device that was controlled

### Test Case 3: Non-Device Action
1. Create a new user
2. Check activity logs
3. **Verify**: Device column shows "-" for user creation log

### Test Case 4: View Full History
1. Go to Admin → Activity Logs
2. Look at past device control actions
3. **Verify**: All device controls show device name/ID

---

## 📋 Summary of Changes

| File | Lines Changed | Change Description |
|------|---------------|-------------------|
| `ActivityLogRepository.php` | ~27-35, ~48-57 | Added device table JOIN to SQL queries |
| `dashboard/index.php` | ~306-333 | Added device column to Recent Activity table |
| `admin/activity-logs.php` | ~107-135 | Added device column to full Activity Logs table |

**Total Files Modified**: 3
**Total Lines Added**: ~40
**Functionality**: Enhanced user experience with device visibility

---

## ✅ Completion Status

- ✅ Database queries updated to fetch device info
- ✅ Dashboard activity logs display device column
- ✅ Full activity logs page display device column
- ✅ Handles NULL device_id gracefully (shows "-")
- ✅ Device name in green for visibility
- ✅ Device ID in gray as secondary info
- ✅ User role moved under username to save space
- ✅ Responsive design maintained

---

## 🎉 Result

Users can now **clearly see which ESP32 device** was controlled for every device-related action in both:
1. Dashboard "Recent System Activity" section
2. Full Activity Logs page (/admin/activity-logs)

This provides complete audit trail visibility for multi-device IoT systems!

---

**Enhancement Status**: ✅ COMPLETE
**Date Completed**: 2026-08-02
**Tested**: Ready for use
**User Impact**: Significantly improved activity log clarity and device tracking
