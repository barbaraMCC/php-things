<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$user_id = $_SESSION['user_id']; // 🔐 on ne fait PAS confiance au JS
$equipment_id = $input['equipment_id'] ?? null;
$reservation_date = $input['reservation_date'] ?? null;
$start_time = $input['start_time'] ?? null;
$end_time = $input['end_time'] ?? null;
$purpose = $input['purpose'] ?? '';

if (!$equipment_id || !$reservation_date || !$start_time || !$end_time) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing fields']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "lab_equipment");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$stmt = $conn->prepare("
  INSERT INTO reservation (user_id, equipment_id, reservation_date, start_time, end_time, purpose)
  VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
  "iissss",
  $user_id,
  $equipment_id,
  $reservation_date,
  $start_time,
  $end_time,
  $purpose
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => $stmt->error]);
}

$stmt->close();
$conn->close();
