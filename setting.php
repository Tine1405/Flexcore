<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id='$id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$profile_image = "uploads/" . ($user['image'] ?? "default.png");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings</title>

<link rel="stylesheet" href="style.css">
</head>

<body>

<header class="navbar">
  <div class="logo">FLEXCORE</div>
  <div class="nav-links">
    <a href="index.php">Dashboard</a>
    <a href="shop.php">Shop</a>
    <a href="membership.php">Membership</a>
    <a href="personal.php">My Account</a>
    <a href="settings.php">Settings</a>
  </div>
</header>

<div id="notification" class="notification"></div>

<section class="settings-page">
  <div class="settings-container">

    <h1>Account Settings</h1>

    <!-- FORM START -->
    <form action="update_profile.php" method="POST" enctype="multipart/form-data">

    <div class="settings-card">
      <h2>Profile Settings</h2>

      <!-- IMAGE -->
      <img src="<?php echo $profile_image; ?>" width="120" style="border-radius:50%; margin-bottom:15px;">

      <label>Full Name</label>
      <input type="text" name="fullname" value="<?php echo $user['fullname']; ?>" required>

      <label>Username</label>
      <input type="text" name="username" value="<?php echo $user['username']; ?>" required>

      <label>Email</label>
      <input type="email" name="email" value="<?php echo $user['email']; ?>" required>

      <label>Weight (kg)</label>
      <input type="number" step="0.1" name="weight" value="<?php echo $user['weight']; ?>">

      <label>Height (cm)</label>
      <input type="number" name="height" value="<?php echo $user['height']; ?>">

      <label>Fitness Goal</label>
      <input type="text" name="goal" value="<?php echo $user['goal']; ?>">

      <label>Program</label>
      <input type="text" name="program" value="<?php echo $user['program']; ?>">
      <label>Upload Profile Picture</label>
      <input type="file" name="image">

    </div>

    <!-- SECURITY -->
    <div class="settings-card">
      <h2>Security</h2>

      <label>New Password</label>
      <input type="password" name="password">

      <label>Confirm Password</label>
      <input type="password" name="confirmPassword">
    </div>

    <div class="settings-actions">
      <button type="submit" class="save-btn">Save Changes</button>
      <a href="logout.php"><button type="button" class="logout-btn">Logout</button></a>
    </div>

    </form>
    <!-- FORM END -->

  </div>
</section>

<footer class="footer">
  <div class="footer-container">
    <div class="footer-col">
      <h3>FLEXCORE</h3>
      <p>Your ultimate gym system.</p>
    </div>
  </div>
</footer>

</body>
</html>