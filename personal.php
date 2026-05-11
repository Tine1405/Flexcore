<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$id = $_SESSION['user_id'];//$id=pera_Sysyon['usr_id']

//sql fetch
//SLT u.*,t,fnmeASass_trner_nme
$sql = "SELECT u.*, t.fullname AS assigned_trainer_name 
        FROM users u 
        LEFT JOIN trainer_assignments ta ON u.id = ta.user_id 
        LEFT JOIN users t ON ta.trainer_id = t.id 
        WHERE u.id = '$id'";

$result = $conn->query($sql);
$user = $result->fetch_assoc();

$profile_image = !empty($user['image']) ? "uploads/" . $user['image'] : "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
?>

<?php if(isset($_GET['success'])): ?>
  <div id="alertBox" style="background:#22c55e; padding:10px; border-radius:8px; margin: 20px auto; max-width: 1000px; text-align: center; transition: 0.5s;"> 
    Action successfully completed! 
  </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FLEXCORE | My Account</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;800&display=swap" rel="stylesheet">
<style>
:root {
  --primary: #a855f7;
  --bg-dark: #09090b;
  --card-bg: rgba(24, 24, 27, 0.9);
  --text-main: #fafafa;
  --text-dim: #a1a1aa;
}

body {
  background: radial-gradient(circle at bottom right, #1e1b4b 0%, #09090b 60%);
  color: var(--text-main);
  font-family: 'Inter', sans-serif;
  min-height: 100vh;
  margin: 0;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 80px; 
  backdrop-filter: blur(10px);
  background: rgba(9, 9, 11, 0.8);
  border-bottom: 1px solid rgba(255,255,255,0.05);
}

.account-container {
  max-width: 1000px;
  margin: 60px auto;
  padding: 20px;
}

.account-grid {
  display: grid; 
  grid-template-columns: 350px 1fr;
  gap: 30px;
}

.profile-sidebar {
  background: var(--card-bg);
  border-radius: 24px;
  padding: 40px;
  text-align: center;
  border: 1px solid rgba(168, 85, 247, 0.2);
  height: fit-content;
}

.profile-img {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  border: 4px solid var(--primary); 
  margin-bottom: 20px;
  object-fit: cover;
}

.stats-card {
  background: var(--card-bg); 
  border-radius: 24px; 
  padding: 40px;
  border: 1px solid rgba(255,255,255,0.1);
}

.info-row {
  display: flex; 
  justify-content: space-between;
  padding: 15px 0; 
  border-bottom: 1px solid rgba(255,255,255,0.05);
}

.info-row span { 
  color: var(--text-dim); 
  font-size: 14px; 
}
.info-row p { 
  font-weight: 700; 
  color: white; 
  margin: 0; 
}

.progress-gallery {
  margin-top: 30px; 
  display: grid; 
  grid-template-columns: 1fr 1fr; 
  gap: 20px;
}

.progress-gallery img {
  width: 100%; 
  border-radius: 15px; 
  border: 1px solid rgba(255,255,255,0.1);
  aspect-ratio: 1/1; 
  object-fit: cover;
}

.btn-group {
 margin-top: 30px; 
 display: flex; 
 gap: 15px; 
}
.btn {
  flex: 1; 
  padding: 12px; 
  border-radius: 10px;
  border: none;
  font-weight: 700; 
  cursor: pointer; 
  transition: 0.3s;
  text-decoration: none; 
  text-align: center; 
  font-size: 14px;
}
.btn-edit { 
  background: var(--primary); 
  color: white; 
}
.btn-edit:hover { 
  background: #9333ea; 
  transform: translateY(-2px);
  }

.btn-delete { 
  background: rgba(239, 68, 68, 0.1); 
  color: #ef4444; 
  border: 1px solid #ef4444; 
}
.btn-delete:hover { 
  background: #ef4444; 
  color: white; 
}

.footer {
  text-align: center; 
  padding: 40px; 
  color: var(--text-dim); 
  font-size: 12px;
  border-top: 1px solid rgba(255,255,255,0.05); 
  margin-top: 100px;
}

@media (max-width: 850px) { 
    .account-grid { 
      grid-template-columns: 1fr; 
    } 
    .navbar { 
      padding: 20px 40px; 
    }
}
</style>
</head>
<body>

<header class="navbar">
  <div class="logo" style="color:var(--primary); font-weight:800; font-size:24px;">FLEXCORE.</div>
  <div style="font-size:14px; color:var(--text-dim);">
    <a href="index.php" style="color:inherit; text-decoration:none; margin-right:20px;">Back to Dashboard</a>
  </div>
</header>

<div class="account-container">
  <div class="account-grid">
    
    <div class="profile-sidebar">
      <img src="<?php echo $profile_image; ?>" class="profile-img">
      <h2 style="margin-bottom: 5px;"><?php echo $user['fullname']; ?></h2>
      <p style="color: var(--primary); font-weight: 600;">@<?php echo $user['username']; ?></p>

      <div style="margin-top: 20px; padding: 10px; background: rgba(168, 85, 247, 0.1); border-radius: 10px; font-size: 12px; letter-spacing: 1px; color: var(--primary); font-weight: 800;">
      <?php echo strtoupper($user['membership_plan'] ?? 'NO PLAN'); ?> MEMBER
      </div>

      <div style="margin-top: 10px; padding: 10px; background: rgba(255, 255, 255, 0.05); border-radius: 10px; font-size: 12px; letter-spacing: 1px; color: white;">
        <span style="display: block; font-size: 10px; color: var(--text-dim); margin-bottom: 2px;">COACH:</span>
        <?php echo strtoupper($user['assigned_trainer_name'] ?? 'NO TRAINER ASSIGNED'); ?>
      </div>


      <div class="btn-group">
        <a href="setting.php" class="btn btn-edit">Edit Profile</a>
      </div>
      <button class="btn btn-delete" style="margin-top: 15px; width: 100%;" onclick="confirmDelete()">Deactivate Account</button>
    </div>

    <div class="stats-card">
      <h3 style="margin-bottom: 25px; border-left: 4px solid var(--primary); padding-left: 15px;">Personal Details</h3>
      
      <div class="info-row"><span>Full Name</span><p><?php echo $user['fullname']; ?></p></div>
      <div class="info-row">
      <span>Email</span>
      <p><?php echo $user['email']; ?></p>
      </div>

      <div class="info-row"><span>Weight</span><p><?php echo $user['weight']; ?> kg</p></div>

      <div class="info-row"><span>Height</span><p><?php echo $user['height']; ?> cm</p></div>

      <div class="info-row"><span>Current Goal</span><p><?php echo $user['goal']; ?></p></div>

      <div class="info-row"><span>Program</span><p><?php echo $user['program']; ?></p></div>

      <h3 style="margin: 40px 0 20px; border-left: 4px solid var(--primary); padding-left: 15px;">Transformation Progress</h3>
      <div class="progress-gallery">
        <div>
            <p style="font-size:12px; color:var(--text-dim); margin-bottom:10px;">MONTH 0 (Starting)</p>
            <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&q=80&w=400" alt="Before">
        </div>
        <div>
            <p style="font-size:12px; color:var(--text-dim); margin-bottom:10px;">MONTH 3 (Current)</p>
            <img src="https://images.unsplash.com/photo-1583454110551-21f2fa29e58b?auto=format&fit=crop&q=80&w=400" alt="After">
        </div>
      </div>

      <div style="margin-top: 40px; padding: 20px; background: rgba(255,255,255,0.03); border-radius: 15px;">
          <h4 style="margin: 0 0 10px 0; color: var(--primary);">Next Goal Milestone</h4>
          <p style="font-size: 14px; margin: 0; color: var(--text-dim);">You are 2.4kg away from your target weight! Keep pushing the hypertrophy program.</p>
      </div>
    </div>
    
  </div>
</div>

<footer class="footer">
    &copy; 2026 FLEXCORE Fitness Systems. All rights reserved.<br>
    Built for high-performance athletes.
</footer>

<script>
    function confirmDelete() {
        if(confirm("Are you sure you want to deactivate your account? This action cannot be undone.")) {
            alert("Account deactivation request sent.");
        }
    }

 
    const alertBox = document.getElementById('alertBox');
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.opacity = "0";
            setTimeout(() => { alertBox.style.display = "none"; }, 500);
        }, 3000);
    }
</script>

</body>
</html>