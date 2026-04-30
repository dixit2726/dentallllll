<?php
$pageTitle = "Security Logs";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/topbar.php';

$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset   = ($page - 1) * $per_page;

$total_logs = (int) $conn->query("SELECT COUNT(*) FROM activity_logs")->fetch_row()[0];
$total_pages = max(1, ceil($total_logs / $per_page));

$logs_res = $conn->query("SELECT l.*, a.full_name
                           FROM activity_logs l
                           LEFT JOIN admins a ON l.admin_id = a.id
                           ORDER BY l.timestamp DESC
                           LIMIT $per_page OFFSET $offset");

// Action icon map
$action_icons = [
    'Login'        => ['fas fa-right-to-bracket', 'bg-green'],
    'Logout'       => ['fas fa-right-from-bracket', 'bg-red'],
    'Status Update'=> ['fas fa-arrows-rotate', 'bg-blue'],
    'Password Change'=> ['fas fa-lock', 'bg-orange'],
    'Export CSV'   => ['fas fa-file-arrow-down', 'bg-purple'],
    'Export Data'  => ['fas fa-file-arrow-down', 'bg-purple'],
    'Add Appointment'=> ['fas fa-calendar-plus', 'bg-teal'],
    'Delete Lead'  => ['fas fa-trash', 'bg-red'],
];
?>

<div class="content-wrapper">

    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Security & Activity Logs</h1>
            <p class="page-subtitle"><?php echo $total_logs; ?> total audit events recorded.</p>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px;"></th>
                        <th>Timestamp</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($logs_res && $logs_res->num_rows > 0): ?>
                        <?php while ($log = $logs_res->fetch_assoc()):
                            $icons = $action_icons[$log['action']] ?? ['fas fa-circle-dot', 'bg-blue'];
                        ?>
                        <tr>
                            <td>
                                <div class="stat-icon <?php echo $icons[1]; ?>" style="width:32px;height:32px;border-radius:8px;font-size:0.8rem;">
                                    <i class="<?php echo $icons[0]; ?>"></i>
                                </div>
                            </td>
                            <td style="white-space:nowrap;color:var(--text-muted);">
                                <?php echo date('M j, Y', strtotime($log['timestamp'])); ?>
                                <div class="text-sm"><?php echo date('H:i:s', strtotime($log['timestamp'])); ?></div>
                            </td>
                            <td class="fw-600"><?php echo htmlspecialchars($log['full_name'] ?: 'System'); ?></td>
                            <td><span class="badge badge-scheduled"><?php echo htmlspecialchars($log['action']); ?></span></td>
                            <td class="text-muted"><?php echo htmlspecialchars($log['details']); ?></td>
                            <td><code><?php echo htmlspecialchars($log['ip_address']); ?></code></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <tr><td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-shield-halved"></i>
                            <p>No activity logs recorded yet.</p>
                        </div>
                    </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="d-flex ai-center jc-between" style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
            <span class="text-muted text-sm">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
            <div class="d-flex ai-center gap-8" style="gap:8px;">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page-1; ?>" class="btn btn-secondary btn-sm">← Prev</a>
                <?php endif; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page+1; ?>" class="btn btn-secondary btn-sm">Next →</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
