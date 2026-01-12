<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab_equipment";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    exit;
}

$date = $_GET['date'] ?? null;
if (!$date) {
    echo json_encode([]);
    exit;
}

$sql = "
SELECT
  r.reservation_id,
  r.user_id,
  e.equipment_name,
  r.reservation_date,
  r.start_time,
  r.end_time
FROM reservation r
JOIN equipment e ON r.equipment_id = e.equipment_id
WHERE r.reservation_date = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
}

header('Content-Type: application/json');
echo json_encode($reservations);

$stmt->close();
$conn->close();
?>