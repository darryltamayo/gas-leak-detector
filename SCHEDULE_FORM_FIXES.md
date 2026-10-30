# Schedule Form Fixes - August 1, 2026

## Issues Fixed

### 1. Fatal Type Error When Creating Schedule
**Error Message:**
```
Fatal error: Uncaught TypeError: Cannot assign string to property Schedule::$device_id of type ?int
in ScheduleController.php:69
```

**Root Cause:**
- Form POST data returns all values as strings
- `$_POST['device_id']` is a string (e.g., "1")
- Schedule model property `device_id` is typed as `?int`
- PHP strict types don't auto-convert string to int

**Solution:**
- Cast `device_id` to integer before assignment
- Changed line 69 in `ScheduleController.php`:
```php
// OLD:
$schedule->device_id = $data['device_id'];

// NEW:
$schedule->device_id = (int)$data['device_id']; // Convert to int
```

---

### 2. Form Not Scrollable (Content Cut Off)
**Problem:**
- Create/Edit schedule modals have many fields
- On smaller screens or when content is long, fields were cut off
- No way to scroll to see all form fields
- Users couldn't reach submit button

**Solution:**
- Added scrollability to both modal containers
- Made header sticky so title/close button always visible

#### Create Modal Changes:
```html
<!-- OLD -->
<div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">

<!-- NEW -->
<div class="bg-white rounded-xl p-8 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
```

#### Header Made Sticky:
```html
<div class="flex justify-between items-center mb-6 sticky top-0 bg-white pb-4 border-b">
```

**CSS Classes Added:**
- `max-h-[90vh]` - Maximum height is 90% of viewport (leaves space at top/bottom)
- `overflow-y-auto` - Enables vertical scrolling when content exceeds max height
- `sticky top-0` - Header stays at top when scrolling
- `border-b` - Visual separator for sticky header

---

### 3. Device Selection Simplified
**Problem:**
- Form showed checkboxes for multiple device selection (`device_ids[]`)
- Backend only supports ONE device per schedule
- Confusing UI/UX mismatch

**Solution:**
- Changed from checkboxes to dropdown (select)
- Single device selection only
- Clear message: "Each schedule controls one device"

#### OLD (Checkbox List):
```html
<div class="bg-white rounded-lg border p-4 space-y-2">
    <label><input type="checkbox" name="device_ids[]" value="1">Device 1</label>
    <label><input type="checkbox" name="device_ids[]" value="2">Device 2</label>
</div>
```

#### NEW (Dropdown):
```html
<select name="device_id" required>
    <option value="">-- Select a device --</option>
    <option value="1">Office AC (ESP32_OUTLET_001) - Online</option>
    <option value="2">Classroom AC (ESP32_OUTLET_002) - Online</option>
</select>
```

**Benefits:**
- Clearer single-device selection
- Matches backend logic
- Easier to understand
- Less screen space needed

---

## Files Modified

### 1. `app/controllers/ScheduleController.php`
- **Line 69**: Added `(int)` cast to `device_id`
- **Reason**: Fix type error when assigning string to int property

### 2. `app/views/schedule/index.php`
- **Create Modal Container**: Added `max-h-[90vh] overflow-y-auto`
- **Edit Modal Container**: Added `max-h-[90vh] overflow-y-auto`
- **Modal Headers**: Added `sticky top-0 bg-white pb-4 border-b`
- **Device Selection**: Changed from checkboxes to dropdown select
- **Reason**: Enable scrolling and simplify device selection

---

## Testing Checklist

### ✅ Create Schedule Form
1. Click "Create Schedule" button
2. Form opens in modal
3. Can see all fields
4. Can scroll if content exceeds screen height
5. Header stays at top when scrolling
6. Select single device from dropdown
7. Fill all fields
8. Submit successfully (no type error)

### ✅ Edit Schedule Form
1. Click "Edit" on existing schedule
2. Form opens with pre-filled data
3. Can scroll through all fields
4. Header stays at top
5. Update schedule successfully

### ✅ Mobile/Small Screen
1. Test on mobile device or narrow browser window
2. Modal is scrollable
3. All fields accessible
4. Submit button reachable

---

## Before vs After

### Before:
❌ Form content cut off on small screens
❌ No scrolling available
❌ Submit button hidden
❌ Fatal type error when submitting
❌ Confusing multi-device checkbox UI

### After:
✅ Form fully scrollable
✅ Maximum height is 90% of viewport
✅ Header stays visible while scrolling
✅ No type errors - device_id properly cast to int
✅ Clear single-device dropdown selection
✅ Works on all screen sizes

---

## Technical Details

### Tailwind CSS Classes Used
- `max-h-[90vh]` - Arbitrary value for max height
- `overflow-y-auto` - Vertical scroll when needed
- `sticky` - Position sticky
- `top-0` - Stick to top
- `border-b` - Bottom border

### Type Casting in PHP
```php
// String to int conversion
$deviceId = "5";              // string
$deviceId = (int)$deviceId;   // int(5)

// OR
$deviceId = intval($deviceId); // int(5)
```

### Why This Matters
- PHP 7.4+ has strict property types
- `?int` means "nullable integer"
- Cannot assign string to int property without explicit cast
- Error is thrown at runtime, not compile time

---

## Future Improvements (Optional)

1. **Multi-Device Schedules**
   - If needed in future, create schedule_devices junction table
   - One schedule → many devices
   - Requires schema changes

2. **Mobile-First Modal Design**
   - Could make modal full-screen on mobile
   - Better touch targets
   - Slide-up animation

3. **Form Validation**
   - Client-side validation before submit
   - Better error messages
   - Field-level error highlighting

---

## Summary

Both issues are now fixed:
1. ✅ **Type Error**: Device ID is properly cast to integer
2. ✅ **Scrollability**: Modals are scrollable with sticky headers
3. ✅ **Bonus**: Simplified device selection UI

Users can now create and edit schedules without errors or UI issues!
