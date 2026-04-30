<?php
$pageTitle = "Leads Management";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/topbar.php';

// ── Filters ────────────────────────────────────────────────────────────────
$search  = isset($_GET['search'])  ? trim($conn->real_escape_string($_GET['search']))  : '';
$status  = isset($_GET['status'])  ? trim($conn->real_escape_string($_GET['status']))  : '';
$sort    = isset($_GET['sort'])    ? $conn->real_escape_string($_GET['sort'])           : 'created_at';
$order   = (isset($_GET['order']) && $_GET['order']==='asc') ? 'ASC' : 'DESC';

$allowed_sorts = ['id','name','treatment_interest','status','created_at'];
if (!in_array($sort, $allowed_sorts)) $sort = 'created_at';

$where  = "WHERE 1=1";
if ($search) $where .= " AND (name LIKE '%$search%' OR phone LIKE '%$search%' OR treatment_interest LIKE '%$search%' OR email LIKE '%$search%')";
if ($status) $where .= " AND status='$status'";

$total_count = (int) $conn->query("SELECT COUNT(*) FROM leads $where")->fetch_row()[0];
$leads_res   = $conn->query("SELECT * FROM leads $where ORDER BY $sort $order");

// Column sort helper
function sortLink($col, $label, $cur_sort, $cur_order) {
    $next = ($cur_sort===$col && $cur_order==='DESC') ? 'asc' : 'desc';
    $icon = $cur_sort===$col ? ($cur_order==='DESC' ? ' ↓' : ' ↑') : '';
    $qs = http_build_query(array_merge($_GET, ['sort'=>$col,'order'=>$next]));
    return "<a href='leads.php?$qs' style='color:inherit;text-decoration:none;'>$label<span style='color:var(--primary);'>$icon</span></a>";
}
?>

<div class="content-wrapper">

    <!-- Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Leads Management</h1>
            <p class="page-subtitle"><?php echo $total_count; ?> records found</p>
        </div>
        <div class="d-flex ai-center gap-8">
            <a href="export.php<?php echo $status ? '?status='.$status : ''; ?>" class="btn btn-success">
                <i class="fas fa-file-arrow-down"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- ── Filter Bar ── -->
    <div class="card" style="margin-bottom:20px;padding:18px;">
        <form method="GET" class="d-flex ai-center flex-wrap gap-8" style="gap:12px;">
            <div class="topbar-search" style="flex:1;min-width:260px;">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" name="search" class="form-control" style="border-radius:8px;"
                       value="<?php echo htmlspecialchars($search); ?>"
                       placeholder="Search name, phone, email, treatment…">
            </div>

            <select name="status" class="form-control" style="width:160px;" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php foreach(['New','Contacted','Booked','Completed'] as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo $status===$s?'selected':''; ?>><?php echo $s; ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>

            <?php if($search||$status): ?>
                <a href="leads.php" class="btn btn-danger btn-sm"><i class="fas fa-xmark"></i> Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- ── Leads Table ── -->
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px;"><?php echo sortLink('id','#',$sort,$order); ?></th>
                        <th><?php echo sortLink('name','Patient',$sort,$order); ?></th>
                        <th>Contact</th>
                        <th><?php echo sortLink('treatment_interest','Treatment',$sort,$order); ?></th>
                        <th><?php echo sortLink('status','Status',$sort,$order); ?></th>
                        <th><?php echo sortLink('created_at','Date',$sort,$order); ?></th>
                        <th style="width:80px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($leads_res && $leads_res->num_rows > 0): ?>
                        <?php while ($lead = $leads_res->fetch_assoc()): ?>
                        <tr id="row-<?php echo $lead['id']; ?>">
                            <td class="text-muted"><?php echo $lead['id']; ?></td>
                            <td>
                                <div class="fw-700"><?php echo htmlspecialchars($lead['name']); ?></div>
                                <?php if($lead['source']): ?>
                                    <div class="text-muted text-sm"><?php echo htmlspecialchars($lead['source']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($lead['phone']); ?></div>
                                <div class="text-muted text-sm"><?php echo htmlspecialchars($lead['email']); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($lead['treatment_interest'] ?: '—'); ?></td>
                            <td>
                                <select class="badge badge-<?php echo strtolower($lead['status']); ?>"
                                        id="status-<?php echo $lead['id']; ?>"
                                        onchange="updateStatus(<?php echo $lead['id']; ?>, this.value)"
                                        style="border:none;outline:none;cursor:pointer;font-family:inherit;font-weight:700;font-size:0.7rem;appearance:none;-webkit-appearance:none;">
                                    <?php foreach(['New','Contacted','Booked','Completed'] as $s): ?>
                                        <option value="<?php echo $s; ?>" <?php echo $lead['status']===$s?'selected':''; ?>><?php echo $s; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="text-muted"><?php echo date('M j, Y', strtotime($lead['created_at'])); ?></td>
                            <td>
                                <button onclick="deleteLead(<?php echo $lead['id']; ?>)" class="btn btn-danger btn-sm" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <p>No leads match your current filters.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div><!-- /.content-wrapper -->

<script>
function updateStatus(id, newStatus) {
    const sel = document.getElementById('status-' + id);
    const formData = new FormData();
    formData.append('id', id);
    formData.append('status', newStatus);

    fetch('update_status.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update badge colour
            sel.className = 'badge badge-' + newStatus.toLowerCase();
            sel.style.cssText = 'border:none;outline:none;cursor:pointer;font-family:inherit;font-weight:700;font-size:0.7rem;appearance:none;-webkit-appearance:none;';
            showToast('Status updated to ' + newStatus, 'success');
        } else {
            showToast('Failed: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(() => showToast('Network error. Try again.', 'error'));
}

function deleteLead(id) {
    if (!confirm('Delete lead #' + id + '? This cannot be undone.')) return;
    const formData = new FormData();
    formData.append('id', id);
    formData.append('action', 'delete');

    fetch('update_status.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById('row-' + id);
            row.style.opacity = '0';
            row.style.transition = 'opacity 0.3s';
            setTimeout(() => row.remove(), 300);
            showToast('Lead deleted.', 'success');
        } else {
            showToast('Delete failed.', 'error');
        }
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
