<?php
$pageTitle = "Analytics";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/topbar.php';

// ── Treatment distribution ────────────────────────────
$t_res = $conn->query("SELECT treatment_interest, COUNT(*) as cnt FROM leads
                        WHERE treatment_interest != '' AND treatment_interest IS NOT NULL
                        GROUP BY treatment_interest ORDER BY cnt DESC LIMIT 8");
$treatments = [];
$t_counts   = [];
while ($row = $t_res->fetch_assoc()) {
    $treatments[] = $row['treatment_interest'];
    $t_counts[]   = (int)$row['cnt'];
}

// ── Status funnel ─────────────────────────────────────
$funnel_data = [];
foreach (['New','Contacted','Booked','Completed'] as $s) {
    $funnel_data[] = (int) $conn->query("SELECT COUNT(*) FROM leads WHERE status='$s'")->fetch_row()[0];
}

// ── Monthly for the current year ─────────────────────
$monthly_labels = [];
$monthly_vals   = [];
$cur_year = date('Y');
for ($m = 1; $m <= 12; $m++) {
    $monthly_labels[] = date('M', mktime(0,0,0,$m,1));
    $monthly_vals[]   = (int) $conn->query("SELECT COUNT(*) FROM leads WHERE MONTH(created_at)=$m AND YEAR(created_at)=$cur_year")->fetch_row()[0];
}

// ── Summary stats ────────────────────────────────────
$total        = (int) $conn->query("SELECT COUNT(*) FROM leads")->fetch_row()[0];
$booked       = (int) $conn->query("SELECT COUNT(*) FROM leads WHERE status='Booked'")->fetch_row()[0];
$completed    = (int) $conn->query("SELECT COUNT(*) FROM leads WHERE status='Completed'")->fetch_row()[0];
$conv_rate    = $total > 0 ? round(($booked + $completed) / $total * 100, 1) : 0;
?>

<div class="content-wrapper">

    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Performance Analytics</h1>
            <p class="page-subtitle">Patient enquiry trends, conversion rates, and treatment insights.</p>
        </div>
    </div>

    <!-- ── Summary Stats ── -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-blue"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $total; ?></div>
                <div class="stat-label">Total Leads</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-green"><i class="fas fa-percent"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $conv_rate; ?>%</div>
                <div class="stat-label">Conversion Rate</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-purple"><i class="fas fa-star"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $completed; ?></div>
                <div class="stat-label">Completed Treatments</div>
            </div>
        </div>
    </div>

    <!-- ── Doughnut + Funnel ── -->
    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-pie-chart" style="color:var(--accent);margin-right:8px;"></i>Treatment Interest Distribution</h2>
            </div>
            <div style="max-height:320px;display:flex;justify-content:center;padding:12px 0;">
                <canvas id="treatmentChart"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-filter" style="color:var(--secondary);margin-right:8px;"></i>Lead Conversion Funnel</h2>
            </div>
            <canvas id="funnelChart" height="280"></canvas>
        </div>
    </div>

    <!-- ── Monthly Bar Chart ── -->
    <div class="card mt-24">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-chart-bar" style="color:var(--primary);margin-right:8px;"></i>Monthly Enquiries — <?php echo $cur_year; ?></h2>
            <a href="export.php" class="btn btn-success btn-sm"><i class="fas fa-file-arrow-down"></i> Export</a>
        </div>
        <canvas id="monthlyChart" height="110"></canvas>
    </div>

</div>

<script>
const COLORS = ['#2563eb','#10b981','#f59e0b','#8b5cf6','#ec4899','#06b6d4','#f43f5e','#84cc16'];

// Treatment Doughnut
new Chart(document.getElementById('treatmentChart'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($treatments ?: ['No data']); ?>,
        datasets: [{
            data: <?php echo json_encode($t_counts ?: [1]); ?>,
            backgroundColor: COLORS,
            borderWidth: 2,
            borderColor: '#fff',
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        cutout: '62%',
        plugins: {
            legend: { position: 'right' }
        }
    }
});

// Funnel Bar (horizontal)
new Chart(document.getElementById('funnelChart'), {
    type: 'bar',
    data: {
        labels: ['New','Contacted','Booked','Completed'],
        datasets: [{
            label: 'Leads',
            data: <?php echo json_encode($funnel_data); ?>,
            backgroundColor: ['#dbeafe','#fef9c3','#dcfce7','#f3e8ff'],
            borderColor:     ['#2563eb','#d97706','#10b981','#8b5cf6'],
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Monthly Bar
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($monthly_labels); ?>,
        datasets: [{
            label: 'Enquiries',
            data: <?php echo json_encode($monthly_vals); ?>,
            backgroundColor: 'rgba(37,99,235,0.15)',
            borderColor: '#2563eb',
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
