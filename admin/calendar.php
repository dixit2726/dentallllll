<?php
$pageTitle = "Calendar";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/topbar.php';

$month = isset($_GET['month']) ? max(1, min(12, (int)$_GET['month'])) : (int)date('m');
$year  = isset($_GET['year'])  ? (int)$_GET['year'] : (int)date('Y');

$first_ts     = mktime(0,0,0,$month,1,$year);
$days_in_month = (int)date('t', $first_ts);
$start_weekday = (int)date('w', $first_ts);   // 0 = Sun
$month_name   = date('F', $first_ts);
$today_day    = (date('m')==$month && date('Y')==$year) ? (int)date('j') : -1;

// Prev / Next
$prev_ts = strtotime('-1 month', $first_ts);
$next_ts = strtotime('+1 month', $first_ts);
$prev = ['m'=>date('m',$prev_ts), 'y'=>date('Y',$prev_ts)];
$next = ['m'=>date('m',$next_ts), 'y'=>date('Y',$next_ts)];

// Load appointments for this month
$apps_res = $conn->query("SELECT * FROM appointments
                           WHERE MONTH(appointment_date)=$month AND YEAR(appointment_date)=$year
                           ORDER BY appointment_time ASC");
$app_map  = [];
while ($a = $apps_res->fetch_assoc()) {
    $d = (int)date('j', strtotime($a['appointment_date']));
    $app_map[$d][] = $a;
}
?>

<style>
.cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    border-left: 1px solid var(--border);
    border-top:  1px solid var(--border);
}
.cal-head {
    background: var(--bg-main);
    padding: 10px;
    text-align: center;
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
}
.cal-cell {
    min-height: 110px;
    padding: 8px;
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    background: #fff;
    vertical-align: top;
    transition: background .15s;
}
.cal-cell:hover { background: #fafbff; }
.cal-cell.empty-cell { background: var(--bg-main); }
.cal-cell.today-cell { background: var(--primary-light); }
.day-num {
    font-weight: 700;
    font-size: 0.85rem;
    margin-bottom: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px; height: 26px;
    border-radius: 50%;
}
.cal-cell.today-cell .day-num {
    background: var(--primary);
    color: #fff;
}
.app-tag {
    display: block;
    font-size: 0.68rem;
    font-weight: 600;
    padding: 3px 7px;
    border-radius: 5px;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: pointer;
    color: #fff;
}
.app-tag.scheduled { background: var(--primary); }
.app-tag.completed { background: var(--secondary); }
.app-tag.cancelled { background: var(--danger); }
</style>

<div class="content-wrapper">

    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Appointment Calendar</h1>
            <p class="page-subtitle">Manage and view all scheduled appointments.</p>
        </div>
        <div class="d-flex ai-center gap-8">
            <a href="?month=<?php echo $prev['m']; ?>&year=<?php echo $prev['y']; ?>" class="btn btn-secondary">
                <i class="fas fa-chevron-left"></i>
            </a>
            <span style="font-size:1.15rem;font-weight:700;min-width:170px;text-align:center;">
                <?php echo $month_name . ' ' . $year; ?>
            </span>
            <a href="?month=<?php echo $next['m']; ?>&year=<?php echo $next['y']; ?>" class="btn btn-secondary">
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="?month=<?php echo date('m'); ?>&year=<?php echo date('Y'); ?>" class="btn btn-secondary" style="margin-left:8px;">Today</a>
            <button onclick="openAddModal()" class="btn btn-primary" style="margin-left:8px;">
                <i class="fas fa-plus"></i> Add Appointment
            </button>
        </div>
    </div>

    <!-- Legend -->
    <div class="d-flex ai-center gap-8" style="margin-bottom:16px;gap:16px;flex-wrap:wrap;">
        <span style="font-size:0.8rem;font-weight:600;color:var(--text-muted);">Legend:</span>
        <span class="app-tag scheduled" style="position:static;width:auto;display:inline-block;opacity:1;"> Scheduled</span>
        <span class="app-tag completed" style="position:static;width:auto;display:inline-block;opacity:1;"> Completed</span>
        <span class="app-tag cancelled" style="position:static;width:auto;display:inline-block;opacity:1;"> Cancelled</span>
    </div>

    <!-- Calendar grid -->
    <div class="card" style="padding:0;overflow:hidden;">
        <div class="cal-grid">
            <?php foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d): ?>
                <div class="cal-head"><?php echo $d; ?></div>
            <?php endforeach; ?>

            <?php
            // Empty cells before 1st
            for ($i=0; $i<$start_weekday; $i++) echo '<div class="cal-cell empty-cell"></div>';

            for ($day=1; $day<=$days_in_month; $day++):
                $cls = ($day===$today_day) ? 'today-cell' : '';
            ?>
                <div class="cal-cell <?php echo $cls; ?>">
                    <span class="day-num"><?php echo $day; ?></span>
                    <?php if (!empty($app_map[$day])): ?>
                        <?php foreach($app_map[$day] as $app): ?>
                            <span class="app-tag <?php echo strtolower($app['status']); ?>"
                                  title="<?php echo htmlspecialchars($app['patient_name']); ?> — <?php echo htmlspecialchars($app['treatment']); ?> @ <?php echo date('H:i', strtotime($app['appointment_time'])); ?>">
                                <?php echo date('H:i', strtotime($app['appointment_time'])); ?>
                                <?php echo htmlspecialchars($app['patient_name']); ?>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>

            <?php
            // Fill trailing empty cells
            $used = $start_weekday + $days_in_month;
            $remaining = (7 - ($used % 7)) % 7;
            for ($i=0; $i<$remaining; $i++) echo '<div class="cal-cell empty-cell"></div>';
            ?>
        </div>
    </div>

</div>

<!-- Add Appointment Modal (minimal) -->
<div id="apptModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:2000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:32px;width:100%;max-width:480px;box-shadow:var(--shadow-lg);">
        <div class="d-flex ai-center jc-between" style="margin-bottom:24px;">
            <h2 style="font-size:1.1rem;font-weight:700;">New Appointment</h2>
            <button onclick="closeModal()" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text-muted);">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="save_appointment.php">
            <div class="form-group">
                <label>Patient Name</label>
                <input type="text" name="patient_name" class="form-control" placeholder="e.g. Ravi Kumar" required>
            </div>
            <div class="form-group">
                <label>Treatment</label>
                <select name="treatment" class="form-control" required>
                    <option value="">Select treatment</option>
                    <?php foreach(['Root Canal','Dental Implants','Teeth Whitening','Braces','Crowns & Bridges','Smile Designing','Invisalign','Flap Surgery','Facial Fracture','Full Mouth Rehabilitation'] as $t): ?>
                        <option><?php echo $t; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="appointment_date" class="form-control"
                           min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Time</label>
                    <input type="time" name="appointment_time" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes…"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:8px;">
                <i class="fas fa-calendar-plus"></i> Save Appointment
            </button>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('apptModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('apptModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.getElementById('apptModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
