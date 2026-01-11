<?php
require_once __DIR__ . "/../config/user_guard.php";
require_once __DIR__ . "/../config/db.php";

header("Content-Type: application/json");

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT punch_in, punch_out
    FROM attendance
    WHERE user_id = ? AND DATE(punch_in) = CURDATE()
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode($row ?: []);
