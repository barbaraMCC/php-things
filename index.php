<?php
session_start();

// Définir une variable JS pour savoir si on est connecté
$loggedIn = isset($_SESSION['user_id']);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Lab Equipment Booking</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <header>
      <div class="topbar">
        <h1>Lab Equipment Booking </h1>
        <nav class="nav">
          <a href="index.php">Home</a>
          <a href="mybookings.php">My Bookings</a>
          <a href="login.php">Login</a>
        </nav>
        <div id="userInfo" class="user-info"></div>
      </div>
      <p>Select equipment on the left and click a time cell to book. Booked cells are marked with <strong>X</strong>.</p>
    </header>

    <main>
      <div class="controls">
        <div class="fixed-hours">Hours: 09:00 - 20:00</div>
        <div class="date-picker-container">
          <label for="bookingDate">Date of booking :</label>
          <input type="date" id="bookingDate" />
        </div>
      </div>



      <div class="table-wrap">
        <table id="schedule">
          <!-- Dynamically generated -->
        </table>
      </div>
    </main>


    <!-- Modal for nicer confirmations -->
    <div id="modal" class="modal hidden" aria-hidden="true">
      <div class="modal-content" role="dialog" aria-modal="true">
        <div class="modal-title" id="modalTitle">Confirm</div>
        <div class="modal-body" id="modalBody">Are you sure?</div>
        <div class="modal-actions">
          <button id="modalCancel" class="btn ghost">Cancel</button>
          <button id="modalOk" class="btn primary">OK</button>
        </div>
      </div>
    </div>

   
<script>
  window.bookingApp = window.bookingApp || {};
  window.bookingApp.loggedIn = <?= $loggedIn ? 'true' : 'false' ?>;
  window.bookingApp.currentUser = {
    user_id: <?= $loggedIn ? $_SESSION['user_id'] : 'null' ?>,
  };
</script>



    <script src="app.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', async () => {
        // Update user UI
        bookingApp.updateUserUI();
      });
    </script>
  </body>
</html>
