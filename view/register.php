<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link rel="stylesheet" href="../css/style.css">
  <script src="../js/register.js" defer></script>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <h2>MyShop</h2>
    <div>
      <a href="../index.php">Home</a>
      <a href="login.php">Login</a>
    </div>
  </div>

  <!-- Registration Form -->
  <div class="form-container">
    <h2>Create an Account</h2>
    <form id="registerForm">
  <div class="form-group">
    <label for="full_name">Full Name</label>
    <input type="text" name="full_name" id="full_name" required>
  </div>

  <div class="form-group">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" required>
    <div id="emailStatus" class="msg"></div>
  </div>

  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" name="password" id="password" required autocomplete="new-password" data-lpignore="true" data-form-type="other">
  </div>

  <div class="form-group">
    <label for="country">Country</label>
    <input type="text" name="country" id="country" required>
  </div>

  <div class="form-group">
    <label for="city">City</label>
    <input type="text" name="city" id="city" required>
  </div>

  <div class="form-group">
    <label for="contact_number">Contact Number</label>
    <input type="text" name="contact_number" id="contact_number" required>
  </div>

  <button type="submit" id="registerBtn" class="primary-btn">Register</button>
  <div id="registerMessage" class="msg"></div>
</form>

  </div>

</body>
</html>
