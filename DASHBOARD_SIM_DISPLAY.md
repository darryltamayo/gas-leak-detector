# Dashboard SIM Card Number Display

## Date: August 1, 2026

## Update Applied

Added SIM card number display to device cards on the Dashboard page, matching the design from the Outlet Control page.

---

## File Modified

**File:** `app/views/dashboard/index.php` (Lines 163-181)

---

## What Was Added

### SIM Card Badge Display
Shows the GSM900A SIM card number installed in each ESP32 device.

**Location:** Device card header, below Device ID

**Visual Design:**
- **Badge Color:** Yellow/Amber (#FEF3C7 background, #92400E text)
- **Icon:** SIM card icon
- **Text:** Phone number (e.g., 09605682714)
- **Conditional:** Only displays if SIM number exists

---

## Code Changes

### Before:
```php
<!-- Device Header -->
<div class="flex items-start justify-between mb-4">
    <div class="flex-1">
        <h4><?= htmlspecialchars($device->device_name) ?></h4>
        <p>ID: <?= htmlspecialchars($device->device_id) ?></p>
    </div>
    <!-- ... badges ... -->
</div>
```

### After:
```php
<!-- Device Header -->
<div class="flex items-start justify-between mb-4">
    <div class="flex-1">
        <h4><?= htmlspecialchars($device->device_name) ?></h4>
        <p>ID: <?= htmlspecialchars($device->device_id) ?></p>
        
        <!-- ✅ NEW: SIM Card Number Badge -->
        <?php if ($device->sim_card_number): ?>
        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs font-semibold" 
             style="background-color: #FEF3C7; color: #92400E;">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z"/>
            </svg>
            <?= htmlspecialchars($device->sim_card_number) ?>
        </div>
        <?php endif; ?>
    </div>
    <!-- ... badges ... -->
</div>
```

---

## Visual Display

### Device Card Layout:

```
┌──────────────────────────────────┐
│ [Status Bar - Green/Gray]        │
├──────────────────────────────────┤
│ Office AC                    ✓   │ ← Device Name + Selection
│ ID: ESP32_OUTLET_001             │ ← Device ID
│ 📱 09605682714                   │ ← ✅ NEW: SIM Number Badge
│                          [Online]│
│                         [Active] │
├──────────────────────────────────┤
│  Outlet: ON    |  Mode: Manual   │
├──────────────────────────────────┤
│  🕐 Last Heartbeat               │
│     Aug 1, 2026 2:30 PM          │
├──────────────────────────────────┤
│  [View]    |    [Turn OFF]       │
└──────────────────────────────────┘
```

---

## Consistency Across Pages

### ✅ Outlet Control Page
- Shows SIM badge below Device ID
- Yellow/amber badge with SIM icon
- Same styling

### ✅ Dashboard Page (This Update)
- Shows SIM badge below Device ID
- Yellow/amber badge with SIM icon
- Same styling

### Result: Consistent UI
Both pages now display SIM card information in the same location with the same design.

---

## Badge Design Details

### Colors:
- **Background:** `#FEF3C7` (Amber-50)
- **Text:** `#92400E` (Amber-800)
- **Icon:** SIM card with chip design

### Spacing:
- **Padding:** `px-2 py-0.5` (8px horizontal, 2px vertical)
- **Gap:** `gap-1.5` (6px between icon and text)
- **Border Radius:** `rounded-lg` (8px)

### Typography:
- **Font Size:** `text-xs` (0.75rem / 12px)
- **Font Weight:** `font-semibold`

---

## Database Field Used

**Table:** `iot_outlet_devices`
**Column:** `sim_card_number` (VARCHAR)

**Example Values:**
- `09605682714` (Office AC)
- `09123456789` (Classroom 1 AC)
- `NULL` or empty (No SIM configured - badge hidden)

---

## Conditional Display Logic

```php
<?php if ($device->sim_card_number): ?>
    <!-- Show SIM badge -->
<?php endif; ?>
```

**Shows badge when:**
- ✅ `sim_card_number` is not NULL
- ✅ `sim_card_number` is not empty string
- ✅ `sim_card_number` has a valid value

**Hides badge when:**
- ❌ `sim_card_number` is NULL
- ❌ `sim_card_number` is empty ('')
- ❌ Device has no SIM configured

---

## Testing Checklist

### ✅ Test 1: Device with SIM
1. Open Dashboard
2. Find device with SIM number (e.g., ESP32_OUTLET_001 with 09605682714)
3. Verify yellow SIM badge appears below Device ID
4. Verify correct phone number is displayed

### ✅ Test 2: Device without SIM
1. Create or find device without SIM number
2. Open Dashboard
3. Verify NO SIM badge appears
4. Device card still displays normally

### ✅ Test 3: Visual Consistency
1. Open Dashboard (view device cards)
2. Open Outlet Control page
3. Compare SIM badge appearance
4. Verify same color, size, and position

### ✅ Test 4: Multiple Devices
1. Dashboard with 3+ devices
2. Each device shows its own SIM number
3. Different numbers displayed correctly
4. No mixing of data between cards

### ✅ Test 5: Responsive Design
1. View on desktop - Badge displays inline
2. View on tablet - Badge wraps appropriately
3. View on mobile - Badge still visible
4. No overflow or text cutting

---

## Where SIM Numbers Are Displayed

### ✅ Dashboard Page
**Location:** Device cards in "All Devices Status" section
**Display:** Yellow badge below Device ID
**Selectability:** Cards are selectable for bulk actions

### ✅ Outlet Control Page
**Location:** Device cards in main grid
**Display:** Yellow badge below Device ID
**Selectability:** Cards are selectable for bulk actions

### ✅ Admin > Devices Page
**Location:** Device list table
**Display:** Column showing SIM card number

### ✅ Admin > Create/Edit Device
**Location:** Form field
**Display:** Input field for entering SIM number

---

## Benefits

1. **Visibility:** Operators can see which SIM is installed in which device
2. **Identification:** Helps identify devices by their SIM number
3. **Troubleshooting:** Easy to verify correct SIM is configured
4. **SMS Control:** Know which number receives SMS commands
5. **Documentation:** Clear record of device-SIM mapping
6. **Consistency:** Same display on Dashboard and Outlet Control

---

## Related Features

### SIM Card Function:
- **GSM900A Module:** Connected to ESP32
- **SMS Commands:** Receives ON/OFF commands via SMS
- **Authorized Numbers:** ESP32 fetches list from server
- **Control:** Allows remote control via text message

### Database Structure:
```sql
ALTER TABLE iot_outlet_devices 
ADD COLUMN sim_card_number VARCHAR(20) DEFAULT NULL;
```

### ESP32 Code:
```cpp
// SIM card number is stored in device table
// ESP32 uses it to identify which device it is
// Displays on Serial Monitor during boot
```

---

## Summary

✅ **Dashboard now displays SIM card numbers** in device cards
✅ Same yellow badge design as Outlet Control page
✅ Conditional display (only shows if SIM exists)
✅ Consistent UI across all pages
✅ Easy identification of device SIM cards

Users can now see which phone number is installed in each ESP32 device directly from the Dashboard!
