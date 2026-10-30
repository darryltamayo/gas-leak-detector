# 📊 Database Schema

## 📁 Single File Import

**File:** `complete_schema.sql`

This file contains **EVERYTHING** you need:
- ✅ All tables and relationships
- ✅ All views and stored procedures
- ✅ All triggers and indexes
- ✅ Initial admin user
- ✅ Initial ESP32 device
- ✅ Initial authorized SIM number

---

## 🚀 How to Import

### **Step 1: Access phpMyAdmin**
```
1. Login to Hostinger control panel
2. Open phpMyAdmin
3. Select database: u907280979_docrequest
```

### **Step 2: Import Schema**
```
1. Click "Import" tab
2. Choose file: complete_schema.sql
3. Click "Go"
```

**Note:** This will DROP and recreate all tables (fresh start).

---

## 📋 What Gets Installed

### **1. Admin User**
```
Username: admin
Password: password
Role: Administrator
```

### **2. ESP32 Device**
```
Device ID: ESP32_OUTLET_001
API Key: IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
SIM Card: 09605682714
Status: Offline (until ESP32 connects)
```

### **3. Authorized SIM Number**
```
Phone Number: 09554778665
Owner: System Owner
Purpose: SMS control of outlet
Status: Active
```

---

## 🔐 Login After Import

```
URL: https://isu-documentrequest.online/ac_controller_system/
Username: admin
Password: password
```

**⚠️ Change password immediately after first login!**

---

## 📱 SMS Control

After ESP32 connects, send SMS from authorized number:

```
From: 09554778665
To: 09605682714 (SIM in ESP32)
Commands: ON, OFF, STATUS
```

---

## 🧪 Verify Installation

**Check tables exist:**
```sql
SHOW TABLES LIKE 'iot_outlet_%';
```

**Check admin user:**
```sql
SELECT * FROM iot_outlet_users WHERE username = 'admin';
```

**Check device:**
```sql
SELECT * FROM iot_outlet_devices WHERE device_id = 'ESP32_OUTLET_001';
```

**Check authorized SIM:**
```sql
SELECT * FROM iot_outlet_authorized_sims WHERE phone_number = '09554778665';
```

---

## 📊 Database Summary

**Tables:** 7
- iot_outlet_users
- iot_outlet_devices
- iot_outlet_authorized_sims
- iot_outlet_schedules
- iot_outlet_activity_logs
- iot_outlet_sessions
- iot_outlet_password_resets

**Views:** 3
- iot_outlet_active_schedules_view
- iot_outlet_recent_activity_view
- iot_outlet_device_status_view

**Stored Procedures:** 3
- sp_iot_outlet_cleanup_old_logs
- sp_iot_outlet_get_user_stats
- sp_iot_outlet_update_heartbeat

**Triggers:** 3
- trg_iot_outlet_user_login_log
- trg_iot_outlet_status_change
- trg_iot_outlet_device_offline_check

---

## 🎯 One File Does It All

```
✅ database/complete_schema.sql  ← Import this one file only

❌ NO separate migration files needed
❌ NO multiple SQL files to run
❌ NO manual INSERT statements needed
```

Everything is ready to go!

---

**Updated:** July 31, 2026
**Version:** 2.0 - Complete with authorized SIM
