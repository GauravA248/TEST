<?php
require_once __DIR__ . "/../config/user_guard.php";
require_once __DIR__ . "/../config/db.php";

header("Content-Type: application/json");

$user_id = $_SESSION['user_id'];
$month   = $_GET['month']; // YYYY-MM

$data = [];

/* ===== ATTENDANCE ===== */
$stmt = $conn->prepare("
    SELECT DATE(punch_in) as day, punch_in, punch_out, hours_worked
    FROM attendance
    WHERE user_id = ?
      AND DATE_FORMAT(punch_in,'%Y-%m') = ?
");
$stmt->bind_param("is", $user_id, $month);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {

    if (!$row['punch_out']) {
        $data[$row['day']] = "yellow";
    } elseif ($row['hours_worked'] >= 8.5) {
        $data[$row['day']] = "green";
    } else {
        $data[$row['day']] = "yellow";
    }
}

/* ===== LEAVES ===== */
$stmt = $conn->prepare("
    SELECT from_date, to_date
    FROM leaves
    WHERE user_id = ?
      AND status = 'Approved'
");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$res = $stmt->get_result();

while ($l = $res->fetch_assoc()) {
    $start = strtotime($l['from_date']);
    $end   = strtotime($l['to_date']);

    while ($start <= $end) {
        $data[date("Y-m-d",$start)] = "leave";
        $start = strtotime("+1 day",$start);
    }
}

echo json_encode($data);
