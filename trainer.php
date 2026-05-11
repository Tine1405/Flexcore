<?php
session_start();
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all trainers from the database
$trainer_query = "SELECT id, fullname, username, role FROM users WHERE role = 'trainer'";
$trainer_result = $conn->query($trainer_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FLEXCORE | Trainers</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #a855f7;
            --primary-dark: #9333ea;
            --bg-dark: #09090b;
            --card-bg: rgba(24, 24, 27, 0.9);
            --text-main: #fafafa;
            --text-dim: #a1a1aa;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Inter', sans-serif; }

        body {
            background: radial-gradient(circle at 0% 0%, #1e1b4b 0%, #09090b 50%);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
        }

        
        .navbar {
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            padding: 20px 80px; 
            position: sticky; 
            top: 0; z-index: 1000;
            backdrop-filter: blur(12px); 
            background: rgba(9, 9, 11, 0.8);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .logo { 
            font-weight: 800; 
            font-size: 26px; 
            color: var(--primary); 
            letter-spacing: 2px; 
        }
        .nav-links a {
            color: var(--text-dim); 
            text-decoration: none; 
            margin-left: 30px; 
            font-size: 14px; 
            font-weight: 600; 
            transition: var(--transition); 
            text-transform: uppercase;
        }
        .nav-links a:hover { 
            color: var(--primary); 
        }

        
        .hero { 
            padding: 100px 20px 40px; 
            text-align: center; 
        }
        .hero h1 { 
            font-size: clamp(40px, 8vw, 72px); 
            line-height: 1; 
            margin-bottom: 24px;
            background: linear-gradient(to bottom, #fff 30%, var(--primary));
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
            font-weight: 800;
        }
        .hero p { 
            color: var(--text-dim); 
            font-size: 18px; 
            max-width: 600px; 
            margin: 0 auto; 
        }

        
        .trainer-section { 
            padding: 40px 20px 100px; 
            max-width: 1200px; 
            margin: 0 auto; 
        }
        .trainer-grid {
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px; 
            padding: 20px;
        }

        .trainer-card {
            background: var(--card-bg); 
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px; 
            padding: 50px 30px 40px; 
            text-align: center;
            transition: var(--transition); 
            display: flex; flex-direction: column; 
            align-items: center;
        }
        .trainer-card:hover {
            transform: translateY(-12px); 
            border-color: var(--primary);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        .trainer-img {
            width: 110px; 
            height: 110px;
            background: linear-gradient(45deg, var(--primary), #4f46e5);
            border-radius: 50%; 
            margin-bottom: 25px;
            display: flex; 
            align-items: center; 
            justify-content: center;
            font-size: 32px; 
            font-weight: 800; 
            color: white;
        }

        .specialization { 
            color: var(--primary); 
            font-weight: 700; 
            font-size: 12px; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            margin-bottom: 12px; 
        }
        .trainer-card h3 { 
            font-size: 24px; 
            margin-bottom: 15px; 
        }
        
        .trainer-card ul { 
            list-style: none; 
            margin-bottom: 30px; 
            text-align: left; 
            width: 100%; 
        }
        .trainer-card li { 
            color: var(--text-dim); 
            margin-bottom: 10px; 
            font-size: 14px; 
        }
        .trainer-card li::before {
         content: "✓ "; 
         color: var(--primary); 
         font-weight: bold; 
     }

        .avail-btn {
            background: var(--primary); 
            color: white; 
            border: none; 
            padding: 16px;
            border-radius: 14px; 
            font-weight: 800; 
            cursor: pointer; 
            width: 100%;
            transition: var(--transition); 
            text-transform: uppercase;
        }
        .avail-btn:hover { 
            background: white; 
            color: var(--primary-dark); 
        }

        
        #paymentModal {
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0;
            width: 100%; 
            height: 100%; 
            background: rgba(0,0,0,0.9);
            backdrop-filter: blur(8px); 
            justify-content: center; 
            align-items: center; 
            z-index: 2000;
        }
        .modal-content {
            background: #111; 
            padding: 40px; 
            border-radius: 24px;
            width: 90%; 
            max-width: 400px;
            border: 1px solid #333; 
            text-align: center;
        }

        select, input {
            width: 100%; 
            padding: 14px; 
            margin-bottom: 20px;
            background: #1a1a1a; 
            border: 1px solid #333; 
            color: white; 
            border-radius: 10px;
        }

        @media (max-width: 768px) { .navbar { padding: 20px; } .nav-links { display: none; } }
    </style>
</head>
<body>

    <header class="navbar">
        <div class="logo">FLEXCORE</div>
        <nav class="nav-links">
             <a href="index.php">Dashboard</a>
        <a href="aboutus.php">About US</a>
        <a href="shop.php">Shop</a>
        <a href="membership.php">Membership</a>
        <a href="trainer.php">Trainers</a>
        <a href="personal.php">My Account</a>
        <a href="logout.php">Logout</a>
        </nav>
    </header>

    <section class="hero">
        <h1>HIRE AN EXPERT</h1>
        <p>Your goals, their expertise. Choose a trainer to unlock personalized programs and 24/7 guidance.</p>
    </section>

    <section class="trainer-section">
        <div class="trainer-grid">
            <?php 
            if ($trainer_result->num_rows > 0) {
                while($trainer = $trainer_result->fetch_assoc()) {
                    
                    $names = explode(" ", $trainer['fullname']);
                    $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ""));
            ?>
                <div class="trainer-card">
                    <div class="trainer-img"><?php echo $initials; ?></div>
                    <div class="specialization">Elite Coach</div>
                    <h3><?php echo htmlspecialchars($trainer['fullname']); ?></h3>
                    <ul>
                        <li>Custom Workout Plans</li>
                        <li>Direct Messaging Access</li>
                        <li>Progress Tracking</li>
                        <li>Dietary Guidelines</li>
                    </ul>
                    <button class="avail-btn" onclick="openModal('<?php echo addslashes($trainer['fullname']); ?>', '<?php echo $trainer['id']; ?>')">
                        Avail Now
                    </button>
                </div>
            <?php 
                }
            } else {
                echo "<p style='text-align:center; width:100%; color:var(--text-dim);'>No trainers available at the moment.</p>";
            }
            ?>
        </div>
    </section>

   <div id="paymentModal">
    <div class="modal-content">
        <h3 style="margin-bottom: 10px;">Confirm Trainer</h3>
        <p id="trainerNameDisplay" style="color: var(--primary); font-weight: 800; margin-bottom: 20px;"></p>
        
        <form action="select_trainer.php" method="POST">
            <input type="hidden" name="trainer_id" id="trainerIdInput">
            
            <label style="display:block; text-align:left; color:var(--text-dim); font-size:12px; margin-bottom:5px;">Payment Method</label>
            <select name="payment_method" id="paymentMethodSelect" required>
                <option value="">Select Method</option>
                <option value="GCash">GCash</option>
                <option value="Card">Credit/Debit Card</option>
                <option value="Cash">Cash at Gym</option>
            </select>

            <div id="gcashQR" style="display:none; margin-bottom:20px; background: #fff; padding: 15px; border-radius: 15px; text-align: center;">
                <p style="color:#333; font-size:12px; margin-bottom:10px; font-weight: bold;">Scan to pay via GCash</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=FLEXCORE_TRAINER_PAY" alt="QR" style="width:150px;">
            </div>

            <button type="submit" name="confirm_booking" class="avail-btn">Confirm & Pay</button>
        </form>
        
        <button onclick="closeModal()" style="background:none; border:none; color:#666; margin-top:15px; cursor:pointer;">Cancel</button>
    </div>
</div>

    <script>
    const modal = document.getElementById('paymentModal');
    const nameDisplay = document.getElementById('trainerNameDisplay');
    const idInput = document.getElementById('trainerIdInput');
    const paymentSelect = document.getElementById('paymentMethodSelect');
    const gcashQR = document.getElementById('gcashQR');

    // Function to open the modal
    window.openModal = function(name, id) {
        modal.style.display = 'flex';
        nameDisplay.innerText = "Booking with " + name;
        idInput.value = id;
        

        paymentSelect.value = "";
        gcashQR.style.display = "none";
    }


    window.closeModal = function() {
        modal.style.display = 'none';
    }


    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }

 
    if (paymentSelect) {
        paymentSelect.addEventListener("change", function() {
            if (this.value === "GCash") {
                gcashQR.style.display = "block";
            } else {
                gcashQR.style.display = "none";
            }
        });
    }
</script>
    </script>
</body>
</html>