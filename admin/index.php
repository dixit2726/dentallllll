<?php
$pageTitle = "Dashboard";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/topbar.php';

// ── Quick Stats ──────────────────────────────────────
$total_enquiries     = (int) $conn->query("SELECT COUNT(*) FROM leads")->fetch_row()[0];
$new_leads           = (int) $conn->query("SELECT COUNT(*) FROM leads WHERE status='New'")->fetch_row()[0];
$booked_apps         = (int) $conn->query("SELECT COUNT(*) FROM appointments WHERE status='Scheduled'")->fetch_row()[0];
$completed           = (int) $conn->query("SELECT COUNT(*) FROM appointments WHERE status='Completed'")->fetch_row()[0];

// ── Chart data: enquiries per month (last 6 months) ──
$chart_months = [];
$chart_counts = [];
for ($i = 5; $i >= 0; $i--) {
    $ts    = strtotime("-$i months");
    $m     = date('m', $ts);
    $y     = date('Y', $ts);
    $label = date('M', $ts);
    $count = (int) $conn->query("SELECT COUNT(*) FROM leads WHERE MONTH(created_at)=$m AND YEAR(created_at)=$y")->fetch_row()[0];
    $chart_months[] = $label;
    $chart_counts[] = $count;
}

// ── Recent activity (last 5 logs) ──
$logs_res = $conn->query("SELECT l.action, l.details, l.timestamp, a.full_name
                           FROM activity_logs l
                           LEFT JOIN admins a ON l.admin_id = a.id
                           ORDER BY l.timestamp DESC LIMIT 5");

// ── Recent leads (last 5) ──
$leads_res = $conn->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5");
?>

<div class="content-wrapper">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Welcome back, <?php echo htmlspecialchars(explode(' ', $_SESSION['admin_name'])[0]); ?>! 👋</h1>
            <p class="page-subtitle">Here's what's happening at Vijaya Dental Clinic today.</p>
        </div>
        <a href="leads.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Lead</a>
    </div>

    <!-- ── Stat Cards ── -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-blue"><i class="fas fa-envelope-open-text"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $total_enquiries; ?></div>
                <div class="stat-label">Total Enquiries</div>
                <div class="stat-trend"><i class="fas fa-arrow-up"></i> All time</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-orange"><i class="fas fa-bell-concierge"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $new_leads; ?></div>
                <div class="stat-label">New Leads</div>
                <div class="stat-trend" style="color:#f59e0b;"><i class="fas fa-clock"></i> Awaiting contact</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-purple"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $booked_apps; ?></div>
                <div class="stat-label">Booked Appointments</div>
                <div class="stat-trend"><i class="fas fa-calendar"></i> Upcoming</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-green"><i class="fas fa-circle-check"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $completed; ?></div>
                <div class="stat-label">Completed Treatments</div>
                <div class="stat-trend"><i class="fas fa-star"></i> Total</div>
            </div>
        </div>
    </div>

    <!-- ── Charts + Activity ── -->
    <div class="dashboard-grid">

        <!-- Enquiry Trend Line Chart -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-chart-line" style="color:var(--primary);margin-right:8px;"></i>Patient Enquiry Trends</h2>
                <a href="analytics.php" class="btn btn-secondary btn-sm">Full Analytics</a>
            </div>
            <canvas id="enquiryChart" height="210"></canvas>
        </div>

        <!-- Recent Activity Feed -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-bolt" style="color:var(--accent);margin-right:8px;"></i>Recent Activity</h2>
                <a href="activity_logs.php" style="font-size:0.78rem;color:var(--primary);font-weight:600;">View All</a>
            </div>
            <div>
                <?php if ($logs_res && $logs_res->num_rows > 0): ?>
                    <?php while ($log = $logs_res->fetch_assoc()): ?>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div class="activity-content">
                            <div class="activity-text"><?php echo htmlspecialchars($log['action']); ?></div>
                            <div class="activity-time">
                                <?php echo $log['full_name'] ? htmlspecialchars($log['full_name']) . ' · ' : ''; ?>
                                <?php echo date('M j, H:i', strtotime($log['timestamp'])); ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-list-check"></i>
                        <p>No recent activity yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── Recent Leads Table ── -->
    <div class="card mt-24">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-users" style="color:var(--secondary);margin-right:8px;"></i>Recent Leads</h2>
            <a href="leads.php" class="btn btn-primary btn-sm"><i class="fas fa-arrow-right"></i> All Leads</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient</th>
                        <th>Treatment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($leads_res && $leads_res->num_rows > 0): ?>
                        <?php while ($lead = $leads_res->fetch_assoc()): ?>
                        <tr>
                            <td class="text-muted">#<?php echo $lead['id']; ?></td>
                            <td>
                                <div class="fw-700"><?php echo htmlspecialchars($lead['name']); ?></div>
                                <div class="text-muted text-sm"><?php echo htmlspecialchars($lead['phone']); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($lead['treatment_interest'] ?: '—'); ?></td>
                            <td><span class="badge badge-<?php echo strtolower($lead['status']); ?>"><?php echo $lead['status']; ?></span></td>
                            <td class="text-muted"><?php echo date('M j, Y', strtotime($lead['created_at'])); ?></td>
                            <td>
                                <a href="leads.php" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i> View</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No leads recorded yet. <a href="leads.php" style="color:var(--primary);">Add one →</a></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div><!-- /.content-wrapper -->

<script>
// Enquiry Trend Chart
(function(){
    const ctx = document.getElementById('enquiryChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_months); ?>,
            datasets: [{
                label: 'Enquiries',
                data: <?php echo json_encode($chart_counts); ?>,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.08)',
                fill: true,
                tension: 0.45,
                borderWidth: 2.5,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#2563eb',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: {}
            }
        }
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
