<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../config/user_guard.php";
require_once __DIR__ . "/../config/db.php";

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id  = $_SESSION['user_id'];
$action   = $_POST['action'] ?? '';
$location = $_POST['location'] ?? '';

try {

    /* ===================== PUNCH IN ===================== */
    if ($action === "punch_in") {

        $stmt = $conn->prepare("
            SELECT id FROM attendance
            WHERE user_id = ? AND DATE(punch_in) = CURDATE()
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            throw new Exception("Already punched in today");
        }

        $stmt = $conn->prepare("
            INSERT INTO attendance (user_id, punch_in, punch_in_location)
            VALUES (?, NOW(), ?)
        ");
        $stmt->bind_param("is", $user_id, $location);
        $stmt->execute();

        echo json_encode([
            "status"  => "success",
            "message" => "Punch In successful",
            "time"    => date("H:i:s")
        ]);
        exit;
    }

    /* ===================== PUNCH OUT ===================== */
    if ($action === "punch_out") {

        $stmt = $conn->prepare("
            SELECT id, punch_in, punch_out
            FROM attendance
            WHERE user_id = ? AND DATE(punch_in) = CURDATE()
            LIMIT 1
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            throw new Exception("Punch in first");
        }

        if (!empty($row['punch_out'])) {
            throw new Exception("Already punched out");
        }

        $hours = round((time() - strtotime($row['punch_in'])) / 3600, 2);

        $stmt = $conn->prepare("
            UPDATE attendance
            SET punch_out = NOW(),
                punch_out_location = ?,
                hours_worked = ?
            WHERE id = ?
        ");
        $stmt->bind_param("sdi", $location, $hours, $row['id']);
        $stmt->execute();

        echo json_encode([
            "status"  => "success",
            "message" => "Punch Out successful",
            "time"    => date("H:i:s")
        ]);
        exit;
    }

    throw new Exception("Invalid action");

} catch (Exception $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
