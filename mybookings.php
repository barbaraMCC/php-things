<?php
session_start();

// Protection : rediriger si pas connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab_equipment";

// Connexion à la base
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer les réservations de l'utilisateur
$user_id = $_SESSION['user_id'];

// --- RÉCUPÉRATION DES FILTRES ---
$filter_equipment = $_GET['equipment'] ?? '';
$filter_date = $_GET['date'] ?? '';
$hide_canceled = isset($_GET['hide_canceled']);

// Construction dynamique de la requête
$sql = "SELECT r.reservation_id, e.equipment_name, r.reservation_date, r.start_time, r.end_time, r.status 
        FROM reservation r 
        JOIN equipment e ON r.equipment_id = e.equipment_id 
        WHERE r.user_id = ?";

$params = [$user_id];
$types = "i";

if ($filter_equipment !== '') {
    $sql .= " AND e.equipment_name = ?";
    $params[] = $filter_equipment;
    $types .= "s";
}

if ($filter_date !== '') {
    $sql .= " AND r.reservation_date = ?";
    $params[] = $filter_date;
    $types .= "s";
}

if ($hide_canceled) {
    $sql .= " AND r.status != 'canceled'";
}

$sql .= " ORDER BY r.reservation_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$now = new DateTime();

// Pour remplir la liste déroulante des équipements
$eq_list = $conn->query("SELECT equipment_name FROM equipment");
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>My Bookings - Lab Booking</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <header>
      <div class="topbar">
        <h1>My Bookings</h1>
        <nav class="nav">
          <a href="index.php">Home</a>
          <a href="mybookings.php">My Bookings</a>
          <a href="login.php">Login</a>
        </nav>
        <div id="userInfo" class="user-info"></div>
      </div>
    </header>
    <main>
      <section style="max-width:900px;margin:16px auto;">
      <?php if (!isset($_SESSION['user_id'])): ?>
      <div class="muted">
        You are not logged in. <a href="login.php">Login</a> to see your bookings.
      </div>
    <?php else: ?>
      <div id="bookingsArea">
        
      <?php if (isset($_GET['report']) && $_GET['report'] === 'success'): ?>
            <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 16px; border: 1px solid #bbf7d0;">
                ✅ <b>Success!</b> Your report has been saved.
            </div>
        <?php endif; ?>

    <div class="card" style="margin-bottom: 24px;">
        <form method="GET" action="mybookings.php" class="filter-form">
        <div class="filter-group">
            <label>Equipment:</label>
            <select name="equipment">
                <option value="">All Equipments</option>
                <?php while($eq = $eq_list->fetch_assoc()): ?>
                    <option value="<?= $eq['equipment_name'] ?>" <?= $filter_equipment == $eq['equipment_name'] ? 'selected' : '' ?>>
                        <?= $eq['equipment_name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class = "filter-group">
            <label >Date</label>
            <input type="date" name="date" value="<?= $filter_date ?>" style="padding: 7px; border-radius: 6px; border: 1px solid var(--surface-border);">
        </div>

       <div class="filter-group checkbox-group">
            <input type="checkbox" name="hide_canceled" id="hide_canceled" <?= $hide_canceled ? 'checked' : '' ?>>
            <label for="hide_canceled">Hide canceled</label>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn primary">Filter</button>
            <a href="mybookings.php" class="btn ghost">Reset</a>
        </div>
        </form>
    </div>
          <h2>Your bookings</h2>
          <?php if ($result->num_rows === 0): ?>
              <p class="muted">No bookings yet.</p>
          <?php else: ?>
              <table id="myBookingsTable">
                  <thead>
                      <tr>
                          <th>Equipment</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Booking number</th>
                          <th>Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php while ($row = $result->fetch_assoc()): ?>
                          <tr>
                              <td><?= htmlspecialchars($row['equipment_name']) ?></td>
                              <td><?= htmlspecialchars($row['reservation_date']) ?></td>
                              <td>
                                 <?= htmlspecialchars(date('H:i', strtotime($row['start_time']))) ?>
                                  -
                                 <?= htmlspecialchars(date('H:i', strtotime($row['end_time']))) ?>
                              </td>
                              
                              <td> #<?= $row['reservation_id'] ?> </td>

                              <td class="actions-cell">
                                <?php 
                                    // On crée un objet DateTime pour le début de cette réservation précise
                                    $bookingStart = new DateTime($row['reservation_date'] . ' ' . $row['start_time']);
                                    $isPast = $bookingStart < $now; 
                                ?>

                                <?php if ($row['status'] === 'canceled'): ?>
                                    <span style="color: gray; font-weight: bold;">Canceled</span>
                                <?php else: ?>
                                    <?php if (!$isPast): ?>
                                    <form action="cancel_booking.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="reservation_id" value="<?= $row['reservation_id'] ?>">
                                        <button type="submit" onclick="return confirm('Cancel this booking?')">Cancel</button>
                                    </form>

                                    <?php endif; ?><?php if ($isPast): ?>
                                        <button type="button" class="btn-report" onclick="window.location.href='make_report.php?id=<?= $row['reservation_id'] ?>'">
                                            Report Issue
                                        </button>
                                    <?php endif; ?>
                                    
                                <?php endif; ?>
                            </td>

                          </tr>
                      <?php endwhile; ?>
                  </tbody>
              </table>
          <?php endif; ?>
      </div>
      <?php endif; ?>
      </section>
    </main>
  </body>
</html>

<?php
if (isset($_SESSION['user_id'])) {
    $stmt->close();
    $conn->close();
}
?>