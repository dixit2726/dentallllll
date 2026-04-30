<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
checkLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? 'update';
$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success'=>false,'message'=>'Invalid lead ID']);
    exit;
}

// ── Delete ─────────────────────────────────────────────────
if ($action === 'delete') {
    $stmt = $conn->prepare("DELETE FROM leads WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        logActivity($conn, $_SESSION['admin_id'], "Delete Lead", "Deleted lead #$id");
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false,'message'=>'DB error']);
    }
    exit;
}

// ── Status Update ───────────────────────────────────────────
$status        = $conn->real_escape_string($_POST['status'] ?? '');
$valid_statuses = ['New','Contacted','Booked','Completed'];

if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success'=>false,'message'=>'Invalid status value']);
    exit;
}

$stmt = $conn->prepare("UPDATE leads SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    logActivity($conn, $_SESSION['admin_id'], "Status Update", "Lead #$id → $status");
    echo json_encode(['success'=>true, 'status'=>$status]);
} else {
    echo json_encode(['success'=>false,'message'=>'Database error: '.$conn->error]);
}
?>
