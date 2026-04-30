<?php
$pageTitle = "Export Data";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/topbar.php';

// Preview data with optional filters
$status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$from   = isset($_GET['from'])   ? $conn->real_escape_string($_GET['from'])   : '';
$to     = isset($_GET['to'])     ? $conn->real_escape_string($_GET['to'])     : '';

$where = "WHERE 1=1";
if ($status) $where .= " AND status='$status'";
if ($from)   $where .= " AND DATE(created_at) >= '$from'";
if ($to)     $where .= " AND DATE(created_at) <= '$to'";

$count  = (int) $conn->query("SELECT COUNT(*) FROM leads $where")->fetch_row()[0];
$sample = $conn->query("SELECT * FROM leads $where ORDER BY created_at DESC LIMIT 10");
?>

<div class="content-wrapper">

    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Export Lead Data</h1>
            <p class="page-subtitle">Filter and download leads as a CSV spreadsheet.</p>
        </div>
    </div>

    <!-- ── Filter & Export ── -->
    <div class="card" style="margin-bottom:24px;">
        <h2 class="card-title" style="margin-bottom:20px;">Export Options</h2>
        <form method="GET" class="d-flex flex-wrap" style="gap:16px;align-items:flex-end;">
            <div class="form-group" style="margin:0;min-width:160px;">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <?php foreach(['New','Contacted','Booked','Completed'] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $status===$s?'selected':''; ?>><?php echo $s; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin:0;min-width:150px;">
                <label>From Date</label>
                <input type="date" name="from" class="form-control" value="<?php echo htmlspecialchars($from); ?>">
            </div>
            <div class="form-group" style="margin:0;min-width:150px;">
                <label>To Date</label>
                <input type="date" name="to" class="form-control" value="<?php echo htmlspecialchars($to); ?>">
            </div>
            <div class="form-group" style="margin:0;">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-secondary" style="width:auto;">
                    <i class="fas fa-eye"></i> Preview
                </button>
            </div>
            <div class="form-group" style="margin:0;">
                <label>&nbsp;</label>
                <!-- Export actually downloads -->
                <a href="do_export.php?status=<?php echo urlencode($status); ?>&from=<?php echo urlencode($from); ?>&to=<?php echo urlencode($to); ?>"
                   class="btn btn-success" style="width:auto;">
                    <i class="fas fa-file-arrow-down"></i> Download CSV (<?php echo $count; ?> records)
                </a>
            </div>
        </form>
    </div>

    <!-- ── Preview Table ── -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Preview (first 10 rows)</h2>
            <span class="badge badge-new"><?php echo $count; ?> total</span>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Name</th><th>Phone</th><th>Email</th>
                        <th>Treatment</th><th>Status</th><th>Source</th><th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sample && $sample->num_rows > 0): ?>
                        <?php while ($r = $sample->fetch_assoc()): ?>
                        <tr>
                            <td class="text-muted"><?php echo $r['id']; ?></td>
                            <td class="fw-700"><?php echo htmlspecialchars($r['name']); ?></td>
                            <td><?php echo htmlspecialchars($r['phone']); ?></td>
                            <td class="text-muted"><?php echo htmlspecialchars($r['email']); ?></td>
                            <td><?php echo htmlspecialchars($r['treatment_interest']); ?></td>
                            <td><span class="badge badge-<?php echo strtolower($r['status']); ?>"><?php echo $r['status']; ?></span></td>
                            <td><?php echo htmlspecialchars($r['source']); ?></td>
                            <td class="text-muted"><?php echo date('M j, Y', strtotime($r['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <tr><td colspan="8"><div class="empty-state"><i class="fas fa-database"></i><p>No records match the selected filters.</p></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
