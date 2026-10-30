# Schedule Protection Flow Diagram

## Complete Flow: Regular User vs Admin

```
┌─────────────────────────────────────────────────────────────────┐
│                    USER TRIES TO TURN OFF DEVICE                │
└─────────────────────────────────────────────────────────────────┘
                              ↓
                    ┌─────────────────┐
                    │  Device Online? │
                    └─────────────────┘
                       /            \
                    NO /              \ YES
                      ↓                ↓
              ┌─────────────┐   ┌──────────────────┐
              │   ERROR:    │   │ Check if Active  │
              │   Offline   │   │  Schedule Exists │
              └─────────────┘   └──────────────────┘
                                   /              \
                             NO  /                  \ YES
                                ↓                    ↓
                    ┌────────────────┐    ┌──────────────────┐
                    │  Turn OFF      │    │  Check User Role │
                    │  Successfully  │    └──────────────────┘
                    │  Mode: Manual  │         /          \
                    └────────────────┘   USER /            \ ADMIN
                                             ↓              ↓
                              ┌──────────────────┐  ┌─────────────────┐
                              │   🔒 BLOCKED     │  │  ⚠️ SHOW        │
                              │                  │  │  CONFIRMATION   │
                              │ Show Message:    │  │  WARNING        │
                              │ - Schedule name  │  └─────────────────┘
                              │ - Creator        │          ↓
                              │ - End time       │    ┌─────────────┐
                              │ - "Admin only"   │    │ Admin       │
                              └──────────────────┘    │ Confirms?   │
                                                      └─────────────┘
                                                        /        \
                                                   NO /          \ YES
                                                     ↓            ↓
                                          ┌─────────────┐  ┌────────────────┐
                                          │   ABORTED   │  │  OVERRIDE      │
                                          │             │  │  Turn OFF      │
                                          │ Device      │  │  Mode: Manual  │
                                          │ stays ON    │  │  Log Activity  │
                                          └─────────────┘  └────────────────┘
```

---

## Decision Tree

```
Is device trying to turn OFF?
│
├─ NO (turning ON) → ✅ Allow immediately
│
└─ YES (turning OFF)
   │
   ├─ Is device online?
   │  │
   │  ├─ NO → ❌ Error: Device offline
   │  │
   │  └─ YES
   │     │
   │     └─ Is there an active schedule?
   │        │
   │        ├─ NO → ✅ Turn OFF normally
   │        │
   │        └─ YES (Active schedule exists)
   │           │
   │           └─ What is user role?
   │              │
   │              ├─ REGULAR USER
   │              │  └─ ❌ BLOCK IMMEDIATELY
   │              │     └─ Show: "Only admins can override"
   │              │
   │              └─ ADMIN
   │                 └─ Has admin confirmed override?
   │                    │
   │                    ├─ NO (First attempt)
   │                    │  └─ ⚠️ SHOW WARNING DIALOG
   │                    │     ├─ Display schedule details
   │                    │     ├─ Show consequences
   │                    │     └─ Wait for confirmation
   │                    │
   │                    └─ YES (Confirmed)
   │                       └─ ✅ OVERRIDE
   │                          ├─ Turn OFF device
   │                          ├─ Set mode to Manual
   │                          └─ Log activity
```

---

## State Transitions

### Regular User Attempt
```
[Device ON + Schedule Active]
        ↓
   User clicks "OFF"
        ↓
[Check: User = Regular] → BLOCKED
        ↓
[Show Protection Message]
        ↓
[Device remains ON]
[Mode: Scheduled]
[No Activity Logged]
```

### Admin First Attempt
```
[Device ON + Schedule Active]
        ↓
   Admin clicks "OFF"
        ↓
[Check: User = Admin] → WARN
        ↓
[Show Confirmation Dialog]
        ↓
   Admin sees warning
        ↓
[Waiting for decision...]
```

### Admin Confirms Override
```
[Confirmation Dialog Open]
        ↓
   Admin clicks "OK"
        ↓
[Send request with confirm_override=1]
        ↓
[Server processes override]
        ↓
[Device turns OFF]
[Mode: Manual]
[Activity Logged: "outlet_off"]
        ↓
[Success message shown]
[Page reloads]
```

### Admin Cancels Override
```
[Confirmation Dialog Open]
        ↓
   Admin clicks "Cancel"
        ↓
[Dialog closes]
        ↓
[Device remains ON]
[Mode: Scheduled]
[No Activity Logged]
[No error shown]
```

---

## HTTP Request Flow

### Sequence 1: Regular User Blocked
```
1. POST /outlet/control-device
   Body: { device_id: 1, action: "off" }
   
2. ← 403 Forbidden
   {
     "success": false,
     "blocked_by_schedule": true,
     "schedule_info": { ... }
   }
   
3. Frontend shows error toast
4. End
```

### Sequence 2: Admin Override (Full Flow)
```
1. POST /outlet/control-device
   Body: { device_id: 1, action: "off" }
   
2. ← 409 Conflict
   {
     "success": false,
     "requires_confirmation": true,
     "is_admin_override": true,
     "schedule_info": { ... }
   }
   
3. Frontend shows confirmation dialog
4. User clicks "OK"

5. POST /outlet/control-device
   Body: { device_id: 1, action: "off", confirm_override: "1" }
   
6. ← 200 Success
   {
     "success": true,
     "message": "Device turned OFF",
     "status": "off",
     "mode": "manual"
   }
   
7. Frontend shows success toast
8. Page reloads after 1 second
9. End
```

---

## Schedule Active Definition

A schedule is considered "ACTIVE" when:

```
✅ Schedule is_enabled = 1
        AND
✅ Current time >= time_on
        AND
✅ Current time < time_off
        AND
✅ Current day is in days_of_week
        AND
✅ (If repeat_type = "once")
   → schedule_date = today
```

Example:
```
Schedule: "Office Hours"
- Time ON: 08:00 AM
- Time OFF: 05:00 PM
- Days: Mon, Tue, Wed, Thu, Fri
- Enabled: Yes
- Today: Monday, 2:00 PM

Result: ACTIVE ✅
(2:00 PM is between 8 AM and 5 PM, and today is Monday)
```

---

## User Role Behavior Matrix

| Action | Device State | Regular User | Admin (No Confirm) | Admin (Confirmed) |
|--------|--------------|--------------|-------------------|-------------------|
| Turn ON | Any | ✅ Allow | ✅ Allow | N/A |
| Turn OFF | No Schedule | ✅ Allow | ✅ Allow | N/A |
| Turn OFF | Schedule Active | ❌ Block | ⚠️ Warn | ✅ Override |
| Turn OFF | Schedule Inactive | ✅ Allow | ✅ Allow | N/A |
| Toggle ON→OFF | Schedule Active | ❌ Block | ⚠️ Warn | ✅ Override |
| Toggle OFF→ON | Any | ✅ Allow | ✅ Allow | N/A |

Legend:
- ✅ Allow = Action proceeds immediately
- ❌ Block = Action denied, error shown
- ⚠️ Warn = Confirmation required
- N/A = Not applicable

---

## Protection Bypass Scenarios

### When Protection Does NOT Apply

**Turning ON:**
```
Action: Turn ON
Protection: ❌ NEVER APPLIES
Reason: Schedules only protect against turning OFF
```

**No Active Schedule:**
```
Schedule Time: 08:00 - 17:00
Current Time: 18:00 (6:00 PM)
Protection: ❌ DOES NOT APPLY
Reason: Outside schedule window
```

**Wrong Day:**
```
Schedule Days: Mon, Tue, Wed, Thu, Fri
Current Day: Saturday
Protection: ❌ DOES NOT APPLY
Reason: Not a scheduled day
```

**Schedule Disabled:**
```
Schedule Enabled: NO
Protection: ❌ DOES NOT APPLY
Reason: Disabled schedules don't protect
```

**Device Already OFF:**
```
Current Status: OFF
Action: Turn OFF again
Protection: ❌ DOES NOT APPLY
Reason: Already in target state
```

---

## Error Codes & Messages

| Code | Scenario | User Type | Message Type |
|------|----------|-----------|--------------|
| 200 | Success | Any | Success toast |
| 403 | Schedule blocks user | Regular | Error toast |
| 409 | Needs admin confirmation | Admin | Confirm dialog |
| 500 | Server error | Any | Error toast |
| 503 | Device offline | Any | Error toast |

---

## Frontend State Machine

```
State: IDLE
   ↓ [User clicks OFF button]
State: LOADING (button disabled, spinner shown)
   ↓ [Fetch request sent]
State: WAITING_RESPONSE
   ↓
   ├─ [200] → State: SUCCESS
   │           ↓ [Show success toast]
   │           ↓ [Reload page after 1s]
   │           → State: IDLE
   │
   ├─ [403] → State: BLOCKED
   │           ↓ [Show error toast]
   │           ↓ [Re-enable button]
   │           → State: IDLE
   │
   ├─ [409 + is_admin_override] → State: CONFIRMING
   │           ↓ [Show confirm dialog]
   │           ├─ [OK] → State: OVERRIDING
   │           │         ↓ [Send with confirm_override=1]
   │           │         → State: WAITING_RESPONSE
   │           │
   │           └─ [Cancel] → State: IDLE
   │                         ↓ [Re-enable button]
   │
   └─ [Error] → State: ERROR
               ↓ [Show error toast]
               ↓ [Re-enable button]
               → State: IDLE
```

---

## Activity Log Entries

### Regular Control (No Schedule)
```
Timestamp: 2026-08-02 14:30:45
User: john.doe
Device: ESP32_OUTLET_001
Action: outlet_off
Details: "Device 'Lab Room AC' turned OFF via manual control"
Status: success
```

### Admin Override (With Schedule)
```
Timestamp: 2026-08-02 14:30:45
User: admin.user
Device: ESP32_OUTLET_001
Action: outlet_off
Details: "Device 'Lab Room AC' turned OFF via manual control"
Status: success
```

**Note:** Override is logged as regular control. To identify overrides, cross-reference with schedule times.

---

## Testing Checklist

- [ ] Regular user blocked during schedule
- [ ] Admin sees confirmation during schedule
- [ ] Admin can cancel confirmation
- [ ] Admin can confirm and override
- [ ] No warning when no schedule active
- [ ] No warning for turning ON
- [ ] Correct schedule info shown in warning
- [ ] Activity logged correctly
- [ ] CSRF protection working
- [ ] Network error handled gracefully
- [ ] Button states correct (disabled/enabled)
- [ ] Page reloads after success
- [ ] Toast messages appropriate

---

This flow ensures:
✅ Regular users cannot interrupt schedules
✅ Admins can override but must confirm
✅ All actions are deliberate and logged
✅ Emergency access is preserved

