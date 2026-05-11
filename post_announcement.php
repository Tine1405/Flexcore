<?php
session_start();
require_once "db.php";

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: index.php");
    exit();
}

$trainer_id = $_SESSION['user_id'];
$message = "";

// --- HANDLE SAVING / UPDATING ---
if (isset($_POST['save_announcement'])) {
    $client_id = $_POST['client_id'];
    $announcement = $_POST['announcement'];

    $update_sql = "UPDATE users SET coach_announcement = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $announcement, $client_id);
    
    if ($stmt->execute()) {
        $message = "Announcement updated successfully!";
    }
}

// --- FETCH CLIENTS FOR THE DROPDOWN ---
$clients_query = "SELECT u.id, u.fullname, u.coach_announcement FROM users u 
                  JOIN trainer_assignments ta ON u.id = ta.user_id 
                  WHERE ta.trainer_id = ?";
$stmt_c = $conn->prepare($clients_query);
$stmt_c->bind_param("i", $trainer_id);
$stmt_c->execute();
$clients_result = $stmt_c->get_result();

// Store clients in an array to use for the JavaScript "Edit" feature
$clients_data = [];
while($row = $clients_result->fetch_assoc()) {
    $clients_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FLEXCORE | Manage Announcements</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #a855f7; --bg: #09090b; --card: #18181b; }
        body { background: var(--bg); color: white; font-family: 'Inter', sans-serif; padding: 40px; }
        
        .container { max-width: 600px; margin: auto; }
        .card { background: var(--card); padding: 30px; border-radius: 24px; border: 1px solid rgba(168, 85, 247, 0.2); }
        
        h2 { margin-bottom: 20px; color: var(--primary); }
        label { display: block; margin-bottom: 8px; font-size: 14px; color: #a1a1aa; }
        
        select, textarea { 
            width: 100%; padding: 15px; margin-bottom: 20px; 
            background: #09090b; border: 1px solid #333; 
            color: white; border-radius: 12px; font-size: 16px;
        }
        
        .btn-save { 
            background: var(--primary); color: white; border: none; 
            padding: 15px; border-radius: 12px; width: 100%; 
            font-weight: 800; cursor: pointer; transition: 0.3s;
        }
        .btn-save:hover { background: #9333ea; transform: translateY(-2px); }
        
        .alert { background: rgba(34, 197, 94, 0.2); color: #22c55e; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" style="color: #a1a1aa; text-decoration: none; font-size: 14px;">← Back to Dashboard</a>
    <br><br>

    <div class="card">
        <h2>Client Announcements</h2>
        
        <?php if($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Select Client to Edit</label>
            <select name="client_id" id="clientSelect" onchange="updateTextArea()" required>
                <option value="">-- Choose a Client --</option>
                <?php foreach($clients_data as $client): ?>
                    <option value="<?php echo $client['id']; ?>" data-msg="<?php echo htmlspecialchars($client['coach_announcement']); ?>">
                        <?php echo htmlspecialchars($client['fullname']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Announcement Message</label>
            <textarea name="announcement" id="announcementBox" rows="6" placeholder="Type your instructions here..."></textarea>

            <button type="submit" name="save_announcement" class="btn-save">Save Announcement</button>
        </form>
    </div>
</div>

<script>
    function updateTextArea() {
        const select = document.getElementById('clientSelect');
        const textArea = document.getElementById('announcementBox');
        
        // Get the data-msg attribute from the selected option
        const selectedOption = select.options[select.selectedIndex];
        const existingMessage = selectedOption.getAttribute('data-msg');
        
        // Populate the textarea with the existing message
        textArea.value = existingMessage || "";
    }
</script>

</body>
</html>