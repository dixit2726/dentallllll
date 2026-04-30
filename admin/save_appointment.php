<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient = $conn->real_escape_string($_POST['patient_name']);
    $treat   = $conn->real_escape_string($_POST['treatment']);
    $date    = $conn->real_escape_string($_POST['appointment_date']);
    $time    = $conn->real_escape_string($_POST['appointment_time']);
    $notes   = $conn->real_escape_string($_POST['notes'] ?? '');

    // Check for double booking
    $check = $conn->query("SELECT id FROM appointments WHERE appointment_date='$date' AND appointment_time='$time' AND status='Scheduled'");
    if ($check->num_rows > 0) {
        $_SESSION['cal_error'] = "A scheduled appointment already exists at $time on $date.";
    } else {
        $conn->query("INSERT INTO appointments (patient_name, treatment, appointment_date, appointment_time, notes) VALUES ('$patient','$treat','$date','$time','$notes')");
        logActivity($conn, $_SESSION['admin_id'], "Add Appointment", "$patient - $treat on $date at $time");
    }
}

// Redirect back to calendar
$month = isset($_POST['appointment_date']) ? date('m', strtotime($_POST['appointment_date'])) : date('m');
$year  = isset($_POST['appointment_date']) ? date('Y', strtotime($_POST['appointment_date'])) : date('Y');
header("Location: calendar.php?month=$month&year=$year");
exit;
?>
