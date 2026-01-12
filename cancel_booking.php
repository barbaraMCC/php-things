<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab_equipment";

$conn = new mysqli($servername, $username, $password, $dbname);

$reservation_id = $_POST['reservation_id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    UPDATE reservation
    SET status = 'canceled'
    WHERE reservation_id = ? AND user_id = ?
");
$stmt->bind_param("ii", $reservation_id, $user_id);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: mybookings.php");
exit;
