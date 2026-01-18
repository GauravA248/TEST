<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../config/user_guard.php";
require_once __DIR__ . "/../config/db.php";

header("Content-Type: application/json");

$user_id = $_SESSION['user_id'] ?? 0;
$action  = $_POST['action'] ?? '';

if (!$user_id) {
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit;
}

try {

    /* ========= STATUS (ON PAGE LOAD) ========= */
    if ($action === "status") {

        $stmt = $conn->prepare("
            SELECT * FROM attendance
            WHERE user_id = ?
              AND DATE(punch_in) = CURDATE()
            LIMIT 1
        ");
        $stmt->bind_param("i",$user_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!$row) {
            echo json_encode(["status"=>"none"]);
            exit;
        }

        echo json_encode([
            "status"=>"found",
            "punch_in"=>$row['punch_in'],
            "punch_out"=>$row['punch_out'],
            "location"=>$row['punch_in_location']
        ]);
        exit;
    }

    /* ========= PUNCH IN ========= */
    if ($action === "punch_in") {

        $location = $_POST['location'] ?? '';

        $stmt = $conn->prepare("
            SELECT id FROM attendance
            WHERE user_id=? AND DATE(punch_in)=CURDATE()
        ");
        $stmt->bind_param("i",$user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0)
            throw new Exception("Already punched in today");

        $stmt = $conn->prepare("
            INSERT INTO attendance (user_id,punch_in,punch_in_location)
            VALUES (?,NOW(),?)
        ");
        $stmt->bind_param("is",$user_id,$location);
        $stmt->execute();

        echo json_encode(["status"=>"success","message"=>"Punch In successful"]);
        exit;
    }

/* ========= PUNCH OUT ========= */
if ($action === "punch_out") {

    $location = $_POST['location'] ?? '';

    $stmt = $conn->prepare("
        SELECT id,punch_in,punch_out FROM attendance
        WHERE user_id=? AND DATE(punch_in)=CURDATE()
        LIMIT 1
    ");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) throw new Exception("Punch in first");
    if ($row['punch_out']) throw new Exception("Already punched out");

    // ✅ TIME RESTRICTION REMOVED

    // $hours = round((time()-strtotime($row['punch_in']))/3600,2);
    $punchIn  = strtotime($row['punch_in']);
            $punchOut = strtotime(date('Y-m-d H:i:s')); // current time

            $diffSeconds = $punchOut - $punchIn;

            // Safety check (no negative)
            if ($diffSeconds < 0) {
                $diffSeconds = abs($diffSeconds);
            }

            // Convert seconds to HH:MM:SS
            $h = floor($diffSeconds / 3600);
            $m = floor(($diffSeconds % 3600) / 60);
            $s = $diffSeconds % 60;

            $duration = sprintf('%02d:%02d:%02d', $h, $m, $s);


    $stmt = $conn->prepare("
        UPDATE attendance
        SET punch_out=NOW(),
            punch_out_location=?,
            hours_worked=?
        WHERE id=?
    ");
    // $stmt->bind_param("sdi",$location,$hours,$row['id']);
    $stmt->bind_param("ssi", $location, $duration, $row['id']);
    $stmt->execute();

    echo json_encode(["status"=>"success","message"=>"Punch Out successful"]);
    exit;
}


    throw new Exception("Invalid action");

} catch(Exception $e){
    echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
}
