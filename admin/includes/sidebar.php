<?php
$cur = basename($_SERVER['PHP_SELF']);

// Get new leads count for badge (db connection already open via header.php -> db.php)
$new_leads_res   = $conn->query("SELECT COUNT(*) FROM leads WHERE status='New'");
$new_leads_count = $new_leads_res ? (int)$new_leads_res->fetch_row()[0] : 0;
?>
<aside class="sidebar" id="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
        <div class="logo-icon"><i class="fas fa-tooth"></i></div>
        <div class="logo-text">
            <strong>Vijaya Dental</strong>
            <small>Admin Panel v2.0</small>
        </div>
    </div>

    <!-- MAIN -->
    <nav class="nav-group">
        <div class="nav-label">Main</div>

        <a href="index.php" class="nav-item <?php echo $cur==='index.php'?'active':''; ?>">
            <i class="fas fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>

        <a href="analytics.php" class="nav-item <?php echo $cur==='analytics.php'?'active':''; ?>">
            <i class="fas fa-chart-line"></i>
            <span>Analytics</span>
        </a>

        <a href="calendar.php" class="nav-item <?php echo $cur==='calendar.php'?'active':''; ?>">
            <i class="fas fa-calendar-days"></i>
            <span>Calendar</span>
        </a>
    </nav>

    <div class="sidebar-divider"></div>

    <!-- LEAD MANAGEMENT -->
    <nav class="nav-group">
        <div class="nav-label">Lead Management</div>

        <a href="leads.php" class="nav-item <?php echo $cur==='leads.php'?'active':''; ?>">
            <i class="fas fa-users"></i>
            <span>All Leads</span>
            <?php if($new_leads_count > 0): ?>
                <span class="nav-badge"><?php echo $new_leads_count; ?></span>
            <?php endif; ?>
        </a>
    </nav>

    <div class="sidebar-divider"></div>

    <!-- REPORTS -->
    <nav class="nav-group">
        <div class="nav-label">Reports</div>

        <a href="export.php" class="nav-item <?php echo $cur==='export.php'?'active':''; ?>">
            <i class="fas fa-file-arrow-down"></i>
            <span>Export Data</span>
        </a>

        <a href="activity_logs.php" class="nav-item <?php echo $cur==='activity_logs.php'?'active':''; ?>">
            <i class="fas fa-shield-halved"></i>
            <span>Security Logs</span>
        </a>
    </nav>

    <div class="sidebar-divider"></div>

    <!-- ACCOUNT -->
    <nav class="nav-group">
        <div class="nav-label">Account</div>

        <a href="settings.php" class="nav-item <?php echo $cur==='settings.php'?'active':''; ?>">
            <i class="fas fa-gear"></i>
            <span>Settings</span>
        </a>

        <a href="logout.php" class="nav-item nav-logout">
            <i class="fas fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </nav>

</aside>
