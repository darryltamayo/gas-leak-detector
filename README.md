# Smart AC Outlet Controller System

IoT-based smart outlet controller with ESP32, web dashboard, and automated scheduling.

## 🎯 Features

- **Web Dashboard** - Control outlet remotely via web interface
- **Automated Schedules** - Set daily/weekly ON/OFF times
- **SMS Control** - Control via SMS commands (SIM900A)
- **ESP32 Integration** - Real-time hardware control
- **User Management** - Admin and regular user roles
- **Activity Logging** - Track all actions and schedule executions
- **Safety Features** - Relay defaults to OFF without internet

## 📋 System Requirements

### Server:
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite

### Hardware:
- ESP32 Dev Board
- SIM900A GSM Module (optional for SMS)
- Single Channel Relay Module
- WiFi connection

## 🚀 Quick Start

### 1. Database Setup

Import the SQL file:
```bash
mysql -u your_user -p your_database < database/complete_schema.sql
```

### 2. Configuration

Copy `.env.example` to `.env` and configure:
```env
DB_HOST=localhost
DB_NAME=your_database
DB_USER=your_user
DB_PASS=your_password
API_KEY=IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T
```

### 3. Web Server

Upload to server:
- Root directory: `public_html/ac_controller_system/`
- Ensure `.htaccess` is uploaded
- Verify `.env` file exists on server

### 4. ESP32 Setup

1. Open `esp32/iot_outlet_controller.ino` in Arduino IDE
2. Configure WiFi credentials:
   ```cpp
   const char* WIFI_SSID = "YourWiFiName";
   const char* WIFI_PASSWORD = "YourPassword";
   ```
3. Configure API settings (match your server)
4. Upload to ESP32

### 5. Cron Job Setup

For automatic schedules, set up external cron service (e.g., cron-job.org):
- URL: `https://your-domain.com/ac_controller_system/public/cron.php?key=YOUR_API_KEY`
- Interval: Every 1 minute

## 📖 Documentation

### Main Guides:
- **DEPLOYMENT_CHECKLIST.txt** - Complete deployment steps
- **SCHEDULE_TROUBLESHOOTING.md** - Schedule system help
- **FINAL_DEPLOYMENT_GUIDE.md** - Comprehensive deployment guide

### ESP32 Documentation:
- **esp32/ESP32_COMPLETE_GUIDE.md** - Full ESP32 setup
- **esp32/TROUBLESHOOTING.md** - Hardware troubleshooting
- **esp32/SAFETY_FEATURES.md** - Safety feature documentation

### Database:
- **database/README.md** - Database setup instructions
- **database/complete_schema.sql** - Complete database schema

### Cron:
- **cron/README.md** - Cron job setup instructions

## 🔐 Default Login

- **Username:** admin
- **Password:** password
- ⚠️ **Change password immediately after first login!**

## 🎛️ Control Modes

### Manual Mode
- Control via web buttons or SMS
- Schedules are disabled
- Full manual control

### Scheduled Mode
- Automatic control via schedules
- Manual control disabled (except admin override)
- Recommended for automation

### Override Mode (Admin Only)
- Force control regardless of schedules
- Highest priority

## 📅 Schedule System

### How It Works:
1. Create schedules with ON/OFF times
2. Set days of week (or specific date for one-time)
3. Enable schedules (toggle ON)
4. Set device control mode to "Scheduled"
5. Schedules execute automatically via cron job

### Schedule Types:
- **Repeat Daily** - Runs every selected day
- **Once Only** - Runs once on specific date, then auto-disables

### Schedule Protection:
- Regular users cannot turn OFF during active schedule
- Admin users can override anytime
- Manual control switches mode to "Manual" (schedules stop)

## 🛡️ Safety Features

### ESP32 Safety:
- **Default OFF** - Relay stays OFF on power-on/reset
- **WiFi Loss** - Relay turns OFF when connection lost
- **Internet Required** - Won't turn ON without server connection

### Web Safety:
- CSRF protection on all forms
- Session security
- Role-based access control
- Activity logging

## 🔧 Troubleshooting

### Schedules Not Working:
1. Check schedules are enabled (toggle ON)
2. Verify control mode is "Scheduled"
3. Ensure device is online
4. Confirm cron job is running
5. Read: `SCHEDULE_TROUBLESHOOTING.md`

### ESP32 Not Connecting:
1. Verify WiFi credentials
2. Check WiFi signal strength
3. Confirm API URL and key are correct
4. View Serial Monitor (115200 baud)
5. Read: `esp32/TROUBLESHOOTING.md`

### 500 Server Error:
1. Check `.env` file exists on server
2. Verify database credentials
3. Check server PHP version (need 7.4+)
4. View server error logs

## 📊 Project Structure

```
ac_controller_system/
├── app/
│   ├── controllers/     # Request handlers
│   ├── models/         # Data models
│   ├── repositories/   # Database operations
│   ├── services/       # Business logic
│   ├── views/          # UI templates
│   ├── middleware/     # Authentication, CSRF
│   └── helpers/        # Utility functions
├── config/             # Configuration files
├── cron/              # Cron job scripts
├── database/          # SQL schemas
├── esp32/             # Arduino code & docs
├── public/            # Public web files
│   ├── assets/       # CSS, JS, images
│   ├── cron.php      # Cron executor
│   └── index.php     # Entry point
├── routes/            # Route definitions
├── storage/           # Runtime files
└── .env              # Environment config
```

## 🔄 Workflow

### User Interaction:
```
User → Web Dashboard → PHP Backend → Database
                              ↓
                         Activity Log
```

### Schedule Execution:
```
Cron Service (every 1 min)
    ↓
cron.php (check schedules)
    ↓
Database Update (outlet_status)
    ↓
ESP32 Polls API (every 10 sec)
    ↓
Relay Control (physical outlet)
```

### SMS Control:
```
SMS → SIM900A → ESP32 → API → Database → Response SMS
```

## 🎓 Usage Examples

### Creating a Schedule:
1. Go to **Schedules** page
2. Click **"Create Schedule"**
3. Set schedule name: "Morning AC"
4. Set ON time: 7:00 AM
5. Set OFF time: 9:00 AM
6. Select days: Monday-Friday
7. Enable schedule (toggle ON)
8. Go to **Outlet Control** → Set mode to "Scheduled"

### Manual Control:
1. Go to **Outlet Control** page
2. Click **"Turn ON"** or **"Turn OFF"**
3. Confirm action
4. ⚠️ Note: This switches mode to "Manual"

### SMS Control (if SIM900A connected):
- Send **"ON"** to turn outlet ON
- Send **"OFF"** to turn outlet OFF
- Send **"STATUS"** to get current status

## 📱 API Endpoints

### Device Endpoints:
- `POST /api/heartbeat` - Device keepalive
- `GET /api/status` - Get outlet status
- `POST /api/sms` - SMS command processing

### Web Endpoints:
- `POST /outlet/turn-on` - Turn outlet ON
- `POST /outlet/turn-off` - Turn outlet OFF
- `POST /outlet/toggle` - Toggle outlet
- `POST /outlet/set-mode` - Change control mode

## 🤝 Contributing

This is a private project. For issues or questions, contact the system administrator.

## 📄 License

Proprietary - Isabela State University, Ilagan Campus

## 👥 Credits

**System:** Smart AC Outlet Controller  
**Institution:** Isabela State University - Ilagan Campus  
**Version:** 1.0.0  
**Year:** 2025-2026

---

## 🆘 Support

For detailed help, see:
- `DEPLOYMENT_CHECKLIST.txt` - Setup instructions
- `SCHEDULE_TROUBLESHOOTING.md` - Schedule issues
- `FINAL_DEPLOYMENT_GUIDE.md` - Complete guide
- `esp32/ESP32_COMPLETE_GUIDE.md` - ESP32 help

---

**Status:** Production Ready ✅  
**Last Updated:** January 2025
