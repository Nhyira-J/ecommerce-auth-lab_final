<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Login</title>
  <link rel="stylesheet" href="../css/style.css">
  <script src="../js/login.js" defer></script>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <h2>MyShop</h2>
    <div>
      <a href="../index.php">Home</a>
      <a href="register.php">Register</a>
    </div>
  </div>

  <!-- Login Form -->
  <div class="form-container">
    <h2>Customer Login</h2>
    <form id="loginForm">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
      </div>

      <button type="submit" id="loginBtn" class="primary-btn">Login</button>
      <div id="loginMessage" class="msg"></div>
    </form>
  </div>

</body>
</html>
