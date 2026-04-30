<header class="topbar">
    <div class="topbar-left">
        <!-- Mobile menu toggle -->
        <div class="mobile-toggle" id="mobileToggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </div>
        <span class="topbar-page-title"><?php echo $pageTitle ?? 'Dashboard'; ?></span>
    </div>

    <div class="topbar-right">

        <!-- Search -->
        <div class="topbar-search">
            <i class="fas fa-magnifying-glass"></i>
            <input type="text" id="topbarSearch" placeholder="Search patients, treatments…">
        </div>

        <!-- Notification bell -->
        <button class="icon-btn" title="Notifications" onclick="window.location='leads.php?status=New'">
            <i class="far fa-bell"></i>
            <?php if($new_leads_count > 0): ?>
                <span class="notification-badge"><?php echo $new_leads_count; ?></span>
            <?php endif; ?>
        </button>

        <!-- Admin profile -->
        <div class="admin-profile" onclick="window.location='settings.php'">
            <div class="admin-info">
                <div class="admin-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
                <div class="admin-role">Administrator</div>
            </div>
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['admin_name']); ?>&background=2563eb&color=fff&size=72&bold=true"
                 alt="Admin avatar"
                 class="admin-avatar">
        </div>

    </div>
</header>

<main class="main-content">
