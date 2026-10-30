# SIM Card Number Display - Update Summary

## ✅ Changes Made

### File Updated: `app/views/outlet/index.php`

**What Changed**: Added SIM card number display in device card header

**Location**: Device card selectable (outlet control page)

**Visual**: SIM number now appears below the Device ID with a yellow/amber badge

---

## Display Format

### Before:
```
┌─────────────────────────┐
│     Main Outlet         │
│  ESP32_OUTLET_001       │
│                         │
│    [Status Circle]      │
└─────────────────────────┘
```

### After:
```
┌─────────────────────────┐
│     Main Outlet         │
│  ESP32_OUTLET_001       │
│  📱 09605682714         │  ← NEW: SIM card badge
│                         │
│    [Status Circle]      │
└─────────────────────────┘
```

---

## Code Added

```php
<?php if ($dev->sim_card_number): ?>
<div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold" 
     style="background-color: #FEF3C7; color: #92400E;">
    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z"/>
    </svg>
    <?= htmlspecialchars($dev->sim_card_number) ?>
</div>
<?php endif; ?>
```

---

## Features

✅ **Conditional Display**: Only shows if SIM card number exists
✅ **Styled Badge**: Amber/yellow colored badge with SIM card icon
✅ **Centered**: Aligned center under Device ID
✅ **Security**: Uses `htmlspecialchars()` for XSS protection
✅ **Responsive**: Works on all screen sizes

---

## Where SIM Number Displays

### 1. Outlet Control Page (Device Cards)
**Location**: `/outlet`  
**Display**: Yellow badge under Device ID  
**Format**: 📱 09605682714  
**Status**: ✅ **UPDATED**

### 2. Device Management Table
**Location**: `/device`  
**Display**: Table column with phone icon  
**Format**: 📱 09605682714  
**Status**: ✅ **Already there**

### 3. Device Configuration Page
**Location**: `/device/view-config/{id}`  
**Display**: In configuration details  
**Format**: SIM Card Number: 09605682714  
**Status**: ✅ **Already there**

---

## How to Test

### 1. Make sure device has SIM number:
```sql
-- Check current data
SELECT device_name, device_id, sim_card_number FROM iot_outlet_devices;

-- Update if needed
UPDATE iot_outlet_devices 
SET sim_card_number = '09605682714' 
WHERE device_id = 'ESP32_OUTLET_001';
```

### 2. View the outlet control page:
```
https://isu-documentrequest.online/ac_controller_system/outlet
```

### 3. Check device card:
- Should see device name
- Should see device ID (ESP32_OUTLET_001)
- Should see SIM number in yellow badge (09605682714) ✅

---

## Styling Details

### Badge Style:
- **Background**: `#FEF3C7` (light amber)
- **Text Color**: `#92400E` (dark amber)
- **Padding**: `px-2.5 py-1` (10px horizontal, 4px vertical)
- **Border Radius**: `rounded-lg` (8px)
- **Font**: `text-xs font-semibold` (12px, bold)

### Icon:
- **Type**: SIM card icon
- **Size**: `w-3.5 h-3.5` (14px)
- **Color**: Matches text (dark amber)

---

## Files Affected

| File | Change | Status |
|------|--------|--------|
| `app/views/outlet/index.php` | Added SIM badge | ✅ Updated |
| `app/views/admin/devices.php` | Already has SIM column | ✅ No change needed |
| `app/views/admin/device-config.php` | Already shows SIM | ✅ No change needed |

---

## Example Display

### Device with SIM:
```
┌──────────────────────────────────┐
│        Living Room AC            │
│     ESP32_OUTLET_001             │
│  [📱 09605682714]  ← Amber badge │
│                                  │
│     [Green Circle - ON]          │
│     Status: ON                   │
│     🟢 Online  | Manual          │
└──────────────────────────────────┘
```

### Device without SIM:
```
┌──────────────────────────────────┐
│        Bedroom Outlet            │
│     ESP32_OUTLET_002             │
│         (no SIM badge)           │
│                                  │
│     [Red Circle - OFF]           │
│     Status: OFF                  │
│     🔴 Offline | Manual          │
└──────────────────────────────────┘
```

---

## Database Schema

The `iot_outlet_devices` table has:
```sql
sim_card_number VARCHAR(20) NULL
```

This field:
- ✅ Stores phone number (e.g., 09605682714, +639605682714)
- ✅ Is optional (can be NULL)
- ✅ Used for SMS control identification
- ✅ Displayed in device cards when available

---

## Benefits

1. **Quick Identification**: Easily see which SIM is in which device
2. **SMS Control**: Know which number the ESP32 uses for SMS
3. **Device Management**: Track SIM assignments across devices
4. **Visual Clarity**: Badge stands out but doesn't clutter
5. **Optional Display**: Only shows when SIM is configured

---

## Next Steps

1. ✅ Upload updated `app/views/outlet/index.php` to server
2. ✅ Clear browser cache (Ctrl+F5)
3. ✅ View outlet control page
4. ✅ Verify SIM number badge displays

---

## Summary

✅ **Added SIM card number display to device cards**  
✅ **Styled with amber badge and icon**  
✅ **Conditional display (only when SIM exists)**  
✅ **Responsive and secure**  
✅ **Ready to deploy**

Upload the updated file and refresh the page! 🎯
