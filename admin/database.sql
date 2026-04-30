-- ============================================================
-- Vijaya Dental Admin Panel (v2.0) — Full Database Setup
-- ============================================================
-- Run this file once to create the database, tables, and seed data.
-- Default Admin Login: username=admin  password=admin123
-- ============================================================

CREATE DATABASE IF NOT EXISTS vijaya_dental CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vijaya_dental;

-- ── Admins ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admins (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  UNIQUE NOT NULL,
    password   VARCHAR(255) NOT NULL,
    full_name  VARCHAR(100) NOT NULL,
    email      VARCHAR(100) UNIQUE NOT NULL,
    role       ENUM('super_admin','admin') DEFAULT 'admin',
    last_login DATETIME     DEFAULT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Leads ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS leads (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    name               VARCHAR(100) NOT NULL,
    email              VARCHAR(100) DEFAULT '',
    phone              VARCHAR(20)  NOT NULL,
    treatment_interest VARCHAR(100) DEFAULT '',
    message            TEXT,
    status             ENUM('New','Contacted','Booked','Completed') DEFAULT 'New',
    source             VARCHAR(50)  DEFAULT 'Website',
    created_at         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Appointments ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS appointments (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    patient_id       INT DEFAULT NULL,
    patient_name     VARCHAR(100) NOT NULL,
    treatment        VARCHAR(100) NOT NULL,
    appointment_date DATE        NOT NULL,
    appointment_time TIME        NOT NULL,
    status           ENUM('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled',
    notes            TEXT,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES leads(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Activity Logs ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS activity_logs (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    admin_id   INT DEFAULT NULL,
    action     VARCHAR(100) NOT NULL,
    details    TEXT,
    ip_address VARCHAR(45) DEFAULT NULL,
    timestamp  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Default Admin ────────────────────────────────────────────
-- Password is 'admin123'  (bcrypt hash)
INSERT IGNORE INTO admins (username, password, full_name, email, role)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Clinic Admin',
    'admin@vijayadental.com',
    'super_admin'
);

-- ── Sample Leads ─────────────────────────────────────────────
INSERT IGNORE INTO leads (name, email, phone, treatment_interest, status, source, created_at) VALUES
('Priya Sharma',    'priya@email.com',   '9876543210', 'Root Canal',               'New',       'Website',  NOW() - INTERVAL 1 DAY),
('Ravi Kumar',      'ravi@email.com',    '9123456780', 'Dental Implants',           'Contacted', 'Walk-in',  NOW() - INTERVAL 3 DAY),
('Anita Reddy',     'anita@email.com',   '9988776655', 'Teeth Whitening',           'Booked',    'Referral', NOW() - INTERVAL 5 DAY),
('Mohammed Irfan',  'irfan@email.com',   '9765432100', 'Braces',                   'New',       'Website',  NOW() - INTERVAL 1 DAY),
('Suresh Babu',     'suresh@email.com',  '9800001111', 'Full Mouth Rehabilitation', 'Completed', 'Website',  NOW() - INTERVAL 10 DAY),
('Kavitha Nair',    'kavitha@email.com', '9900111222', 'Smile Designing',           'New',       'Instagram',NOW() - INTERVAL 2 DAY),
('Arjun Menon',     'arjun@email.com',  '9700222333', 'Invisalign',                'Contacted', 'Website',  NOW() - INTERVAL 4 DAY),
('Deepa Pillai',    'deepa@email.com',   '9600333444', 'Crowns & Bridges',          'Booked',    'Google',   NOW() - INTERVAL 6 DAY),
('Vikram Singh',    'vikram@email.com',  '9500444555', 'Flap Surgery',              'New',       'Website',  NOW()),
('Meera Iyer',      'meera@email.com',   '9400555666', 'Root Canal',               'New',       'Referral', NOW() - INTERVAL 1 HOUR);

-- ── Sample Appointments ──────────────────────────────────────
INSERT IGNORE INTO appointments (patient_name, treatment, appointment_date, appointment_time, status, notes) VALUES
('Anita Reddy',  'Teeth Whitening',  CURDATE(),                      '10:00:00', 'Scheduled', 'First session'),
('Deepa Pillai', 'Crowns & Bridges', CURDATE(),                      '11:30:00', 'Scheduled', 'Consultation'),
('Ravi Kumar',   'Dental Implants',  CURDATE() + INTERVAL 2 DAY,    '09:00:00', 'Scheduled', 'Pre-surgery check'),
('Kavitha Nair', 'Smile Designing',  CURDATE() + INTERVAL 3 DAY,    '14:00:00', 'Scheduled', 'Design consultation'),
('Suresh Babu',  'Full Mouth Rehab', CURDATE() - INTERVAL 1 DAY,    '10:00:00', 'Completed', 'Completed successfully');

-- ── Sample Activity Logs ─────────────────────────────────────
INSERT INTO activity_logs (admin_id, action, details, ip_address, timestamp) VALUES
(1, 'Login',   'Admin logged in successfully',            '127.0.0.1', NOW() - INTERVAL 30 MINUTE),
(1, 'Status Update', 'Lead #3 → Booked',                 '127.0.0.1', NOW() - INTERVAL 25 MINUTE),
(1, 'Add Appointment', 'Kavitha Nair - Smile Designing',  '127.0.0.1', NOW() - INTERVAL 20 MINUTE);
