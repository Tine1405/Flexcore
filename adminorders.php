<?php
$conn = new mysqli("localhost","root","","flexcore_db");
if($conn->connect_error){
    die("DB Error: ".$conn->connect_error);
}

$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FLEXCORE | Order Management</title>

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #a855f7;
            --primary-light: rgba(168, 85, 247, 0.1);
            --bg: #0f172a;
            --sidebar: #1e293b;
            --card: rgba(30, 41, 59, 0.5);
            --text: #f8fafc;
            --dim: #94a3b8;
            --border: rgba(255, 255, 255, 0.06);
            --green: #22c55e;
            --yellow: #facc15;
            --blue: #3b82f6;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background-color: var(--bg);
            background-image: radial-gradient(circle at 50% 0%, rgba(168, 85, 247, 0.05) 0%, transparent 50%);
            color: var(--text);
            min-height: 100vh;
        }

        .layout { display: flex; }

        /* SIDEBAR REDESIGN */
        .sidebar {
            width: 280px;
            height: 100vh;
            background: var(--sidebar);
            border-right: 1px solid var(--border);
            padding: 30px 20px;
            position: sticky;
            top: 0;
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
            padding-left: 10px;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            color: var(--dim);
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 8px;
            transition: 0.3s;
            font-weight: 500;
        }

        .nav-links a i { font-size: 20px; }

        .nav-links a:hover, .nav-links a.active {
            background: var(--primary-light);
            color: var(--primary);
        }

        .nav-links a.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(168, 85, 247, 0.3);
        }

        /* MAIN CONTENT */
        .main { flex: 1; padding: 40px; }

        .header {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .header h2 { font-size: 32px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { color: var(--dim); margin-top: 5px; }

        /* TABLE STYLING */
        .table-container {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: rgba(15, 23, 42, 0.3);
            padding: 18px 24px;
            color: var(--dim);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        td {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        tr:last-child td { border-bottom: none; }

        .tracking-code { font-family: monospace; font-size: 14px; color: var(--primary); font-weight: 600; }
        .price { font-weight: 700; color: var(--text); }
        .items-list { color: var(--dim); font-size: 13px; max-width: 250px; line-height: 1.4; }

        /* STATUS BADGES */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .pending { background: rgba(250, 204, 21, 0.1); color: var(--yellow); }
        .pending::before { background: var(--yellow); }

        .processing { background: rgba(59, 130, 246, 0.1); color: var(--blue); }
        .processing::before { background: var(--blue); }

        .shipped { background: rgba(168, 85, 247, 0.1); color: var(--primary); }
        .shipped::before { background: var(--primary); }

        .delivered { background: rgba(34, 197, 94, 0.1); color: var(--green); }
        .delivered::before { background: var(--green); }

        /* CUSTOM SELECT */
        .status-select {
            background: #0f172a;
            color: #fff;
            border: 1px solid var(--border);
            padding: 8px 12px;
            border-radius: 8px;
            outline: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .status-select:focus { border-color: var(--primary); }

        .action-btn {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
            padding: 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .action-btn:hover { background: var(--primary); border-color: var(--primary); }
    </style>
</head>

<body>

<div class="layout">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="logo">
            <i class='bx bxs-bolt-circle'></i> FLEXCORE
        </div>
        <nav class="nav-links">
            <a href="admindashboad.php"><i class='bx bx-grid-alt'></i> Dashboard</a>
            <a href="adminmembers.php"><i class='bx bx-group'></i> Members</a>
            <a href="adminshop.php"><i class='bx bx-store-alt'></i> Shop</a>
            <a href="adminorders.php" class="active"><i class='bx bx-package'></i> Orders</a>
        </nav>
    </aside>

    <!-- MAIN -->
    <main class="main">
        <div class="header">
            <div>
                <h2>Orders</h2>
                <p>Monitor and manage customer fulfillment status</p>
            </div>
            <div class="stats">
                <!-- Optional: Add quick summary stats here -->
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tracking</th>
                        <th>Products</th>
                        <th>Payment</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($o = $result->fetch_assoc()): ?>
                        <?php $items = json_decode($o['items'], true); ?>
                        <tr>
                            <td><span class="tracking-code">#<?= $o['tracking_code'] ?></span></td>
                            <td>
                                <div class="items-list">
                                    <?php 
                                    if(is_array($items)){
                                        echo implode(", ", array_map(fn($i)=>$i['name']." (x".$i['qty'].")", $items));
                                    }
                                    ?>
                                </div>
                            </td>
                            <td><span style="font-size: 14px;"><?= $o['payment_method'] ?></span></td>
                            <td><span class="price">₱<?= number_format($o['total'], 2) ?></span></td>
                            <td>
                                <span class="badge <?= strtolower($o['status']) ?>">
                                    <?= $o['status'] ?>
                                </span>
                            </td>
                            <td>
                                <select class="status-select" onchange="updateStatus(<?= $o['id'] ?>, this.value)">
                                    <option <?= $o['status']=="Pending"?"selected":"" ?>>Pending</option>
                                    <option <?= $o['status']=="Processing"?"selected":"" ?>>Processing</option>
                                    <option <?= $o['status']=="Shipped"?"selected":"" ?>>Shipped</option>
                                    <option <?= $o['status']=="Delivered"?"selected":"" ?>>Delivered</option>
                                </select>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
function updateStatus(id, status){
    // Optional: Add a loading state UI here
    fetch("update_status.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({id: id, status: status})
    })
    .then(r => r.json())
    .then(data => {
        // Simple reload to show updated badges
        location.reload();
    })
    .catch(err => {
        alert("Failed to update status. Please try again.");
    });
}
</script>

</body>
</html>