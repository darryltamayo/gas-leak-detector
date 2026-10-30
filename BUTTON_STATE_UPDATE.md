# Button State Update - Disabled Active State

## Date: July 31, 2026

## Issue
User requested that control buttons reflect the current device state:
- When device is ON → ON button should be disabled
- When device is OFF → OFF button should be disabled

## Solution

### Files Modified
- `app/views/outlet/index.php` (lines 156-173)

### Changes Made

#### ON Button
```php
<?= (!$dev->is_online || $dev->outlet_status === 'on') ? 'disabled' : '' ?>
```
- Disabled when:
  - Device is offline OR
  - Device is already ON

#### OFF Button
```php
<?= (!$dev->is_online || $dev->outlet_status === 'off') ? 'disabled' : '' ?>
```
- Disabled when:
  - Device is offline OR
  - Device is already OFF

### Visual States

#### When Device is ON:
- ✅ OFF button: Enabled (green background, hover effects)
- ❌ ON button: Disabled (50% opacity, no hover, cursor not-allowed)

#### When Device is OFF:
- ✅ ON button: Enabled (green background, hover effects)
- ❌ OFF button: Disabled (50% opacity, no hover, cursor not-allowed)

#### When Device is Offline:
- ❌ Both buttons: Disabled (50% opacity, no hover, cursor not-allowed)

### CSS Classes Applied
When button is disabled:
- `opacity-50` - Makes button appear faded
- `cursor-not-allowed` - Shows "not allowed" cursor on hover
- Removes `active:scale-95` - No press animation
- Removes hover color changes

## Benefits
1. **Clear Visual Feedback**: Users instantly see the current state
2. **Prevents Redundant Actions**: Can't click ON when already ON
3. **Better UX**: Clearer what actions are available
4. **Matches Current State**: Button states always reflect reality

## Testing
1. Load outlets page
2. Check device that is ON → ON button should be disabled
3. Click OFF button → Device turns OFF → OFF button becomes disabled
4. Click ON button → Device turns ON → ON button becomes disabled
5. Test with offline device → Both buttons disabled

## Code Location
File: `app/views/outlet/index.php`
Lines: 156-173 (Control Buttons section in device card)
