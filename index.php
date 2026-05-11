<?php
session_start();
require_once "db.php";//connector to database

if (!isset($_SESSION['user_id'])) {//ip(issit(pera_session['usr_id']))
    header("Location: index.php");//hed("loc")
    exit();//exit
}

$user_id = $_SESSION['user_id'];

//gets the trainer assignment
$query = "SELECT u.fullname, (SELECT coach_announcement FROM users WHERE id = ?) as announcement 
          FROM trainer_assignments ta 
          JOIN users u ON ta.trainer_id = u.id 
          WHERE ta.user_id = ? LIMIT 1";

//prepairs the statement
$stmt = $conn->prepare($query);


$stmt->bind_param("ii",$user_id, $user_id);//$statement->bind_param("ii",perauser_idx2with,)

//execute statement
$stmt->execute();
$result = $stmt->get_result();

    
$data = $result->fetch_assoc();

$assigned_trainer = $data['fullname'] ?? null;
$announcement = $data['announcement'] ?? "No updates from your coach yet.";//if no data, this appear 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FLEXCORE | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #a855f7;
            --primary-dark: #9333ea;
            --bg-dark: #09090b;
            --card-bg: rgba(24, 24, 27, 0.8);
            --text-main: #fafafa;
            --text-dim: #a1a1aa;
        }

        * { margin: 0; padding: 0; 
            box-sizing: border-box; 
            font-family: 'Inter', sans-serif; }

        body {
            background: radial-gradient(circle at 0% 0%, #1e1b4b 0%, #09090b 50%);
            color: var(--text-main);
            line-height: 1.6;
            overflow-x: hidden;
        }

      
        .navbar {
            display: flex; 
            justify-content: space-between;
             align-items: center;
            padding: 20px 80px; 
            position: sticky; 
            top: 0; z-index: 1000;
            backdrop-filter: blur(10px); 
            background: rgba(9, 9, 11, 0.8);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .logo { 
        font-weight: 800;
         font-size: 24px; 
         color: var(--primary); 
        }
        .nav-links a { 
        color: var(--text-dim); 
        text-decoration: none; 
        margin-left: 30px; 
        font-size: 14px; 
        font-weight: 600; 
        transition: 0.3s; 
        }
        .nav-links a:hover { 
        color: var(--primary); 
        }

        
        .hero {
        height: 60vh;
        display: flex; 
        flex-direction: column; 
        justify-content: center; 
        align-items: center; 
        text-align: center; 
        padding: 0 20px;
        }
        .hero h1 { 
            font-size: clamp(40px, 8vw, 80px); 
            line-height: 1; 
            margin-bottom: 20px;
            background: linear-gradient(to right, #fff, var(--primary));
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent;
        }
        .hero p { 
        color: var(--text-dim); 
        font-size: 20px; 
        max-width: 600px; 
        margin-bottom: 30px; 
    }

        .announcement-container { 
        max-width: 1200px; 
        margin: 0 auto 50px; 
        padding: 0 80px; 
    }
        .coach-briefing {
            background: var(--card-bg); 
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 20px; 
            padding: 25px; display: flex; 
            align-items: center; 
            gap: 20px;
            backdrop-filter: blur(10px);
        }
        .status-dot { 
            width: 12px; 
            height: 12px; 
            border-radius: 50%; 
            background: var(--primary); 
            box-shadow: 0 0 10px var(--primary); 
        }
        .announcement-text-box {
            border-left: 1px solid rgba(255,255,255,0.1);
            padding-left: 20px;
            margin-left: 20px;
        }

        
        .slider-container {
            width: 100%; 
            max-width: 1200px; 
            margin: 50px auto; 
            height: 500px;
            border-radius: 30px; 
            overflow: hidden; 
            position: relative;
        }
        .slides {
            display: flex;
            width: 300%; 
            height: 100%;
            animation: slideAnim 15s infinite;
        }
        .slide {
            width: 33.3333%; 
            height: 100%;
            position: relative;
        }
        .slide img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            filter: brightness(0.4); 
        }
        .slide-text { 
            position: absolute; 
            bottom: 50px; 
            left: 50px; 
            z-index: 2; 
        }
        .slide-text h2 { 
            font-size: 42px; 
            color: white; 
            font-weight: 800; 
        }

        @keyframes slideAnim {
            0%   { transform: translateX(0); }
            30%  { transform: translateX(0); }
            33%  { transform: translateX(-33.3333%); }
            63%  { transform: translateX(-33.3333%); }
            66%  { transform: translateX(-66.6666%); }
            96%  { transform: translateX(-66.6666%); }
            100% { transform: translateX(0); }
        }

        .cta-btn {
            padding: 18px 40px; 
            background: var(--primary); 
            color: white;
            border: none; 
            border-radius: 50px; 
            font-weight: 700; 
            cursor: pointer; 
            transition: 0.3s;
        }
        .cta-btn:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 10px 30px rgba(168, 85, 247, 0.4); 
        }

        .split-section { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 50px; 
            padding: 100px 80px; 
            align-items: center; 
        }
        .split-text h1 { 
            font-size: 48px; 
            margin-bottom: 20px; 
        }
        .split-image img { 
            width: 100%; 
            border-radius: 20px; 
            border: 1px solid var(--primary); 
        }

        .footer { padding: 80px;
         background: #050505; 
         border-top: 1px solid #111; 
         display: grid; 
         grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
         gap: 40px; 
     }
        
        @media (max-width: 768px){ 
            .split-section { grid-template-columns: 1fr; padding: 40px; 
            } 
            .navbar { 
                padding: 20px; 
            }
            .announcement-container { 
                padding: 0 20px; 
            }
            .coach-briefing { 
                flex-direction: column; 
                text-align: center; 
            }
            .announcement-text-box { 
                border-left: none;
                border-top: 1px solid rgba(255,255,255,0.1); 
                padding-left: 0; 
                padding-top: 15px; 
                margin-left: 0; 
            }
        }
    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">FLEXCORE.</div>
    <nav class="nav-links">
        <a href="index.php">Dashboard</a>
        <a href="aboutus.php">About us</a>
        <a href="shop.php">Shop</a>
        <a href="membership.php">Membership</a>
        <a href="trainer.php">Trainers</a>
        <a href="personal.php">My Account</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<section class="hero">
    <h1>WELCOME, <?php echo htmlspecialchars($_SESSION['username']); ?>.</h1>
    <p>Your fitness journey continues. Push your limits and track every milestone.</p>
    <button class="cta-btn" onclick="window.open('https://musclewiki.com/', '_blank')">START WORKOUT</button>
</section>

<div class="announcement-container">
    <div class="coach-briefing">
        <div class="status-dot"></div>
        <div style="min-width: 180px;">
            <h4 style="font-size: 10px; color: var(--primary); letter-spacing: 2px; text-transform: uppercase; font-weight: 800;">Coach Assigned</h4>
            <p style="font-size: 16px; font-weight: 700;">
                <?php echo ($assigned_trainer) ? htmlspecialchars($assigned_trainer) : "Waiting for trainer..."; ?>
            </p>
        </div>
        
        <div class="announcement-text-box" style="flex: 1;">
            <h4 style="font-size: 10px; color: var(--text-dim); letter-spacing: 2px; text-transform: uppercase; margin-bottom: 5px;">Latest Announcement</h4>
            <p style="font-size: 14px; font-weight: 400; color: #fff; font-style: italic;">
                "<?php echo htmlspecialchars($announcement); ?>"
            </p>
        </div>
        
        <div style="font-size: 20px;">⚡</div>
    </div>
</div>

<div class="slider-container">
    <div class="slides">
        <div class="slide">
            <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1200" alt="Gym">
            <div class="slide-text"><h2>DISCIPLINE BEATS MOTIVATION</h2><p>Consistency is everything.</p></div>
        </div>
        <div class="slide">
            <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1200" alt="Gym">
            <div class="slide-text"><h2>CONSISTENCY IS KEY</h2><p>Small steps every day.</p></div>
        </div>
        <div class="slide">
            <img src="https://images.unsplash.com/photo-1581009146145-b5ef03a7403f?auto=format&fit=crop&w=1200" alt="Gym">
            <div class="slide-text"><h2>NO EXCUSES</h2><p>Make it happen.</p></div>
        </div>
    </div>
</div>

<section class="split-section">
    <div class="split-text">
        <h1>GET INSPIRED.<br>GO FURTHER.</h1>
        <p style="color: var(--text-dim); margin-bottom: 20px;">Elite coaching and premium equipment waiting for you. Discover why thousands choose Flexcore.</p>
        <a href="membership.php" style="text-decoration: none;">
            <button class="cta-btn" style="background: transparent; border: 2px solid var(--primary);">VIEW PLANS</button>
        </a>
    </div>
    <div class="split-image">
        <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=800" alt="Trainer">
    </div>
</section>

<footer class="footer">
    <div class="footer-col"><h3>FLEXCORE</h3><p>Your ultimate fitness management system.</p></div>
    <div class="footer-col"><h4>Contact</h4><p>Email: flexcore@email.com</p></div>
</footer>

</body>
</html>