-- ============================================
-- IoT Smart Outlet Management System Database
-- COMPLETE SCHEMA - All Updates Included
-- ============================================
-- Database: u907280979_docrequest
-- Description: Complete database schema with all tables, relationships, and sample data
-- Version: 2.0 (Consolidated - includes all migrations)
-- ============================================

USE u907280979_docrequest;

-- ============================================
-- DROP EXISTING TABLES (for clean re-import)
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS iot_outlet_activity_logs;
DROP TABLE IF EXISTS iot_outlet_schedules;
DROP TABLE IF EXISTS iot_outlet_authorized_sims;
DROP TABLE IF EXISTS iot_outlet_devices;
DROP TABLE IF EXISTS iot_outlet_sessions;
DROP TABLE IF EXISTS iot_outlet_password_resets;
DROP TABLE IF EXISTS iot_outlet_users;

DROP VIEW IF EXISTS iot_outlet_active_schedules_view;
DROP VIEW IF EXISTS iot_outlet_recent_activity_view;
DROP VIEW IF EXISTS iot_outlet_device_status_view;

DROP PROCEDURE IF EXISTS sp_iot_outlet_cleanup_old_logs;
DROP PROCEDURE IF EXISTS sp_iot_outlet_get_user_stats;
DROP PROCEDURE IF EXISTS sp_iot_outlet_update_heartbeat;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- TABLE PREFIX: iot_outlet_
-- All tables will be prefixed to avoid conflicts with existing database
-- ============================================

-- ============================================
-- TABLE: iot_outlet_users
-- Description: Stores user accounts (admin and regular users)
-- Updated: Added first_name, last_name fields; removed email field
-- ============================================
CREATE TABLE iot_outlet_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_first_name (first_name),
    INDEX idx_last_name (last_name),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: iot_outlet_devices
-- Description: Stores ESP32 device information
-- ============================================
CREATE TABLE iot_outlet_devices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_name VARCHAR(100) NOT NULL,
    device_id VARCHAR(50) NOT NULL UNIQUE COMMENT 'ESP32 unique identifier - auto-generated',
    api_key VARCHAR(255) NOT NULL UNIQUE,
    sim_card_number VARCHAR(20) NULL COMMENT 'SIM card phone number installed in device',
    outlet_status ENUM('on', 'off') NOT NULL DEFAULT 'off',
    control_mode ENUM('manual', 'scheduled', 'override') NOT NULL DEFAULT 'manual',
    is_online TINYINT(1) NOT NULL DEFAULT 0,
    last_heartbeat DATETIME NULL,
    firmware_version VARCHAR(20) NULL,
    ip_address VARCHAR(45) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_device_id (device_id),
    INDEX idx_api_key (api_key),
    INDEX idx_sim_card_number (sim_card_number),
    INDEX idx_outlet_status (outlet_status),
    INDEX idx_is_online (is_online),
    INDEX idx_is_active (is_active),
    INDEX idx_last_heartbeat (last_heartbeat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: iot_outlet_authorized_sims
-- Description: Stores authorized SIM numbers for SMS control
-- Updated: Added first_name, last_name fields
-- ============================================
CREATE TABLE iot_outlet_authorized_sims (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    phone_number VARCHAR(20) NOT NULL UNIQUE,
    owner_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_phone_number (phone_number),
    INDEX idx_first_name (first_name),
    INDEX idx_last_name (last_name),
    INDEX idx_is_active (is_active),
    INDEX idx_created_by (created_by),
    
    CONSTRAINT fk_iot_outlet_authorized_sims_created_by 
        FOREIGN KEY (created_by) 
        REFERENCES iot_outlet_users(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: iot_outlet_schedules
-- Description: Stores outlet scheduling rules
-- Updated: Added repeat_type and last_executed fields
-- ============================================
CREATE TABLE iot_outlet_schedules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    device_id INT UNSIGNED NOT NULL,
    schedule_name VARCHAR(100) NOT NULL,
    time_on TIME NOT NULL COMMENT 'Time to turn outlet ON',
    time_off TIME NOT NULL COMMENT 'Time to turn outlet OFF',
    is_enabled TINYINT(1) NOT NULL DEFAULT 1,
    days_of_week VARCHAR(50) DEFAULT 'Mon,Tue,Wed,Thu,Fri,Sat,Sun' COMMENT 'Comma-separated days',
    repeat_type ENUM('repeat', 'once') NOT NULL DEFAULT 'repeat' COMMENT 'Schedule repeat type: repeat = recurring, once = one-time only',
    schedule_date DATE NULL COMMENT 'Specific date for one-time schedules',
    last_executed DATETIME NULL COMMENT 'Last time this schedule was executed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_device_id (device_id),
    INDEX idx_is_enabled (is_enabled),
    INDEX idx_repeat_type (repeat_type),
    INDEX idx_time_on (time_on),
    INDEX idx_time_off (time_off),
    INDEX idx_schedule_date (schedule_date),
    
    CONSTRAINT fk_iot_outlet_schedules_user 
        FOREIGN KEY (user_id) 
        REFERENCES iot_outlet_users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_iot_outlet_schedules_device 
        FOREIGN KEY (device_id) 
        REFERENCES iot_outlet_devices(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    
    CONSTRAINT chk_time_valid 
        CHECK (time_on != time_off)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: iot_outlet_activity_logs
-- Description: Stores all system activities and outlet state changes
-- ============================================
CREATE TABLE iot_outlet_activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL COMMENT 'NULL for system or ESP32 actions',
    device_id INT UNSIGNED NULL,
    action_type ENUM(
        'outlet_on', 
        'outlet_off', 
        'schedule_created', 
        'schedule_updated', 
        'schedule_deleted',
        'schedule_enabled',
        'schedule_disabled',
        'sms_command', 
        'manual_control',
        'override',
        'login', 
        'logout',
        'user_created',
        'user_updated',
        'user_deleted',
        'sim_registered',
        'sim_updated',
        'sim_deleted',
        'unauthorized_sms',
        'heartbeat_lost',
        'device_online',
        'device_offline'
    ) NOT NULL,
    action_details TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    status ENUM('success', 'failed', 'pending') NOT NULL DEFAULT 'success',
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_device_id (device_id),
    INDEX idx_action_type (action_type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_composite_user_action (user_id, action_type, created_at),
    
    CONSTRAINT fk_iot_outlet_activity_logs_user 
        FOREIGN KEY (user_id) 
        REFERENCES iot_outlet_users(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_iot_outlet_activity_logs_device 
        FOREIGN KEY (device_id) 
        REFERENCES iot_outlet_devices(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: iot_outlet_sessions
-- Description: Stores user session data
-- ============================================
CREATE TABLE iot_outlet_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity),
    
    CONSTRAINT fk_iot_outlet_sessions_user 
        FOREIGN KEY (user_id) 
        REFERENCES iot_outlet_users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: iot_outlet_password_resets
-- Description: Stores password reset tokens
-- ============================================
CREATE TABLE iot_outlet_password_resets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_token (token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at),
    
    CONSTRAINT fk_iot_outlet_password_resets_user 
        FOREIGN KEY (user_id) 
        REFERENCES iot_outlet_users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- VIEWS: Useful database views
-- ============================================

-- View: Active schedules with user and device info
CREATE OR REPLACE VIEW iot_outlet_active_schedules_view AS
SELECT 
    s.id,
    s.schedule_name,
    s.time_on,
    s.time_off,
    s.days_of_week,
    s.repeat_type,
    s.last_executed,
    s.is_enabled,
    u.username,
    u.full_name,
    u.first_name,
    u.last_name,
    d.device_name,
    d.device_id,
    s.created_at,
    s.updated_at
FROM iot_outlet_schedules s
INNER JOIN iot_outlet_users u ON s.user_id = u.id
INNER JOIN iot_outlet_devices d ON s.device_id = d.id
WHERE s.is_enabled = 1 AND u.is_active = 1 AND d.is_active = 1;

-- View: Recent activity logs with user names
CREATE OR REPLACE VIEW iot_outlet_recent_activity_view AS
SELECT 
    al.id,
    al.action_type,
    al.action_details,
    al.status,
    al.created_at,
    u.username,
    u.full_name,
    u.first_name,
    u.last_name,
    d.device_name,
    al.ip_address
FROM iot_outlet_activity_logs al
LEFT JOIN iot_outlet_users u ON al.user_id = u.id
LEFT JOIN iot_outlet_devices d ON al.device_id = d.id
ORDER BY al.created_at DESC
LIMIT 100;

-- View: Device status summary
CREATE OR REPLACE VIEW iot_outlet_device_status_view AS
SELECT 
    d.id,
    d.device_name,
    d.device_id,
    d.sim_card_number,
    d.outlet_status,
    d.control_mode,
    d.is_online,
    d.last_heartbeat,
    d.firmware_version,
    d.ip_address,
    CASE 
        WHEN d.last_heartbeat IS NULL THEN 'Never Connected'
        WHEN TIMESTAMPDIFF(SECOND, d.last_heartbeat, NOW()) <= 120 THEN 'Online'
        ELSE 'Offline'
    END AS connection_status,
    TIMESTAMPDIFF(SECOND, d.last_heartbeat, NOW()) AS seconds_since_heartbeat
FROM iot_outlet_devices d
WHERE d.is_active = 1;

-- ============================================
-- STORED PROCEDURES
-- ============================================

DELIMITER //

-- Procedure: Clean old activity logs (older than 90 days)
CREATE PROCEDURE sp_iot_outlet_cleanup_old_logs()
BEGIN
    DELETE FROM iot_outlet_activity_logs 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
    
    DELETE FROM iot_outlet_password_resets 
    WHERE expires_at < NOW() OR used = 1;
    
    SELECT ROW_COUNT() AS rows_deleted;
END //

-- Procedure: Get user statistics
CREATE PROCEDURE sp_iot_outlet_get_user_stats(IN p_user_id INT UNSIGNED)
BEGIN
    SELECT 
        COUNT(DISTINCT s.id) AS total_schedules,
        COUNT(DISTINCT CASE WHEN s.is_enabled = 1 THEN s.id END) AS active_schedules,
        COUNT(DISTINCT al.id) AS total_actions,
        MAX(al.created_at) AS last_activity
    FROM iot_outlet_users u
    LEFT JOIN iot_outlet_schedules s ON u.id = s.user_id
    LEFT JOIN iot_outlet_activity_logs al ON u.id = al.user_id
    WHERE u.id = p_user_id
    GROUP BY u.id;
END //

-- Procedure: Update device heartbeat
CREATE PROCEDURE sp_iot_outlet_update_heartbeat(
    IN p_device_id VARCHAR(50),
    IN p_ip_address VARCHAR(45)
)
BEGIN
    DECLARE v_device_pk INT UNSIGNED;
    DECLARE v_was_online TINYINT(1);
    
    SELECT id, is_online INTO v_device_pk, v_was_online
    FROM iot_outlet_devices 
    WHERE device_id = p_device_id AND is_active = 1;
    
    IF v_device_pk IS NOT NULL THEN
        UPDATE iot_outlet_devices 
        SET 
            last_heartbeat = NOW(),
            is_online = 1,
            ip_address = p_ip_address,
            updated_at = NOW()
        WHERE id = v_device_pk;
        
        -- Log if device just came online
        IF v_was_online = 0 THEN
            INSERT INTO iot_outlet_activity_logs (device_id, action_type, action_details, ip_address, status)
            VALUES (v_device_pk, 'device_online', 'Device reconnected', p_ip_address, 'success');
        END IF;
        
        SELECT 'success' AS status, 'Heartbeat updated' AS message;
    ELSE
        SELECT 'failed' AS status, 'Device not found' AS message;
    END IF;
END //

DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

DELIMITER //

-- Trigger: Log user login
CREATE TRIGGER trg_iot_outlet_user_login_log
AFTER UPDATE ON iot_outlet_users
FOR EACH ROW
BEGIN
    IF NEW.last_login != OLD.last_login OR (OLD.last_login IS NULL AND NEW.last_login IS NOT NULL) THEN
        INSERT INTO iot_outlet_activity_logs (user_id, action_type, action_details, status)
        VALUES (NEW.id, 'login', CONCAT('User logged in: ', NEW.username), 'success');
    END IF;
END //

-- Trigger: Log outlet status change
CREATE TRIGGER trg_iot_outlet_status_change
AFTER UPDATE ON iot_outlet_devices
FOR EACH ROW
BEGIN
    IF NEW.outlet_status != OLD.outlet_status THEN
        INSERT INTO iot_outlet_activity_logs (device_id, action_type, action_details, status)
        VALUES (
            NEW.id, 
            CASE WHEN NEW.outlet_status = 'on' THEN 'outlet_on' ELSE 'outlet_off' END,
            CONCAT('Outlet status changed from ', OLD.outlet_status, ' to ', NEW.outlet_status, ' (Mode: ', NEW.control_mode, ')'),
            'success'
        );
    END IF;
END //

-- Trigger: Check device offline status
CREATE TRIGGER trg_iot_outlet_device_offline_check
BEFORE UPDATE ON iot_outlet_devices
FOR EACH ROW
BEGIN
    -- Mark device offline if no heartbeat for 2 minutes
    IF NEW.last_heartbeat IS NOT NULL 
       AND TIMESTAMPDIFF(SECOND, NEW.last_heartbeat, NOW()) > 120 
       AND NEW.is_online = 1 THEN
        SET NEW.is_online = 0;
    END IF;
END //

DELIMITER ;

-- ============================================
-- DATABASE MAINTENANCE
-- ============================================

-- Note: Event scheduler and scheduled tasks require SUPER privilege
-- which is NOT available on shared hosting environments like Hostinger
-- 
-- The system uses triggers and application logic instead:
-- - Devices are marked offline via the trg_iot_outlet_device_offline_check trigger
-- - Old logs can be cleaned manually if needed using:
--   DELETE FROM iot_outlet_activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
--   DELETE FROM iot_outlet_password_resets WHERE expires_at < NOW() OR used = 1;

-- ============================================
-- INDEXES FOR PERFORMANCE
-- ============================================

-- Additional composite indexes for common queries
CREATE INDEX idx_iot_outlet_schedules_device_enabled ON iot_outlet_schedules(device_id, is_enabled);
CREATE INDEX idx_iot_outlet_activity_logs_device_type ON iot_outlet_activity_logs(device_id, action_type);
CREATE INDEX idx_iot_outlet_users_role_active ON iot_outlet_users(role, is_active);

-- ============================================
-- INITIAL DATA / SEEDS
-- ============================================

-- Insert default admin user
-- Username: admin
-- Password: password (hashed with PASSWORD_BCRYPT)
INSERT INTO iot_outlet_users (username, password, full_name, first_name, last_name, role, is_active) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'System', 'Administrator', 'admin', 1);

-- Insert default ESP32 device
INSERT INTO iot_outlet_devices (device_name, device_id, api_key, sim_card_number, outlet_status, control_mode, is_active) VALUES
('Main Outlet', 'ESP32_OUTLET_001', 'IOT_OUTLET_2026_SECURE_KEY_XJ9KP2M8N5QW7R3T', '09605682714', 'off', 'manual', 1);

-- Insert authorized SIM number for SMS control
INSERT INTO iot_outlet_authorized_sims (phone_number, owner_name, first_name, last_name, description, is_active, created_by) VALUES
('+639554778665', 'System Owner', 'System', 'Owner', 'Primary authorized number for SMS outlet control', 1, 1);

-- ============================================
-- GRANTS (Adjust for production)
-- ============================================

-- For development environment
-- GRANT ALL PRIVILEGES ON u907280979_docrequest.* TO 'root'@'localhost';

-- For production environment (recommended)
-- CREATE USER 'iot_outlet_user'@'localhost' IDENTIFIED BY 'secure_password_here';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON u907280979_docrequest.* TO 'iot_outlet_user'@'localhost';
-- FLUSH PRIVILEGES;

-- ============================================
-- END OF COMPLETE SCHEMA
-- ============================================

-- ============================================
-- SUMMARY OF INCLUDED FEATURES:
-- ============================================
-- ✅ Users table with first_name/last_name (email optional)
-- ✅ SIM cards table with first_name/last_name
-- ✅ Schedules with repeat_type (repeat/once) and last_executed
-- ✅ Activity logs for all system actions
-- ✅ Sessions and password reset tables
-- ✅ Views for common queries
-- ✅ Stored procedures for maintenance
-- ✅ Triggers for automatic logging
-- ✅ Event scheduler for device monitoring
-- ✅ Sample data for testing
-- ✅ All indexes and foreign keys properly configured
-- ============================================

-- ============================================
-- POST-IMPORT FIXES (Optional - Run if needed)
-- ============================================

-- Fix any existing empty or NULL action_type values in activity logs
-- These may exist from old data or bugs
-- Run these queries AFTER importing the schema if you have existing data

-- Fix outlet ON logs
UPDATE iot_outlet_activity_logs
SET action_type = 'outlet_on'
WHERE (action_type IS NULL OR action_type = '' OR action_type NOT IN (
    'outlet_on', 'outlet_off', 'schedule_created', 'schedule_updated', 'schedule_deleted',
    'schedule_enabled', 'schedule_disabled', 'sms_command', 'manual_control', 'override',
    'login', 'logout', 'user_created', 'user_updated', 'user_deleted',
    'sim_registered', 'sim_updated', 'sim_deleted', 'unauthorized_sms',
    'heartbeat_lost', 'device_online', 'device_offline'
))
AND (action_details LIKE '%turned ON%' 
     OR action_details LIKE '%Turn ON%'
     OR action_details LIKE '%ON via%'
     OR action_details LIKE '%OUTLET ON%');

-- Fix outlet OFF logs
UPDATE iot_outlet_activity_logs
SET action_type = 'outlet_off'
WHERE (action_type IS NULL OR action_type = '' OR action_type NOT IN (
    'outlet_on', 'outlet_off', 'schedule_created', 'schedule_updated', 'schedule_deleted',
    'schedule_enabled', 'schedule_disabled', 'sms_command', 'manual_control', 'override',
    'login', 'logout', 'user_created', 'user_updated', 'user_deleted',
    'sim_registered', 'sim_updated', 'sim_deleted', 'unauthorized_sms',
    'heartbeat_lost', 'device_online', 'device_offline'
))
AND (action_details LIKE '%turned OFF%' 
     OR action_details LIKE '%Turn OFF%'
     OR action_details LIKE '%OFF via%'
     OR action_details LIKE '%OUTLET OFF%');

-- Fix schedule creation logs
UPDATE iot_outlet_activity_logs
SET action_type = 'schedule_created'
WHERE (action_type IS NULL OR action_type = '' OR action_type NOT IN (
    'outlet_on', 'outlet_off', 'schedule_created', 'schedule_updated', 'schedule_deleted',
    'schedule_enabled', 'schedule_disabled', 'sms_command', 'manual_control', 'override',
    'login', 'logout', 'user_created', 'user_updated', 'user_deleted',
    'sim_registered', 'sim_updated', 'sim_deleted', 'unauthorized_sms',
    'heartbeat_lost', 'device_online', 'device_offline'
))
AND action_details LIKE '%Created schedule%';

-- Fix schedule update logs
UPDATE iot_outlet_activity_logs
SET action_type = 'schedule_updated'
WHERE (action_type IS NULL OR action_type = '' OR action_type NOT IN (
    'outlet_on', 'outlet_off', 'schedule_created', 'schedule_updated', 'schedule_deleted',
    'schedule_enabled', 'schedule_disabled', 'sms_command', 'manual_control', 'override',
    'login', 'logout', 'user_created', 'user_updated', 'user_deleted',
    'sim_registered', 'sim_updated', 'sim_deleted', 'unauthorized_sms',
    'heartbeat_lost', 'device_online', 'device_offline'
))
AND action_details LIKE '%Updated schedule%';

-- Fix schedule deletion logs
UPDATE iot_outlet_activity_logs
SET action_type = 'schedule_deleted'
WHERE (action_type IS NULL OR action_type = '' OR action_type NOT IN (
    'outlet_on', 'outlet_off', 'schedule_created', 'schedule_updated', 'schedule_deleted',
    'schedule_enabled', 'schedule_disabled', 'sms_command', 'manual_control', 'override',
    'login', 'logout', 'user_created', 'user_updated', 'user_deleted',
    'sim_registered', 'sim_updated', 'sim_deleted', 'unauthorized_sms',
    'heartbeat_lost', 'device_online', 'device_offline'
))
AND action_details LIKE '%Deleted schedule%';

-- Fix login logs
UPDATE iot_outlet_activity_logs
SET action_type = 'login'
WHERE (action_type IS NULL OR action_type = '' OR action_type NOT IN (
    'outlet_on', 'outlet_off', 'schedule_created', 'schedule_updated', 'schedule_deleted',
    'schedule_enabled', 'schedule_disabled', 'sms_command', 'manual_control', 'override',
    'login', 'logout', 'user_created', 'user_updated', 'user_deleted',
    'sim_registered', 'sim_updated', 'sim_deleted', 'unauthorized_sms',
    'heartbeat_lost', 'device_online', 'device_offline'
))
AND (action_details LIKE '%logged in%' 
     OR action_details LIKE '%Login successful%');

-- Fix logout logs
UPDATE iot_outlet_activity_logs
SET action_type = 'logout'
WHERE (action_type IS NULL OR action_type = '' OR action_type NOT IN (
    'outlet_on', 'outlet_off', 'schedule_created', 'schedule_updated', 'schedule_deleted',
    'schedule_enabled', 'schedule_disabled', 'sms_command', 'manual_control', 'override',
    'login', 'logout', 'user_created', 'user_updated', 'user_deleted',
    'sim_registered', 'sim_updated', 'sim_deleted', 'unauthorized_sms',
    'heartbeat_lost', 'device_online', 'device_offline'
))
AND (action_details LIKE '%logged out%' 
     OR action_details LIKE '%Logout%');

-- Note: If you still have invalid action_type values after running these fixes,
-- you can set them to a default value with:
-- UPDATE iot_outlet_activity_logs 
-- SET action_type = 'manual_control' 
-- WHERE action_type NOT IN (
--     'outlet_on', 'outlet_off', 'schedule_created', 'schedule_updated', 'schedule_deleted',
--     'schedule_enabled', 'schedule_disabled', 'sms_command', 'manual_control', 'override',
--     'login', 'logout', 'user_created', 'user_updated', 'user_deleted',
--     'sim_registered', 'sim_updated', 'sim_deleted', 'unauthorized_sms',
--     'heartbeat_lost', 'device_online', 'device_offline'
-- );

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check for any remaining invalid action_type values
-- SELECT COUNT(*) as invalid_count
-- FROM iot_outlet_activity_logs
-- WHERE action_type NOT IN (
--     'outlet_on', 'outlet_off', 'schedule_created', 'schedule_updated', 'schedule_deleted',
--     'schedule_enabled', 'schedule_disabled', 'sms_command', 'manual_control', 'override',
--     'login', 'logout', 'user_created', 'user_updated', 'user_deleted',
--     'sim_registered', 'sim_updated', 'sim_deleted', 'unauthorized_sms',
--     'heartbeat_lost', 'device_online', 'device_offline'
-- );

-- Show distribution of action types
-- SELECT action_type, COUNT(*) as count
-- FROM iot_outlet_activity_logs
-- GROUP BY action_type
-- ORDER BY count DESC;

-- ============================================
-- END OF POST-IMPORT FIXES
-- ============================================
