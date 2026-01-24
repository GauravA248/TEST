<?php
session_start();
require_once __DIR__ . "/../config/db.php";

$userId = $_SESSION['user_id'] ?? 0;
$data = [];

/* === ATTENDANCE DATA === */
$q = $conn->prepare("
    SELECT date, work_hours
    FROM attendance
    WHERE user_id = ?
");
$q->bind_param("i", $userId);
$q->execute();
$res = $q->get_result();

while ($row = $res->fetch_assoc()) {
    if ($row['work_hours'] >= 8) {
        $data[$row['date']] = 'green';
    } elseif ($row['work_hours'] > 0) {
        $data[$row['date']] = 'yellow';
    }
}

/* === APPROVED LEAVES === */
$lq = $conn->prepare("
    SELECT start_date, end_date
    FROM leaves
    WHERE user_id = ? AND status='approved'
");
$lq->bind_param("i", $userId);
$lq->execute();
$lres = $lq->get_result();

while ($l = $lres->fetch_assoc()) {
    $d = strtotime($l['start_date']);
    $end = strtotime($l['end_date']);

    while ($d <= $end) {
        $data[date('Y-m-d', $d)] = 'pink';
        $d = strtotime("+1 day", $d);
    }
}

header('Content-Type: application/json');
echo json_encode($data);
