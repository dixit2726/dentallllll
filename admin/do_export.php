<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
checkLogin();

$status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$from   = isset($_GET['from'])   ? $conn->real_escape_string($_GET['from'])   : '';
$to     = isset($_GET['to'])     ? $conn->real_escape_string($_GET['to'])     : '';

$where = "WHERE 1=1";
if ($status) $where .= " AND status='$status'";
if ($from)   $where .= " AND DATE(created_at) >= '$from'";
if ($to)     $where .= " AND DATE(created_at) <= '$to'";

$filename = "vijaya_leads_" . date('Y-m-d_His') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);
header('Cache-Control: no-cache, must-revalidate');

$out = fopen('php://output', 'w');
fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel

fputcsv($out, ['ID','Name','Email','Phone','Treatment','Status','Source','Date Received','Message']);

$res = $conn->query("SELECT id, name, email, phone, treatment_interest, status, source, created_at, message FROM leads $where ORDER BY created_at DESC");
while ($row = $res->fetch_assoc()) {
    fputcsv($out, [
        $row['id'],
        $row['name'],
        $row['email'],
        $row['phone'],
        $row['treatment_interest'],
        $row['status'],
        $row['source'],
        date('d/m/Y H:i', strtotime($row['created_at'])),
        $row['message']
    ]);
}

logActivity($conn, $_SESSION['admin_id'], "Export CSV", "Downloaded leads CSV with filters: status=$status, from=$from, to=$to");
fclose($out);
exit;
?>
