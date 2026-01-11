<?php
header('Content-Type: application/json');

if (!isset($_POST['lat'], $_POST['lng'])) {
    echo json_encode(["error" => "Missing coordinates"]);
    exit;
}

$lat = $_POST['lat'];
$lng = $_POST['lng'];

$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lng&addressdetails=1";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "User-Agent: AttendanceSystem/1.0 (localhost)"
    ]
]);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(["error" => curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);
echo $response;
