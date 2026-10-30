# Device Card Display Issue - Debug Guide

## Problem
Device ID, device name, and SIM number are not displaying in the device cards.

## Possible Causes

### 1. No Devices in Database
**Check**: Open database and run:
```sql
SELECT id, device_name, device_id, sim_card_number FROM iot_outlet_devices;
```

**Expected**: Should see at least one device (ESP32_OUTLET_001)

**If empty**: The device wasn't created properly

### 2. Template Not Receiving Data
**Check**: Add this PHP debug code at the top of `app/views/outlet/index.php` (after line 1):
```php
<?php
echo '<pre>';
echo 'Number of devices: ' . count($devices) . "\n";
if (!empty($devices)) {
    echo 'First device data:' . "\n";
    print_r($devices[0]);
}
echo '</pre>';
?>
```

**Expected**: Should show device count and first device details

### 3. Properties Not Being Rendered
**Check**: Look at the HTML source of the page (View Page Source in browser)

Search for: `device-card-selectable-outlet`

**Expected HTML**:
```html
<div class="device-card-selectable-outlet" data-device-id="1">
    <h3>Main Outlet</h3>
    <p>ESP32_OUTLET_001</p>
</div>
```

**If you see empty values**:
```html
<div class="device-card-selectable-outlet" data-device-id="">
    <h3></h3>
    <p></p>
</div>
```

This means the properties are null in the Device object.

### 4. JavaScript Error
**Check**: Open browser console (F12 → Console tab)

**Look for**: Any JavaScript errors that might be preventing rendering

### 5. CSS Hiding Content
**Check**: Open browser DevTools (F12 → Elements tab)

**Look for**: Device card elements with:
- `display: none`
- `visibility: hidden`
- `opacity: 0`
- Text color same as background

## Quick Diagnostic Test

Add this temporary debug code to `app/views/outlet/index.php` around line 120 (before the device card loop):

```php
<!-- DEBUG: Show raw device data -->
<div style="background: yellow; padding: 20px; margin: 20px; border: 2px solid red;">
    <h2>DEBUG: Device Data</h2>
    <pre><?php print_r($devices); ?></pre>
</div>
```

This will show you exactly what data is being passed to the view.

## What You Should See

### In Database:
```
id | device_name  | device_id         | sim_card_number
1  | Main Outlet  | ESP32_OUTLET_001  | 09605682714
```

### In Page Source:
```html
<h3 class="text-lg font-bold mb-2" style="color: #1D1D1F;">
    Main Outlet
</h3>
<p class="text-xs font-mono" style="color: #86868B;">
    ESP32_OUTLET_001
</p>
```

### On Screen:
A card with:
- Title: "Main Outlet"
- Device ID: "ESP32_OUTLET_001"
- Status indicator
- ON/OFF buttons

## Common Solutions

### Solution 1: Database Empty
```sql
-- Check if device exists
SELECT * FROM iot_outlet_devices;

-- If empty, insert the device
INSERT INTO iot_outlet_devices (device_name, device_id, api_key, sim_card_number, outlet_status, control_mode, is_active) 
VALUES ('Main Outlet', 'ESP32_OUTLET_001', 'IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T', '09605682714', 'off', 'manual', 1);
```

### Solution 2: Cache Issue
```
1. Clear browser cache (Ctrl+Shift+Delete)
2. Hard refresh page (Ctrl+F5)
3. Try incognito/private mode
```

### Solution 3: PHP Error
Check server error logs for PHP errors:
- cPanel → Error Logs
- Look for errors in `OutletController.php` or `DeviceRepository.php`

### Solution 4: Template Issue
The template at line 127-135 in `outlet/index.php` should show:
```php
<h3 class="text-lg font-bold mb-2" style="color: #1D1D1F;">
    <?= htmlspecialchars($dev->device_name) ?>
</h3>
<p class="text-xs font-mono" style="color: #86868B;">
    <?= htmlspecialchars($dev->device_id) ?>
</p>
```

If this code is missing or commented out, that's the issue.

## Step-by-Step Debug Process

1. **Check database**:
   ```sql
   SELECT COUNT(*) FROM iot_outlet_devices;
   ```
   If 0 → No devices registered
   If > 0 → Device exists, continue

2. **Check PHP is receiving data**:
   Add debug code to controller or view (see above)
   
3. **Check HTML is being generated**:
   View page source → Search for device-card-selectable-outlet
   
4. **Check CSS/JS isn't hiding it**:
   Open DevTools → Inspect device card element

5. **Check browser console**:
   F12 → Console tab → Look for errors

## Expected Full Device Card HTML

```html
<div class="device-card-selectable-outlet bg-white rounded-xl p-6 border-2 transition-all duration-300 relative overflow-hidden cursor-pointer" 
     style="border-color: #E5E5E7; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);" 
     data-device-id="1" 
     data-device-online="1">
    
    <div class="text-center mb-6 pt-2">
        <h3 class="text-lg font-bold mb-2" style="color: #1D1D1F;">
            Main Outlet
        </h3>
        <p class="text-xs font-mono" style="color: #86868B;">
            ESP32_OUTLET_001
        </p>
    </div>
    
    <!-- Rest of card content -->
</div>
```

## What To Report Back

Please check and report:
1. ☐ Number of devices in database (SELECT COUNT(*))
2. ☐ View page source - do you see device data in HTML?
3. ☐ Browser console - any errors?
4. ☐ Screenshot of what you currently see
5. ☐ Result of adding debug PHP code (print_r)

This will help me pinpoint exactly where the issue is!
