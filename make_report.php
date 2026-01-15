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
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$reservation_id = $_GET['id'] ?? null;
$equipment_name = "";

// 1. Récupérer le nom de l'équipement pour le titre
if ($reservation_id) {
    $stmt = $conn->prepare("SELECT e.equipment_name FROM reservation r JOIN equipment e ON r.equipment_id = e.equipment_id WHERE r.reservation_id = ?");
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $equipment_name = $row['equipment_name'];
    } else {
        header("Location: mybookings.php"); // ID invalide
        exit;
    }
}

// 2. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res_id = $_POST['reservation_id'];
    $desc = $_POST['description'];
    $now = date('Y-m-d H:i:s');
    $status = "pending";

    $ins = $conn->prepare("INSERT INTO problem_report (reservation_id, description, reported_at, status) VALUES (?, ?, ?, ?)");
    $ins->bind_param("isss", $res_id, $desc, $now, $status);
    
    if ($ins->execute()) {
        // Redirection avec un paramètre de succès
        header("Location: mybookings.php?report=success");
        exit;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Report Issue - Lab Booking</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .report-container { max-width: 600px; margin: 40px auto; }
        textarea { 
            width: 100%; 
            height: 150px; 
            padding: 12px; 
            border: 1px solid var(--surface-border); 
            border-radius: 8px; 
            margin: 15px 0;
            font-size: 14px;
            resize: vertical;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="card">
            <h2 class="modal-title">Please explain your problem with the <u><?= htmlspecialchars($equipment_name) ?></u></h2>
            <p class="muted">Reservation #<?= htmlspecialchars($reservation_id) ?></p>
            
            <form action="make_report.php" method="POST">
                <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($reservation_id) ?>">
                
                <label for="description">Issue Description :</label>
                <textarea name="description" id="description" placeholder="Describe the problem in detail (max 600 chars)..." maxlength="600" required></textarea>
                
                <div style="display:flex; gap:10px; justify-content: flex-end;">
                    <a href="mybookings.php" class="btn ghost" style="text-decoration:none;">Cancel</a>
                    <button type="submit" class="btn primary">Send Report</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>