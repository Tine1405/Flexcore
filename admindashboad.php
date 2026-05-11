<?php
session_start();
include "db.php";

// 1. Protection Gate (Must be at the very top)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {//if(notisset(session['usrid']) and session[role] the not 'admin')
    header("Location: login.php");//this stays you at the login page
    exit();
}

//shows member
$members = []; 
$result = $conn->query("SELECT id, fullname, username, role FROM users ORDER BY id DESC LIMIT 10"); //result to connection->query then SQL from user order by id and desc limit 10

//fetch the members
while ($row = $result->fetch_assoc()) { 
    $members[] = $row; 
} 
//shows those people who purchase in premium
$premiumResult = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='admin'"); //premiumreslt= connector->query
$premium = $premiumResult->fetch_assoc()['total']; 

//shows total member
$totalMembers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total']; 

//shows the sales
$salesResult = $conn->query("SELECT SUM(total) as total FROM orders"); //use SUM for order purchase
$sales = $salesResult->fetch_assoc()['total'] ?? 0; 

//Shows monthly members
$monthly = $conn->query("SELECT MONTH(created_at) as month, SUM(total) as total FROM orders GROUP BY MONTH(created_at)"); 

$chartData = []; 
while ($row = $monthly->fetch_assoc()) { 
    $chartData[] = $row['total']; 
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Flexcore | Admin Dashboard</title>

<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root {
  --primary: #a855f7;
  --primary-hover: #9333ea;
  --bg-dark: #0f172a;
  --card-bg: rgba(30, 41, 59, 0.5);
  --sidebar-bg: rgba(15, 23, 42, 0.8);
  --text-main: #f8fafc;
  --text-dim: #94a3b8;
  --border: rgba(255, 255, 255, 0.1);
  --success: #22c55e;
  --danger: #ef4444;
}

.light-mode {
  --bg-dark: #f1f5f9;
  --card-bg: #ffffff;
  --sidebar-bg: #ffffff;
  --text-main: #1e293b;
  --text-dim: #64748b;
  --border: rgba(0, 0, 0, 0.05);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Plus Jakarta Sans', sans-serif;
  transition: background 0.3s ease, color 0.3s ease;
}

body {
  background: var(--bg-dark);
  color: var(--text-main);
  overflow-x: hidden;
}

.admin-layout {
  display: flex;
  min-height: 100vh;
}

/* SIDEBAR */
.sidebar {
  width: 260px;
  background: var(--sidebar-bg);
  backdrop-filter: blur(15px);
  border-right: 1px solid var(--border);
  padding: 30px 20px;
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
  height: 100vh;
}

.logo {
  font-size: 24px;
  font-weight: 800;
  color: var(--primary);
  margin-bottom: 40px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.sidebar a {
  text-decoration: none;
  color: var(--text-dim);
  padding: 12px 15px;
  border-radius: 12px;
  margin: 5px 0;
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 600;
  transition: 0.2s;
}

.sidebar a:hover, .sidebar a.active {
  background: rgba(168, 85, 247, 0.1);
  color: var(--primary);
}

.toggle-btn {
  margin-top: auto;
  padding: 14px;
  border: none;
  background: var(--primary);
  color: white;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.toggle-btn:hover { background: var(--primary-hover); }

/* MAIN CONTENT */
.main-content {
  flex: 1;
  padding: 40px;
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 35px;
}

.topbar h1 { font-size: 28px; font-weight: 700; }

.search-box {
  position: relative;
}

.search-box input {
  background: var(--card-bg);
  border: 1px solid var(--border);
  padding: 12px 20px 12px 45px;
  border-radius: 12px;
  color: var(--text-main);
  width: 300px;
  outline: none;
}

.search-box i {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-dim);
}

/* DASHBOARD CARDS */
.dashboard-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.dashboard-card {
  background: var(--card-bg);
  border: 1px solid var(--border);
  padding: 24px;
  border-radius: 20px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.dashboard-card i {
  font-size: 30px;
  color: var(--primary);
  background: rgba(168, 85, 247, 0.1);
  padding: 10px;
  border-radius: 12px;
}

.dashboard-card h3 {
  color: var(--text-dim);
  font-size: 14px;
  margin-top: 15px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.dashboard-card p {
  font-size: 26px;
  font-weight: 800;
  margin-top: 5px;
}

/* GRID LAYOUT */
.grid {
  display: grid;
  grid-template-columns: 1.5fr 1fr;
  gap: 25px;
}

.content-box {
  background: var(--card-bg);
  border: 1px solid var(--border);
  padding: 25px;
  border-radius: 20px;
}

.content-box h2 {
  font-size: 18px;
  margin-bottom: 20px;
}

/* TABLE STYLES */
table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0 8px;
}

th {
  color: var(--text-dim);
  font-weight: 600;
  font-size: 13px;
  text-transform: uppercase;
  padding: 10px;
}

td {
  padding: 12px 10px;
  background: rgba(255,255,255,0.02);
}

td:first-child { border-radius: 12px 0 0 12px; }
td:last-child { border-radius: 0 12px 12px 0; }

.avatar {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  border: 2px solid var(--primary);
}

.status-badge {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
}

.status-badge.active { background: rgba(34, 197, 94, 0.1); color: var(--success); }
.status-badge.expired { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

/* FILTERS */
.filters {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
}

.filters select {
  background: var(--bg-dark);
  color: var(--text-main);
  border: 1px solid var(--border);
  padding: 8px 15px;
  border-radius: 10px;
  outline: none;
}

.filters button {
  background: var(--primary);
  color: white;
  border: none;
  padding: 8px 20px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
}

@media (max-width: 1024px) {
  .grid { grid-template-columns: 1fr; }
  .sidebar { width: 80px; padding: 20px 10px; }
  .logo span, .sidebar a span, .toggle-btn span { display: none; }
}
.report-btn{
    background: var(--primary);
    color:white;
    padding:12px 18px;
    border-radius:12px;
    text-decoration:none;
    font-weight:700;
    display:inline-flex;
    gap:8px;
    align-items:center;
}

.report-btn:hover{
    background: var(--primary-hover);
}
</style>
</head>

<body>

<div class="admin-layout">

  <aside class="sidebar">
    <div class="logo">
      <i class='bx bx-dumbbell'></i> <span>FLEXCORE</span>
    </div>

    <a href="#" class="active"><i class="active"></i> <span>Dashboard</span></a>
    <a href="adminmembers.php"><i class='bx bx-user'></i> <span>Members</span></a>
    <a href="adminshop.php"><i class='bx bx-store'></i> <span>Shop</span></a>
    <a href="logout.php"><i class='bx bx-logout'></i> <span>Logout</span></a>
    <a href="adminorders.php"><i class='bx bx-order'></i> <span>Orders</span></a>

    <button class="toggle-btn" onclick="toggleTheme()">
      <i class='bx bx-moon'></i> <span>Toggle Mode</span>
    </button>
  </aside>

  <main class="main-content">

    <div class="topbar">
      <h1>Dashboard Overview</h1>
      <div class="search-box">
        <i class='bx bx-search'></i>
        <input type="text" placeholder="Search members...">
      </div>
    </div>
<a href="report.php" target="_blank" class="report-btn">
   <i class='bx bx-printer'></i> Generate Report
</a>  
   <div class="dashboard-cards">
  <div class="dashboard-card">
    <i class='bx bx-user'></i>
    <h3>Total Members</h3>
    <p><?php echo $totalMembers; ?></p>
  </div>

  <div class="dashboard-card">
    <i class='bx bx-crown'></i>
    <h3>Premium</h3>
    <p><?php echo $premium; ?></p>
  </div>

  <div class="dashboard-card">
    <i class='bx bx-cart'></i>
    <h3>Shop Sales</h3>
    <p>₱<?php echo number_format($sales, 2); ?></p>
  </div>

  <div class="dashboard-card">
    <i class='bx bx-line-chart'></i>
    <h3>Revenue</h3>
    <p>₱<?php echo number_format($sales, 2); ?></p>
  </div>
</div>
    <div class="grid">
      <div class="content-box">
        <h2>Recent Members</h2>

        <div class="filters">
          <select id="filterType">
            <option value="All">Membership</option>
            <option value="Half Month">Half Month</option>
            <option value="Monthly">Monthly</option>
            <option value="Premium">Premium</option>
          </select>

          <select id="filterStatus">
            <option value="All">Status</option>
            <option value="Active">Active</option>
            <option value="Expired">Expired</option>
          </select>

          <button onclick="applyFilters()">Filter</button>
        </div>

        <table>
  <thead>
    <tr>
      <th>User</th>
      <th>Name</th>
      <th>Plan</th>
      <th>Status</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($members as $m): ?>
    <tr>
      <td>
        <img src="https://i.pravatar.cc/150?u=<?php echo $m['id']; ?>" class="avatar">
      </td>

      <td style="font-weight:600">
        <?php echo $m['fullname']; ?>
      </td>

      <td style="color:var(--text-dim)">
        <?php echo ucfirst($m['role']); ?>
      </td>

      <td>
        <span class="status-badge active">Active</span>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
      </div>

      <div class="content-box">
        <h2>Revenue Analytics</h2>
        <div style="height: 300px;">
          <canvas id="salesChart"></canvas>
        </div>
      </div>

    </div>
  </main>
</div>
<script>
// ==========================
// THEME TOGGLE ONLY
// ==========================
function toggleTheme(){
  document.body.classList.toggle("light-mode");
  const icon = document.querySelector('.toggle-btn i');

  if(document.body.classList.contains('light-mode')) {
    icon.classList.replace('bx-moon', 'bx-sun');
  } else {
    icon.classList.replace('bx-sun', 'bx-moon');
  }
}
</script>
</script>

</body>
</html>