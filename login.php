<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Login - Lab Booking</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <header>
      <div class="topbar">
        <h1>Login</h1>
        <nav class="nav">
          <a href="index.php">Home</a>
          <a href="mybookings.php">My Bookings</a>
          <a href="login.php">Login</a>
        </nav>
        <div id="userInfo" class="user-info"></div>
      </div>
    </header>
    <main>
      <section class="card" style="max-width:420px;margin:20px auto;">
        <form id="loginForm" action="loginUser.php" method="POST">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" required placeholder="your@email.com" />
          <label for="password">Password</label>
          <input id="password" name="password" type= "password" required placeholder="Your password" />
          <div style="margin-top:12px;display:flex;gap:8px;justify-content:flex-end;">
            <button type="submit">Login</button>
            <a href="register.html" class="muted">Register</a>
          </div>
        </form>
        <?php if (isset($_GET['error'])): ?>
          <div class="muted" style="color:#c0392b;margin-bottom:10px;">
          <?php if ($_GET['error'] === 'notfound'): ?>
            User does not exist.
          <?php elseif ($_GET['error'] === 'wrongpass'): ?>
            Incorrect password.
          <?php endif; ?>
          </div>
        <?php endif; ?>

      </section>
    </main>
     
  </body>
</html>
