<?php
session_start();
require_once "db.php";

// 1. Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: login.php?error=unauthorized_trainer");
    exit();
}

$trainer_id = $_SESSION['user_id'];
$trainer_name = $_SESSION['username'];
$status_msg = "";

// 2. Handle the Post/Update logic on the same page
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_announcement'])) {
    $client_id = $_POST['user_id'];
    $message = $_POST['message'];

    $update_query = "UPDATE users SET coach_announcement = ? WHERE id = ?";
    $stmt_up = $conn->prepare($update_query);
    $stmt_up->bind_param("si", $message, $client_id);
    
    if ($stmt_up->execute()) {
        $status_msg = "Instructions updated successfully.";
    }
}

// 3. Fetch Assigned Clients (including their current announcement)
$query = "SELECT users.id, users.fullname, users.username, users.image, users.coach_announcement 
          FROM users 
          JOIN trainer_assignments ON users.id = trainer_assignments.user_id 
          WHERE trainer_assignments.trainer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$clients = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FLEXCORE | Trainer Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #a855f7;
            --primary-dark: #9333ea;
            --bg-dark: #09090b;
            --card-bg: rgba(24, 24, 27, 0.95);
            --text-main: #fafafa;
            --text-dim: #a1a1aa;
            --transition: all 0.3s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body {
            background: radial-gradient(circle at 0% 0%, #1e1b4b 0%, #09090b 50%);
            color: var(--text-main);
            min-height: 100vh;
            padding: 40px 5%;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
        }

        .header-area {
            grid-column: 1 / -1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .logo { font-weight: 800; font-size: 28px; color: var(--primary); letter-spacing: 2px; }

        .client-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .client-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            padding: 25px;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .client-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
        }

        .client-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .client-img {
            width: 65px;
            height: 65px;
            border-radius: 15px;
            object-fit: cover;
            border: 2px solid var(--primary);
            background: #222;
        }

        .client-info h4 { font-size: 20px; font-weight: 700; }
        .client-info p { color: var(--primary); font-size: 12px; font-weight: 800; text-transform: uppercase; }

        .announcement-section {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .announcement-section label {
            display: block;
            font-size: 11px;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .announcement-textarea {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 10px;
            color: #fff;
            font-size: 13px;
            resize: none;
            outline: none;
            transition: 0.3s;
            margin-bottom: 10px;
        }

        .announcement-textarea:focus { border-color: var(--primary); background: rgba(255, 255, 255, 0.1); }

        .post-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
            text-transform: uppercase;
        }

        .post-btn:hover { background: var(--primary-dark); box-shadow: 0 5px 15px rgba(168, 85, 247, 0.3); }

        .calendar-box {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.05);
            height: fit-content;
            position: sticky;
            top: 40px;
        }

        .fc .fc-toolbar-title { font-size: 16px !important; color: var(--primary); }
        .fc .fc-button-primary { background: var(--primary); border: none; }
        
        .toast {
            position: fixed; top: 20px; right: 20px; background: #10b981; color: white;
            padding: 15px 25px; border-radius: 10px; z-index: 2000; box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        @media (max-width: 1100px) {
            .dashboard-container { grid-template-columns: 1fr; }
            .calendar-box { position: static; }
        }
    </style>
</head>
<body>

    <?php if ($status_msg): ?>
        <div class="toast" id="statusToast"><?php echo $status_msg; ?></div>
        <script>setTimeout(() => { document.getElementById('statusToast').style.display = 'none'; }, 3000);</script>
    <?php endif; ?>

    <div class="header-area">
        <div class="logo">FLEXCORE</div>
        <div style="text-align: right;">
            <p style="font-weight: 800; color: white;">Coach <?php echo htmlspecialchars($trainer_name); ?></p>
            <p style="color: var(--text-dim); font-size: 12px;">Trainer Dashboard</p>
            <a href="logout.php" style="color: var(--primary); text-decoration: none; font-size: 11px; font-weight: bold;">LOGOUT</a>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="main-content">
            <h3 style="margin-bottom: 25px; font-weight: 800; letter-spacing: 1px;">MY CLIENTS</h3>
            
            <div class="client-grid">
                <?php if ($clients->num_rows > 0): ?>
                    <?php while($row = $clients->fetch_assoc()): ?>
                    <div class="client-card">
                        <div class="client-info">
                            <img src="uploads/<?php echo $row['image'] ?: 'default_avatar.jpg'; ?>" class="client-img" alt="Client">
                            <div>
                                <h4><?php echo htmlspecialchars($row['fullname']); ?></h4>
                                <p>@<?php echo htmlspecialchars($row['username']); ?></p>
                            </div>
                        </div>

                        <div class="announcement-section">
                            <label>Client Briefing</label>
                            <form method="POST">
                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                <textarea name="message" class="announcement-textarea" rows="3" placeholder="Post daily workout/diet..."><?php echo htmlspecialchars($row['coach_announcement']); ?></textarea>
                                <button type="submit" name="update_announcement" class="post-btn">Update Instructions</button>
                            </form>
                        </div>

                        <div style="font-size: 11px; color: var(--text-dim); display: flex; justify-content: space-between;">
                            <span>ID: #00<?php echo $row['id']; ?></span>
                            <span style="color: #10b981;">● Active Connection</span>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: var(--text-dim);">No clients currently assigned.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="calendar-box">
            <h3>Schedule</h3>
            <div id="calendar"></div>
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.05);">
                <h4 style="font-size: 14px; margin-bottom: 15px; color: var(--primary);">Program Tools</h4>
                <ul style="list-style: none; font-size: 12px; color: var(--text-dim);">
                    <li style="margin-bottom: 10px;">• Manage Subscriptions</li>
                    <li style="margin-bottom: 10px;">• View Progress Photos</li>
                    <li>• Export Workout Data</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: { left: 'prev', center: 'title', right: 'next' },
                height: 'auto'
            });
            calendar.render();
        });
    </script>
</body>
</html>