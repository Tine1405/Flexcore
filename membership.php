<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
//peramess=""
$message = "";
if (isset($_GET['success'])) { //ip(isser(pera_GET['sucss']))
    $message = "Membership successfully updated!";//peramessage="tugon"
} elseif (isset($_GET['error'])) {//ilseip(isset(pera_GET['errr']))
    $message = "Something went wrong. Please try again.";//peratugon
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Plans | FLEXCORE</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            overflow-x: hidden;
        }
        
        #paymentModal {
            display: none;
             position: fixed;
            top: 0; 
            left: 0;
            width: 100%; 
            height: 100%;
            background: rgba(0,0,0,0.85);
            justify-content: center;
            align-items: center;
            z-index: 9999; /* 🔥 increase */
        }

        .modal-content {
            background: #111;
            padding: 30px;
            border-radius: 15px;
            width: 320px;
            text-align: center;
            border: 1px solid #333;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }

        .membership-btn {
            cursor: pointer;
            transition: transform 0.2s;
        }

        .membership-btn:active {
            transform: scale(0.95);
        }

        .alert-box {
            background: #22c55e; 
            color: white;
            padding: 15px; 
            border-radius: 8px; 
            margin: 20px auto; 
            max-width: 600px;
            text-align: center;
        }
        .membership-card {
    position: relative;
    z-index: 1;
}

.membership-btn {
    position: relative;
    z-index: 2;
}
#alertBox {
    transition: opacity 0.5s ease, transform 0.5s ease;
}
    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">FLEXCORE</div>
    <div class="nav-links">
         <a href="index.php">Dashboard</a>
        <a href="aboutus.php">About US</a>
        <a href="shop.php">Shop</a>
        <a href="membership.php">Membership</a>
        <a href="trainer.php">Trainers</a>
        <a href="personal.php">My Account</a>
        <a href="logout.php">Logout</a>
    </div>
</header>

<section class="membership-page">

    <div class="membership-hero">
        <h1>Choose Your Membership</h1>
        <p>Start your fitness journey today and unlock your full potential</p>
    </div>

    <?php if($message): ?>
    <div id="alertBox" class="alert-box" style="<?php echo isset($_GET['error']) ? 'background:#ef4444;' : ''; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

    <div class="membership-container">

        <div class="membership-card">
            <h3>Half Month</h3>
            <p class="price">₱300</p>
            <ul>
                <li>✔ Weight Lifting Only</li>
                <li>✔ 15 Days Access</li>
                <li>✔ Basic Equipment</li>
            </ul>
            <button type="button" class="membership-btn" onclick="openPayment('Half Month')">
                Select Plan
            </button>
        </div>

        <div class="membership-card">
            <h3>Monthly</h3>
            <p class="price">₱500</p>
            <ul>
                <li>✔ Weight Lifting Only</li>
                <li>✔ 30 Days Access</li>
                <li>✔ Full Gym Hours</li>
            </ul>
            <button type="button" class="membership-btn" onclick="openPayment('Monthly')">
                Select Plan
            </button>
        </div>

        <div class="membership-card featured">
            <h3>Premium</h3>
            <p class="price">₱800</p>
            <ul>
                <li>✔ All Equipment Access</li>
                <li>✔ Unlimited Gym Use</li>
                <li>✔ Priority Access</li>
            </ul>
            <button type="button" class="membership-btn" onclick="openPayment('Premium')">
                Select Plan
            </button>
        </div>
    </div>
</section>

<div id="paymentModal">
    <div class="modal-content">
        <h3 style="color:white; margin-bottom: 5px;">Payment</h3>
        <p id="selectedPlanDisplay" style="color:#a855f7; font-weight:bold; margin-bottom: 20px;"></p>

        <form action="select_membership.php" method="POST">
            <input type="hidden" name="plan" id="planInput">
            
            <label style="color: #ccc; font-size: 14px; display: block; text-align: left; margin-bottom: 5px;">Payment Method:</label>
            <select name="payment" id="paymentMethod" required style="width:100%; padding:12px; border-radius:8px; background:#222; color:white; border:1px solid #444; margin-bottom: 20px;">
    
            <option value="">Choose Method</option>
            <option value="GCash">GCash</option>
            <option value="Cash">Cash</option>
            <option value="Card">Card</option>
        </select>

            <div id="gcashQR" style="display:none; margin-bottom:15px;">
             <p style="color:#ccc; font-size:14px; margin-bottom:10px;"> Scan to pay via GCash</p>
            <img src="qr.jpg" alt="GCash QR" style="width:180px; border-radius:10px;">
            </div>

            <button type="submit" name="submit_payment" style="width:100%; padding:12px; background:#a855f7; color:white; border:none; border-radius:8px; cursor:pointer; font-weight:bold;">
                Confirm & Pay
            </button>
        </form>

        <button onclick="closeModal()" style="margin-top:10px; background:transparent; color:#888; padding:10px; width:100%; border:none; cursor:pointer;">
            Cancel
        </button>
    </div>
</div>

<section class="motivation">
    <h2>"Invest in your body. It pays the best interest."</h2>
</section>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-col">
            <h3>FLEXCORE</h3>
            <p>Your ultimate gym system.</p>
        </div>
        <div class="footer-col">
            <h4>Contact</h4>
            <p>Email: flexcore@email.com</p>
            <p>Phone: +63 912 345 6789</p>
        </div>
    </div>
</footer>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const modal = document.getElementById("paymentModal");
    const planInput = document.getElementById("planInput");
    const planDisplay = document.getElementById("selectedPlanDisplay");
    const paymentSelect = document.getElementById("paymentMethod");
    const gcashQR = document.getElementById("gcashQR");
    const alertBox = document.getElementById("alertBox");

    window.openPayment = function(planName) {
        planInput.value = planName;
        planDisplay.innerText = planName + " Plan";
        modal.style.display = "flex";
    }


    window.closeModal = function() {
        modal.style.display = "none";
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

    if (alertBox) {
        setTimeout(() => {
            alertBox.style.opacity = "0";
            alertBox.style.transform = "translateY(-10px)";
        }, 1000); // fade after 1 sec

        setTimeout(() => {
            alertBox.style.display = "none";
        }, 1500); // remove after fade
    }

});
</script>
</body>
</html>