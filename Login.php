<?php
session_start();
include "db.php";


if (isset($_SESSION['user_id'])) {
    header("Location: " . $_SESSION['role'] . "index.php");
    exit();
}


if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $selected_role = $_POST['role'];


    if ($_SESSION['attempts'] >= 2) {

        if (empty($_POST['g-recaptcha-response'])) {
            header("Location: login.php?error=captcha_required");
            exit();
        }


        $secretKey = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";
        $captcha = $_POST['g-recaptcha-response'];

        $verifyResponse = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret="
            . $secretKey
            . "&response="
            . $captcha
        );

        $captchaResult = json_decode($verifyResponse);

        if (!$captchaResult || !$captchaResult->success) {
            header("Location: login.php?error=captcha_failed");
            exit();
        }
    }


    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['attempts'] = 0;

            if ($user['role'] !== $selected_role) {
                header("Location: login.php?error=role_mismatch");
                exit();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            switch ($user['role']) {
                case 'admin':
                    header("Location: admindashboad.php");
                    break;
                case 'trainer':
                    header("Location: trainerdashboard.php");
                    break;
                default:
                    header("Location: index.php");
                    break;
            }
            exit();

        } else {
            $_SESSION['attempts']++;
            header("Location: login.php?error=wrong_password");
            exit();
        }

    } else {
        $_SESSION['attempts']++;
        header("Location: login.php?error=user_not_found");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FLEXCORE | Premium Gym Management</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
  --primary: #a855f7;
  --primary-dark: #9333ea;
  --bg-dark: #09090b;
  --card-bg: rgba(24, 24, 27, 0.8);
  --text-main: #fafafa;
  --text-dim: #a1a1aa;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Inter', sans-serif;
}

body {
  background: radial-gradient(circle at 0% 0%, #1e1b4b 0%, #09090b 50%);
  color: var(--text-main);
  min-height: 100vh;
  line-height: 1.6;
  overflow-x: hidden;
  scroll-behavior: smooth;
}

/* Navbar */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 25px 80px;
  position: sticky;
  top: 0;
  z-index: 100;
  backdrop-filter: blur(10px);
  background: rgba(9, 9, 11, 0.7);
}

.logo {
  font-weight: 800;
  font-size: 24px;
  letter-spacing: -1px;
  color: var(--primary);
}

.nav-links a {
  color: var(--text-dim);
  text-decoration: none;
  margin-left: 40px;
  font-size: 14px;
  font-weight: 600;
  transition: 0.3s;
}

.nav-links a:hover {
  color: var(--primary);
}

/* Hero Section */
.container {
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap: 40px;
  padding: 80px;
  align-items: center;
  max-width: 1400px;
  margin: 0 auto;
}

.left h1 {
  font-size: clamp(40px, 5vw, 72px);
  line-height: 1.1;
  font-weight: 800;
  margin-bottom: 30px;
  background: linear-gradient(to right, #fff, var(--primary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.info {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
}

.info h2 {
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: var(--primary);
  margin-bottom: 15px;
}

.info ul {
  list-style: none;
  color: var(--text-dim);
  font-size: 15px;
}

.info li {
  margin-bottom: 8px;
  display: flex;
  align-items: center;
}

.info li::before {
  content: "→";
  margin-right: 10px;
  color: var(--primary);
}

/* Right Side Image & Glow */
.right {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.image-frame {
  position: relative;
  border-radius: 24px;
  padding: 10px;
  background: linear-gradient(45deg, var(--primary), transparent);
}

.image-frame img {
  width: 100%;
  max-width: 400px;
  border-radius: 18px;
  display: block;
}

.signin-btn {
  margin-top: -30px;
  z-index: 2;
  padding: 18px 50px;
  font-size: 16px;
  font-weight: 700;
  border: none;
  background: var(--primary);
  color: #fff;
  border-radius: 12px;
  cursor: pointer;
  box-shadow: 0 10px 30px rgba(168, 85, 247, 0.4);
  transition: 0.3s;
}

.signin-btn:hover {
  transform: translateY(-5px);
  background: var(--primary-dark);
  box-shadow: 0 15px 40px rgba(168, 85, 247, 0.6);
}

/* About & Programs Sections */
.content-section {
  padding: 80px 80px;
  max-width: 1400px;
  margin: 0 auto;
}

.section-title {
  font-size: 32px;
  margin-bottom: 50px;
  text-align: center;
  font-weight: 800;
}

.section-title span {
  color: var(--primary);
}

.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 30px;
}

.custom-card {
  background: var(--card-bg);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 20px;
  padding: 30px;
  transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  backdrop-filter: blur(10px);
  text-align: center;
}

.custom-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 12px;
  margin-bottom: 20px;
}

.custom-card h3 {
  margin-bottom: 15px;
  font-weight: 700;
}

.custom-card p {
  color: var(--text-dim);
  font-size: 15px;
}

.custom-card:hover {
  transform: translateY(-10px);
  border-color: var(--primary);
  box-shadow: 0 20px 40px rgba(0,0,0,0.4);
}

.highlight-card {
  border: 1px solid var(--primary);
  background: rgba(168, 85, 247, 0.1);
}

/* Modal */
.modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.85);
  backdrop-filter: blur(8px);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  opacity: 0;
  transition: 0.3s;
}

.modal.show {
  display: flex;
  opacity: 1;
}

.form-box {
  background: #18181b;
  padding: 40px;
  border-radius: 24px;
  width: 100%;
  max-width: 400px;
  border: 1px solid rgba(168, 85, 247, 0.3);
}

.form-box input {
  width: 100%;
  padding: 14px;
  margin: 10px 0;
  background: #09090b;
  border: 1px solid #27272a;
  border-radius: 10px;
  color: white;
  outline: none;
}

.form-box button {
  width: 100%;
  padding: 14px;
  background: var(--primary);
  border: none;
  color: white;
  border-radius: 10px;
  font-weight: 700;
  cursor: pointer;
  margin-top: 10px;
}

/* Footer */
.footer {
  background: #09090b;
  padding: 80px 80px 30px;
  border-top: 1px solid #18181b;
}

.footer-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 50px;
  max-width: 1400px;
  margin: 0 auto;
}

.footer-col h3, .footer-col h4 {
  color: var(--primary);
  margin-bottom: 20px;
}

.footer-col p, .footer-col a {
  color: var(--text-dim);
  text-decoration: none;
  display: block;
  margin-bottom: 10px;
  font-size: 14px;
}

.footer-bottom {
  text-align: center;
  margin-top: 50px;
  padding-top: 20px;
  border-top: 1px solid #18181b;
  font-size: 12px;
  color: #555;
}

@media (max-width: 900px) {
  .container { grid-template-columns: 1fr; padding: 40px; text-align: center; }
  .info { justify-content: center; }
  .navbar { padding: 20px; }
}
.admin-btn {
  display: block;
  width: 100%;
  text-align: center;
  margin-top: 12px;
  padding: 12px;
  background: rgba(168, 85, 247, 0.15);
  border: 1px solid rgba(168, 85, 247, 0.5);
  color: #a855f7;
  border-radius: 10px;
  font-weight: 700;
  text-decoration: none;
  transition: 0.3s;
}

/* INPUT WITH ICON */
.input-group {
  display: flex;
  align-items: center;
  background: #09090b;
  border: 1px solid #27272a;
  border-radius: 10px;
  margin: 10px 0;
  padding: 0 12px;
}

.input-group i {
  color: var(--primary);
  margin-right: 10px;
  font-size: 14px;
}

.input-group input {
  width: 100%;
  padding: 14px 5px;
  border: none;
  outline: none;
  background: transparent;
  color: white;
}

/* ROLE SELECT BOX */
.role-box {
  margin: 12px 0;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.role-box label {
  font-size: 13px;
  color: var(--text-dim);
}

.role-box select {
  padding: 12px;
  border-radius: 10px;
  border: 1px solid #27272a;
  background: #09090b;
  color: white;
  outline: none;
  cursor: pointer;
  transition: 0.3s;
}

.role-box select:focus {
  border-color: var(--primary);
  box-shadow: 0 0 10px rgba(168, 85, 247, 0.3);
}
</style>
</head>
<body>

<header class="navbar">
  <div class="logo">FLEXCORE.</div>
  <nav class="nav-links">
    <a href="#home">HOME</a>
    <a href="#about">ABOUT</a>
    <a href="#programs">PROGRAMS</a>
  </nav>
</header>

<main class="container" id="home">
  <section class="left">
    <h1>GYM MANAGEMENT<br>SYSTEM</h1>
    <div class="info">
      <div>
        <h2>FEATURES</h2>
        <ul>
          <li>Member registration and tracking</li>
          <li>Workout plans and schedules</li>
          <li>Supplement shop</li>
        </ul>
      </div>
      <div>
        <h2>OPERATIONS</h2>
        <ul>
          <li>Monitor attendance</li>
          <li>Manage trainers</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="right">
    <div class="image-frame">
      <img src="image.jpg" alt="Gym">
    </div>
    <button class="signin-btn" onclick="openModal()">SIGN IN</button>
  </section>
</main>

<section id="about" class="content-section">
  <h2 class="section-title">ABOUT <span>US</span></h2>
  <div class="card-grid">
    <div class="custom-card">
      <h3>WHO WE ARE</h3>
      <p>FLEXCORE is a modern gym management system designed to help gyms manage members, workouts, and progress efficiently.</p>
    </div>
    <div class="custom-card highlight-card">
      <h3>OUR MISSION</h3>
      <p>To empower individuals to achieve their fitness goals through technology, tracking, and smart gym solutions.</p>
    </div>
    <div class="custom-card">
      <h3>OUR VISION</h3>
      <p>To become a leading platform in digital fitness management systems worldwide.</p>
    </div>
  </div>
</section>

<section id="programs" class="content-section">
  <h2 class="section-title">OUR <span>PROGRAMS</span></h2>
  <div class="card-grid">
    <div class="custom-card">
      <img src="imageS.jpg" alt="Strength">
      <h3>STRENGTH TRAINING</h3>
      <p>Build Muscle & Power</p>
    </div>
    <div class="custom-card highlight-card">
      <img src="imageC.jpg" alt="HIIT">
      <h3>HIIT & CARDIO</h3>
      <p>Burn Fat & Boost Endurance</p>
    </div>
    <div class="custom-card">
      <img src="imageY.jpg" alt="Yoga">
      <h3>YOGA & FLEXIBILITY</h3>
      <p>Improve Balance & Flexibility</p>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="footer-container">
    <div class="footer-col">
      <h3>FLEXCORE</h3>
      <p>Your ultimate gym management system. Track workouts, manage members, and stay fit.</p>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <a href="#home">Home</a>
      <a href="#about">About</a>
      <a href="#programs">Programs</a>
    </div>
    <div class="footer-col">
      <h4>Contact</h4>
      <p>Email: flexcore@email.com</p>
      <p>Phone: +63 912 345 6789</p>
    </div>
    <div class="footer-col">
      <h4>Follow Us</h4>
      <a href="#">Facebook</a>
      <a href="#">Instagram</a>
      <a href="#">Twitter</a>
    </div>
  </div>
  <div class="footer-bottom">
    © 2026 Flexcore. All rights reserved.
  </div>
</footer>

<div id="modal" class="modal">

  <!-- LOGIN -->
  <div class="form-box" id="loginForm">
    <h2 style="margin-bottom: 20px; text-align: center;">
      <i class="fa-solid fa-right-to-bracket"></i> Login
    </h2>

    <form action="login.php" method="POST">

      <!-- USERNAME -->
      <div class="input-group">
        <i class="fa-solid fa-user"></i>
        <input type="text" name="username" placeholder="Username" required>
      </div>

      <!-- PASSWORD -->
      <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>

      <div class="role-box">
  <label><i class="fa-solid fa-user-shield"></i> Login as</label>
  <select name="role" required>
    <option value="user">👤 User</option>
    <option value="trainer">💪 Trainer</option>
    <option value="admin">🛡️ Admin</option>
  </select>
</div>

<?php if (isset($_GET['error'])): ?>
  <div style="margin-bottom:15px; padding:10px; border-radius:8px; background:#3f1d1d; color:#ffb4b4; font-size:14px; text-align:center;">
    
    <?php
      switch ($_GET['error']) {
        case 'wrong_password':
          echo "Incorrect password. Please try again.";
          break;
        case 'user_not_found':
          echo "User not found.";
          break;
        case 'role_mismatch':
          echo "Selected role does not match your account.";
          break;
        case 'captcha_required':
          echo "Too many failed attempts. Please complete the CAPTCHA.";
          break;
        case 'captcha_failed':
          echo "CAPTCHA verification failed. Try again.";
          break;
        default:
          echo "Login error. Please try again.";
      }
    ?>

  </div>
<?php endif; ?>

<?php if (isset($_SESSION['attempts']) && $_SESSION['attempts'] >= 1): ?>
  <div style="margin-bottom:10px; font-size:13px; color:#facc15; text-align:center;">
    Attempt <?php echo $_SESSION['attempts']; ?> of 2 before CAPTCHA is required.
  </div>
  
<?php endif; ?>


<div id="captchaBox" style="display:none; margin-top:10px;">
  <div class="g-recaptcha"></div>
</div>
      <button type="submit">
        <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
      </button>
    </form>

    <p style="text-align:center; margin-top:20px; font-size:14px; color:var(--text-dim)">
      Don't have an account?
      <a href="#" onclick="showRegister()" style="color:var(--primary)">Register</a>
    </p>
  </div>

  <!-- REGISTER -->
  <div class="form-box" id="registerForm" style="display:none;">
    <h2 style="margin-bottom: 20px; text-align: center;">
      <i class="fa-solid fa-user-plus"></i> Register
    </h2>

    <form action="register.php" method="POST" enctype="multipart/form-data">

      <div class="input-group">
        <i class="fa-solid fa-id-card"></i>
        <input type="text" name="fullname" placeholder="Full Name" required>
      </div>

      <div class="input-group">
        <i class="fa-solid fa-user"></i>
        <input type="text" name="username" placeholder="Username" required>
      </div>

      <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="email" placeholder="Email" required>
      </div>

      <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>

        <div class="role-box">
  <label><i class="fa-solid fa-user-shield"></i> Login as</label>
  <select name="role" required>
    <option value="user">👤 User</option>
    <option value="trainer">💪 Trainer</option>
     <option value="admin">🛡️ Admin</option>
  </select>
</div>


      <!-- IMAGE -->
      <div class="input-group">
        <i class="fa-solid fa-image"></i>
        <input type="file" name="image" accept="image/*" required>
      </div>

      <button type="submit">
        <i class="fa-solid fa-user-plus"></i> Register
      </button>
    </form>

    <p style="text-align:center; margin-top:20px; font-size:14px; color:var(--text-dim)">
      Already have an account?
      <a href="#" onclick="showLogin()" style="color:var(--primary)">Login</a>
    </p>
  </div>

</div>
<!-- Google reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
let recaptchaWidget;

function showRegister() {
  document.getElementById("loginForm").style.display = "none";
  document.getElementById("registerForm").style.display = "block";
}

function showLogin() {
  document.getElementById("registerForm").style.display = "none";
  document.getElementById("loginForm").style.display = "block";
}

function openModal() {
  const modal = document.getElementById("modal");
  modal.style.display = "flex";
  setTimeout(() => modal.classList.add("show"), 10);
}

window.onclick = function (e) {
  const modal = document.getElementById("modal");
  if (e.target === modal) {
    modal.classList.remove("show");
    setTimeout(() => modal.style.display = "none", 300);
  }
};

document.addEventListener("DOMContentLoaded", function () {

  const attempts = <?php echo (int)($_SESSION['attempts'] ?? 0); ?>;
  const captchaBox = document.getElementById("captchaBox");

  if (attempts >= 2 && captchaBox) {

    captchaBox.style.display = "block";

    setTimeout(() => {

      if (typeof grecaptcha !== "undefined") {

        grecaptcha.render(
          captchaBox.querySelector('.g-recaptcha'),
          {
            sitekey: '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI'
          }
        );

      }

    }, 300);

  }

});

</script>
</body>
</html>