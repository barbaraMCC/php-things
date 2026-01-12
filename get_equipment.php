<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab_equipment";

// Connexion
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}

// Récupération des équipements
$sql = "SELECT equipment_id, equipment_name FROM equipment";
$result = $conn->query($sql);

$equipment = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $equipment[] = $row;
    }
}

// Retourner le JSON
header('Content-Type: application/json');
echo json_encode($equipment);

$conn->close();
?>
