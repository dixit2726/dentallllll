# 🦷 Vijaya Dental Admin Panel (v2.0)

A premium, medical-themed admin dashboard for Vijaya Dental Clinic.

---

## 🚀 Quick Setup

### 1. Database Setup
1. Open **phpMyAdmin** or MySQL CLI
2. Import `admin/database.sql`
3. This creates the `vijaya_dental` database with sample data

### 2. Configure Database Connection
Edit `admin/includes/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Your MySQL username
define('DB_PASS', '');          // Your MySQL password
define('DB_NAME', 'vijaya_dental');
```

### 3. Access the Panel
- Place the `admin/` folder inside your web root (e.g. `htdocs/vijaya-1-/admin/`)
- Visit: `http://localhost/vijaya-1-/admin/clinic-login-2026.php`
- **Login:** `admin` / `admin123`

> ⚠️ **Change the default password immediately** after first login via Settings → Change Password.

---

## 📁 File Structure

```
admin/
├── .htaccess              ← Apache security rules
├── database.sql           ← Full schema + sample data
├── clinic-login-2026.php  ← Login page
├── index.php              ← Dashboard
├── analytics.php          ← Charts & analytics
├── calendar.php           ← Appointment calendar
├── leads.php              ← Lead management table
├── update_status.php      ← AJAX status/delete handler
├── save_appointment.php   ← Appointment save handler
├── export.php             ← Export UI with preview
├── do_export.php          ← CSV download handler
├── activity_logs.php      ← Security/audit logs
├── settings.php           ← Account & password settings
├── logout.php             ← Session termination
├── assets/
│   └── css/style.css      ← Full design system CSS
└── includes/
    ├── auth.php           ← Session helpers
    ├── db.php             ← MySQL connection
    ├── header.php         ← HTML head + page loader
    ├── sidebar.php        ← Navigation sidebar
    ├── topbar.php         ← Top navigation bar
    └── footer.php         ← Closing tags + global JS
```

---

## ✨ Features

| Feature | Details |
|---|---|
| 🔐 Secure Login | Session-based auth with bcrypt passwords |
| 📊 Dashboard | Live stats, trend chart, recent activity |
| 📈 Analytics | Doughnut, bar, and funnel charts (Chart.js) |
| 📅 Calendar | Monthly view with colour-coded appointments |
| 👥 Leads | Search, filter, sort + instant AJAX status update |
| 📑 Export | CSV with date/status filters + UTF-8 BOM |
| 🛡️ Logs | Paginated audit trail with action icons |
| ⚙️ Settings | Password change with strength meter |
| 📱 Responsive | Mobile-friendly sidebar with overlay |

---

## 🎨 Design System

- **Primary:** `#2563eb` (Blue)
- **Secondary:** `#10b981` (Green)
- **Accent:** `#f59e0b` (Amber)
- **Font:** Inter (Google Fonts)
- **Icons:** Font Awesome 6

---

## 🔒 Security Notes

- All user inputs are escaped with `real_escape_string()` or prepared statements
- Session checks on every protected page
- `.htaccess` blocks access to `includes/` directory
- Sensitive files (`.sql`, `.env`) blocked via Apache rules
- Security headers added (X-Frame-Options, CSP-ready, etc.)
