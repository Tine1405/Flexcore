<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FLEXCORE | Our Team</title>

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

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}

body {
    background: radial-gradient(circle at 0% 0%, #1e1b4b 0%, #09090b 50%);
    color: var(--text-main);
    line-height: 1.6;
    min-height: 100vh;
}

/* NAVBAR */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 80px;
    position: sticky;
    top: 0;
    z-index: 1000;
    backdrop-filter: blur(10px);
    background: rgba(9, 9, 11, 0.8);
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.logo {
    font-weight: 800;
    font-size: 24px;
    color: var(--primary);
}

/* FIXED NAV LINKS */
.nav-links a {
    color: var(--text-dim);
    text-decoration: none;
    margin-left: 25px;
    font-size: 14px;
    transition: 0.3s;
}

.nav-links a:hover {
    color: var(--primary);
}

/* HEADER */
.header-section {
    text-align: center;
    padding: 80px 20px 40px;
}

.header-section h1 {
    font-size: clamp(30px, 5vw, 60px);
    background: linear-gradient(to right, #fff, var(--primary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 800;
    margin-bottom: 10px;
}

.header-section p {
    color: var(--text-dim);
    font-size: 18px;
}

/* TEAM GRID */
.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 80px;
}

.member-card {
    background: var(--card-bg);
    border: 1px solid rgba(168, 85, 247, 0.1);
    border-radius: 24px;
    padding: 30px;
    text-align: center;
    backdrop-filter: blur(12px);
    transition: all 0.4s ease;
}

.member-card:hover {
    transform: translateY(-10px);
    border-color: var(--primary);
    box-shadow: 0 20px 40px rgba(168, 85, 247, 0.15);
}

.img-container {
    position: relative;
    width: 130px;
    height: 130px;
    margin: 0 auto 20px;
}

.member-img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--primary);
    padding: 5px;
}

.status-dot {
    position: absolute;
    bottom: 5px;
    right: 10px;
    width: 15px;
    height: 15px;
    background: var(--primary);
    border-radius: 50%;
    border: 3px solid #18181b;
    box-shadow: 0 0 10px var(--primary);
}

.member-card h3 {
    font-size: 20px;
    margin-bottom: 5px;
}

.role {
    color: var(--primary);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: 800;
    display: block;
    margin-bottom: 15px;
}

.bio {
    color: var(--text-dim);
    font-size: 14px;
}

/* MOBILE FIX */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        gap: 10px;
        padding: 20px;
    }

    .nav-links {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .nav-links a {
        margin: 10px;
    }

    .team-grid {
        padding: 20px;
    }

}
/* MODAL BACKDROP */
.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(6px);
}

/* MODAL BOX */
.modal-content {
    background: #18181b;
    margin: 8% auto;
    padding: 30px;
    width: 350px;
    border-radius: 20px;
    text-align: center;
    position: relative;
    border: 1px solid rgba(168, 85, 247, 0.3);
    animation: pop 0.3s ease;
}

@keyframes pop {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.modal-content img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #a855f7;
    margin-bottom: 15px;
}

.modal-content h2 {
    margin: 10px 0 5px;
}

.modal-content span {
    color: #a855f7;
    font-size: 12px;
    font-weight: bold;
    display: block;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.modal-content p {
    color: #a1a1aa;
    font-size: 14px;
}


.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 22px;
    cursor: pointer;
    color: #fff;
}
</style>
</head>

<body>

<header class="navbar">
    <div class="logo">FLEXCORE.</div>
    <nav class="nav-links">
        <a href="index.php">Dashboard</a>
        <a href="aboutus.php">About Us</a>
        <a href="shop.php">Shop</a>
        <a href="membership.php">Membership</a>
        <a href="trainer.php">Trainers</a>
        <a href="personal.php">My Account</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<section class="header-section">
    <h1>THE DEV SQUAD</h1>
    <p>The 2 IT students behind the system.</p>
</section>

<div class="team-grid">

    <div class="member-card"
     data-name="Jamil Salvatierra"
     data-role="UI/UX Specialist"
     data-bio="Focuses on UI/UX design."
     data-age="20"
     data-loc=" Binalonan PAngasinan"
     data-img="jam.jpg">
    <div class="member-card">
        <div class="img-container">
            <img src="jam.jpg" alt="Jamil Salvatierra" class="member-img">
            <div class="status-dot"></div>
        </div>
        <span class="role">FRONTEND</span>
        <h3>Jamil Salvatierra</h3>
        <p class="bio">Focuses on UI/UX design together with the frontend team</p>
    </div>
</div>
    
    <div class="member-card"
     data-name="Justine Repoyo"
     data-role="Backend"
     data-bio="Worked with all of the members. Gives the idea of FLEXCORE and flow of it"
     data-age="20"
     data-loc="San Felipe Central Binalonan PAngasinan"
     data-img="tine.png">
    <div class="member-card">
        <div class="img-container">
            <img src="tine.png" alt="Justine Repoyo" class="member-img">
            <div class="status-dot"></div>
        </div>
        <span class="role">Backend</span>
        <h3>Justine Repoyo</h3>
        <p class="bio">Created the original FLEXCORE concept and leads system building and testing.</p>
    </div>
</div>


</div>

<div id="memberModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <img id="modalImg" src="" alt="">
        <h2 id="modalName"></h2>
        <span id="modalRole"></span>
        <p id="modalBio"></p>
        <p id="modalAge"></p>
        <p id="modalLoc"></p>
    </div>
</div>
<script>
const modal = document.getElementById("memberModal");
const closeBtn = document.querySelector(".close");

const modalImg = document.getElementById("modalImg");
const modalName = document.getElementById("modalName");
const modalRole = document.getElementById("modalRole");
const modalBio = document.getElementById("modalBio");
const modalAge = document.getElementById("modalAge");
const modalLoc = document.getElementById("modalLoc");

document.querySelectorAll(".member-card").forEach(card => {
    card.addEventListener("click", () => {

        modalImg.src = card.dataset.img;
        modalName.innerText = card.dataset.name;
        modalRole.innerText = card.dataset.role;
        modalBio.innerText = card.dataset.bio;
        modalAge.innerText = "Age: " + card.dataset.age;
        modalLoc.innerText = "Location: " + card.dataset.loc;

        modal.style.display = "block";
    });
});

closeBtn.onclick = () => {
    modal.style.display = "none";
};

window.onclick = (e) => {
    if (e.target === modal) {
        modal.style.display = "none";
    }
};
</script>
</body>
</html>