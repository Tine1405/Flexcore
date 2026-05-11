<?php
// adminmembers.php

$conn = new mysqli("localhost", "root", "", "flexcore_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users from database
$sql = "SELECT id, fullname, role, image FROM users ORDER BY fullname ASC";
$result = $conn->query($sql);

$members = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // If image is null or empty, use placeholder
        if (empty($row['image'])) {
            $row['image'] = "https://i.pravatar.cc/150?u=" . $row['id'];
        }
        $members[] = $row;
    }
}

// Convert PHP array to JSON for JavaScript
$members_json = json_encode($members);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Flexcore | Manage Members</title>

<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet" />

<style>
:root {
  --primary: #a855f7;
  --primary-hover: #9333ea;
  --bg-dark: #0f172a;
  --card-bg: rgba(30, 41, 59, 0.4);
  --sidebar-bg: rgba(15, 23, 42, 0.9);
  --text-main: #f8fafc;
  --text-dim: #94a3b8;
  --border: rgba(255, 255, 255, 0.08);
  --success: #22c55e;
  --danger: #ef4444;
}

* {
  margin: 0; padding: 0; box-sizing: border-box;
  font-family: 'Plus Jakarta Sans', sans-serif;
}

body {
  background: var(--bg-dark);
  color: var(--text-main);
  min-height: 100vh;
}

.admin-layout { display: flex; }

/* SIDEBAR */
.sidebar {
  width: 260px;
  height: 100vh;
  background: var(--sidebar-bg);
  backdrop-filter: blur(12px);
  border-right: 1px solid var(--border);
  padding: 30px 20px;
  position: sticky; top: 0;
}

.logo {
  font-size: 22px; font-weight: 800; color: var(--primary);
  margin-bottom: 40px; display: flex; align-items: center; gap: 10px;
}

.sidebar a {
  text-decoration: none; color: var(--text-dim);
  padding: 12px 15px; border-radius: 12px;
  margin: 8px 0; display: flex; align-items: center; gap: 12px;
  font-weight: 600; transition: 0.3s;
}

.sidebar a:hover, .sidebar a.active {
  background: rgba(168, 85, 247, 0.1);
  color: var(--primary);
}

/* MAIN CONTENT */
.main { flex: 1; padding: 40px; }

.topbar {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 30px;
}

.search-box { position: relative; }
.search-box input {
  background: var(--card-bg); border: 1px solid var(--border);
  padding: 12px 15px 12px 40px; border-radius: 12px;
  color: #fff; width: 300px; outline: none;
}
.search-box i { position: absolute; left: 15px; top: 14px; color: var(--text-dim); }

/* TABLE BOX */
.table-box {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: 20px; padding: 25px;
  backdrop-filter: blur(10px);
}

.header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 25px;
}

.manage-btn {
  background: var(--primary); border: none;
  padding: 12px 20px; border-radius: 12px;
  color: #fff; font-weight: 700; cursor: pointer;
  display: flex; align-items: center; gap: 8px; transition: 0.3s;
}
.manage-btn:hover { background: var(--primary-hover); transform: translateY(-2px); }

/* TABLE */
table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
th { color: var(--text-dim); text-align: left; padding: 10px; font-size: 13px; text-transform: uppercase; }
td { padding: 15px 10px; background: rgba(255,255,255,0.02); vertical-align: middle; }
td:first-child { border-radius: 12px 0 0 12px; }
td:last-child { border-radius: 0 12px 12px 0; }

.avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); }

/* STATUS PILLS */
.status-pill {
  padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
  user-select: none;
}
.status-pill.active { background: rgba(34, 197, 94, 0.1); color: var(--success); }
.status-pill.inactive { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

/* ACTIONS */
.actions { display: flex; gap: 10px; }
.actions i {
  cursor: pointer; font-size: 18px; padding: 8px;
  border-radius: 8px; background: rgba(255,255,255,0.05); transition: 0.2s;
  user-select: none;
}
.actions i:hover { color: var(--primary); background: rgba(168, 85, 247, 0.1); }
.actions .bx-trash:hover { color: var(--danger); background: rgba(239, 68, 68, 0.1); }

/* MODAL STYLES */
.modal-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.7); display: none; align-items: center; justify-content: center;
  z-index: 1000;
}
.modal {
  background: #1e293b; padding: 30px; border-radius: 20px; width: 400px;
  border: 1px solid var(--border);
}
.modal h2 { margin-bottom: 20px; }
.modal input, .modal select {
  width: 100%; padding: 12px; margin-bottom: 15px;
  background: #0f172a; border: 1px solid var(--border);
  color: #fff; border-radius: 10px; outline: none;
}
.modal-btns { display: flex; gap: 10px; }
.modal-btns button { flex: 1; padding: 12px; border-radius: 10px; cursor: pointer; border: none; font-weight: 700; }
.save-btn { background: var(--primary); color: #fff; }
.cancel-btn { background: rgba(255,255,255,0.1); color: #fff; }
</style>
</head>

<body>

<div class="modal-overlay" id="userModal">
  <div class="modal">
    <h2 id="modalTitle">Add New Member</h2>
    <input type="text" id="userName" placeholder="Full Name" />
    <select id="userRole">
      <option value="user">User</option>
      <option value="admin">Admin</option>
    </select>
    <div class="modal-btns">
      <button class="cancel-btn" onclick="closeModal()">Cancel</button>
      <button class="save-btn" onclick="saveUser()">Save Member</button>
    </div>
  </div>
</div>

<div class="admin-layout">

  <aside class="sidebar">
    <h2 class="logo"><i class="bx bx-dumbbell"></i> FLEXCORE</h2>
    <a href="admindashboad.php"><i class="bx bx-grid-alt"></i> Dashboard</a>
    <a href="adminmembers.php" class="active"><i class="bx bx-user"></i> Members</a>
    <a href="adminshop.php"><i class="bx bx-store"></i> Shop</a>
    <a href="adminorders.php"><i class="bx bx-order"></i> Order</a>
  </aside>

  <div class="main">
    <div class="topbar">
      <h1>Manage Members</h1>
      <div class="search-box">
        <i class="bx bx-search"></i>
        <input type="text" placeholder="Search members..." onkeyup="searchUser(this.value)" />
      </div>
    </div>

    <div class="table-box">
      <div class="header">
        <h2>Member List</h2>
        <button class="manage-btn" onclick="openModal()"><i class="bx bx-plus"></i> Add Member</button>
      </div>

      <table>
        <thead>
          <tr>
            <th>Profile</th>
            <th>Full Name</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="userTable"></tbody>
      </table>
    </div>
  </div>

</div>

<script>
// Parse PHP members data to JS
let users = <?php echo $members_json; ?>;

let editingIndex = -1;

function renderTable(data) {
  const table = document.getElementById("userTable");
  if (data.length === 0) {
    table.innerHTML = "<tr><td colspan='5' style='text-align:center; padding: 40px; color: #94a3b8;'>No members found.</td></tr>";
    return;
  }
  table.innerHTML = "";

  data.forEach((u, index) => {
    // We treat 'user' role as active, others inactive (adjust if you want)
    const status = (u.role === "user") ? "Active" : "Inactive";
    table.innerHTML += `
      <tr>
        <td><img src="${u.image}" class="avatar" alt="Profile image" /></td>
        <td style="font-weight:700">${u.fullname}</td>
        <td style="color: var(--text-dim)">${u.role}</td>
        <td><span class="status-pill ${status.toLowerCase()}">${status}</span></td>
        <td class="actions">
          <i class="bx bx-edit" title="Edit" onclick="prepareEdit(${index})"></i>
          <i class="bx bx-refresh" title="Toggle Status" onclick="toggleStatus(${index})"></i>
          <i class="bx bx-trash" title="Delete" onclick="deleteUser(${index})"></i>
        </td>
      </tr>
    `;
  });
}

function openModal() {
  editingIndex = -1;
  document.getElementById("modalTitle").innerText = "Add New Member";
  document.getElementById("userName").value = "";
  document.getElementById("userRole").value = "user";
  document.getElementById("userModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("userModal").style.display = "none";
}

function prepareEdit(index) {
  editingIndex = index;
  const user = users[index];
  document.getElementById("modalTitle").innerText = "Edit Member";
  document.getElementById("userName").value = user.fullname;
  document.getElementById("userRole").value = user.role;
  document.getElementById("userModal").style.display = "flex";
}

function saveUser() {
  const name = document.getElementById("userName").value.trim();
  const role = document.getElementById("userRole").value;

  if (!name) {
    alert("Please enter a full name.");
    return;
  }

  if (editingIndex === -1) {
    // New user - simulate ID and default image
    const newUser = {
      id: Date.now(),
      fullname: name,
      role,
      image: "https://i.pravatar.cc/150?u=" + Date.now(),
    };
    users.push(newUser);
  } else {
    // Edit existing user
    users[editingIndex].fullname = name;
    users[editingIndex].role = role;
  }

  closeModal();
  renderTable(users);

  // TODO: Add AJAX to save to server DB if desired
}

function deleteUser(index) {
  if (confirm("Permanently remove this member?")) {
    users.splice(index, 1);
    renderTable(users);

    // TODO: Add AJAX to delete from server DB if desired
  }
}

function toggleStatus(index) {
  // For demo, toggle between 'user' and 'admin' roles to simulate status
  if (users[index].role === "user") users[index].role = "admin";
  else users[index].role = "user";
  renderTable(users);

  // TODO: Add AJAX to update status on server DB if desired
}

function searchUser(value) {
  const filtered = users.filter((u) => u.fullname.toLowerCase().includes(value.toLowerCase()));
  renderTable(filtered);
}

// Initial table render
renderTable(users);
</script>

</body>
</html>