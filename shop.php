<?php
session_start();
$conn = new mysqli("localhost","root","","flexcore_db");
if($conn->connect_error){
    die("DB Error: ".$conn->connect_error);
}
$result = $conn->query("SELECT * FROM products");
$products = [];
while($row = $result->fetch_assoc()){
    $products[] = $row; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FLEXCORE | Premium Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<style>
:root {
    --primary: #a855f7;
    --primary-hover: #9333ea;
    --glow: rgba(168, 85, 247, 0.4);
    --bg: #0f172a;
    --card: rgba(30, 41, 59, 0.7);
    --surface: #1e293b;
    --text: #f8fafc;
    --dim: #94a3b8;
    --green: #22c55e;
    --border: rgba(255, 255, 255, 0.08);
}

* { margin:0; padding:0; box-sizing:border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

body {
    background: var(--bg);
    color: var(--text);
    line-height: 1.6;
}

/* NAVBAR */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 5%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid var(--border);
}

.logo { font-size: 24px; font-weight: 800; color: var(--primary); letter-spacing: -1px; }

.navbar nav a {
    color: var(--dim);
    text-decoration: none;
    margin-left: 20px;
    font-size: 14px;
    font-weight: 600;
    transition: 0.3s;
}

.navbar nav a:hover, .navbar nav a.active { color: var(--primary); }

/* HERO */
.hero {
    padding: 60px 5%;
    background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url('hero.jpg') center/cover;
    text-align: center;
}

.hero h1 { font-size: 42px; font-weight: 800; margin-bottom: 10px; }
.hero p { color: var(--dim); font-size: 18px; }

/* LAYOUT wrapper */
.wrapper {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 30px;
    padding: 40px 5%;
    max-width: 1400px;
    margin: 0 auto;
}

@media (max-width: 992px) { .wrapper { grid-template-columns: 1fr; } }

/* CATEGORIES */
.filter-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 30px;
    overflow-x: auto;
    padding-bottom: 10px;
}

.cat-btn {
    padding: 10px 20px;
    border-radius: 30px;
    background: var(--surface);
    color: var(--dim);
    cursor: pointer;
    white-space: nowrap;
    transition: 0.3s;
    border: 1px solid var(--border);
    font-size: 14px;
    font-weight: 600;
}

.cat-btn:hover, .cat-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
    box-shadow: 0 4px 15px var(--glow);
}

/* PRODUCT GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 25px;
}

.card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 20px;
    transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}

.card:hover {
    transform: translateY(-8px);
    border-color: var(--primary);
    background: rgba(30, 41, 59, 0.9);
}

.card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 15px;
    margin-bottom: 15px;
}

.card h4 { font-size: 18px; margin-bottom: 5px; }
.card small { font-size: 16px; color: var(--green); font-weight: 700; display: block; margin-bottom: 15px; }

/* CART & SIDEBAR */
.cart-sidebar {
    background: var(--surface);
    padding: 25px;
    border-radius: 24px;
    height: fit-content;
    position: sticky;
    top: 100px;
    border: 1px solid var(--border);
}

.cart-sidebar h3 { font-size: 22px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }

.cart-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border);
}

.cart-item img { width: 50px; height: 50px; border-radius: 10px; object-fit: cover; }
.cart-item-info { flex: 1; }
.cart-item-info p { font-size: 14px; font-weight: 600; }

/* INPUTS & BUTTONS */
.qty-input {
    width: 45px;
    background: var(--bg);
    border: 1px solid var(--border);
    color: white;
    padding: 5px;
    border-radius: 5px;
    text-align: center;
}

.btn-primary {
    width: 100%;
    padding: 14px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 10px;
}

.btn-primary:hover { background: var(--primary-hover); box-shadow: 0 0 20px var(--glow); }

.btn-outline {
    background: transparent;
    border: 1px solid var(--green);
    color: var(--green);
}

/* MODALS */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.85);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 2000;
    backdrop-filter: blur(5px);
}

.modal-content {
    background: var(--surface);
    padding: 30px;
    border-radius: 24px;
    width: 100%;
    max-width: 400px;
    text-align: center;
    border: 1px solid var(--border);
}

/* NOTIFICATION */
.notif {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--primary);
    color: white;
    padding: 12px 30px;
    border-radius: 50px;
    font-weight: 600;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    display: none;
    z-index: 3000;
}

.history-box {
    margin-top: 25px;
    max-height: 200px;
    overflow-y: auto;
    font-size: 13px;
    padding-right: 5px;
}

.history-item {
    background: rgba(0,0,0,0.2);
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 8px;
    border-left: 3px solid var(--primary);
}
</style>
</head>
<body>

<div class="navbar">
    <div class="logo">FLEXCORE</div>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="shop.php" class="active">Shop</a>
        <a href="membership.php">Membership</a>
        <a href="personal.php">Account</a>
        <a href="logout.php"><i class='bx bx-log-out'></i></a>
    </nav>
</div>

<div class="hero">
    <h1>Fitness Arsenal</h1>
    <p>Premium supplements and gear for elite performance.</p>
</div>

<div class="wrapper">
    <main>
        <div class="filter-bar">
            <div class="cat-btn active" onclick="filterCat('All',this)">All Products</div>
            <div class="cat-btn" onclick="filterCat('Supplements',this)">Supplements</div>
            <div class="cat-btn" onclick="filterCat('Accessories',this)">Accessories</div>
            <div class="cat-btn" onclick="filterCat('Snacks',this)">Protein Snacks</div>
        </div>
        <div class="grid" id="productGrid"></div>
    </main>

    <aside class="cart-sidebar">
        <h3><i class='bx bx-shopping-bag'></i> Your Cart</h3>
        <div id="cart">
            <p style="text-align:center; color:var(--dim); padding: 20px 0;">Cart is empty</p>
        </div>
        
        <div style="margin-top: 20px; border-top: 1px solid var(--border); padding-top: 20px;">
            <div style="display:flex; justify-content:space-between; margin-bottom: 20px;">
                <span style="color:var(--dim)">Total Amount</span>
                <span id="total" style="font-weight:800; font-size: 20px;">₱0</span>
            </div>
            <button class="btn-primary" onclick="checkout()"><i class='bx bx-credit-card'></i> Checkout Now</button>
            <button class="btn-primary btn-outline" onclick="openTrackModal()"><i class='bx bx-map-pin'></i> Track Order</button>
        </div>

        <div class="history">
            <h4 style="margin: 20px 0 10px; font-size: 14px; text-transform: uppercase; color:var(--dim)">Recent Orders</h4>
            <div id="history" class="history-box"></div>
        </div>
    </aside>
</div>

<!-- NOTIFICATION -->
<div class="notif" id="notif"></div>

<!-- PAYMENT MODAL -->
<div class="modal-overlay" id="paymentModal">
    <div class="modal-content">
        <h3>Payment Method</h3>
        <p style="color:var(--dim); margin-bottom: 20px;">Choose how you want to pay</p>
        
        <select id="paymentMethod" onchange="handlePaymentChange()" style="width:100%; padding:12px; border-radius:10px; background:var(--bg); color:white; border:1px solid var(--border); margin-bottom: 15px;">
            <option value="Cash">Cash on Delivery</option>
            <option value="GCash">GCash Digital Pay</option>
        </select>

        <div id="gcashBox" style="display:none; padding: 15px; background: rgba(0,0,0,0.2); border-radius: 15px; margin-bottom: 15px;">
            <p style="font-size: 12px; margin-bottom: 10px;">Scan QR to complete transfer</p>
            <img src="qr.jpg" style="width:160px; border-radius: 10px; border: 4px solid white;">
            <button class="btn-primary" style="margin-top:15px;" onclick="confirmGCash()">Confirm Payment</button>
        </div>

        <button class="btn-primary" onclick="confirmCash()" id="cashConfirmBtn">Confirm Order</button>
        <button class="btn-primary" style="background:#334155;" onclick="closeModal()">Cancel</button>
    </div>
</div>

<!-- TRACK MODAL -->
<div class="modal-overlay" id="trackModal">
    <div class="modal-content">
        <h3><i class='bx bx-search-alt'></i> Track Order</h3>
        <input id="trackInput" placeholder="Enter Tracking Code" style="width:100%; padding:12px; border-radius:10px; background:var(--bg); color:white; border:1px solid var(--border); margin: 20px 0;">
        <button class="btn-primary" onclick="trackOrder()">Search Status</button>
        <div id="trackResult" style="margin-top:15px;"></div>
        <button class="btn-primary" style="background:#334155; margin-top:10px;" onclick="closeTrackModal()">Close</button>
    </div>
</div>

<script>
let products = <?php echo json_encode($products); ?>;
let cart = [];
let currentCategory = "All";

function loadProducts(){
    let html = "";
    products.forEach(p=>{
        if(currentCategory !== "All" && p.category !== currentCategory) return;
        html += `
            <div class="card">
                <img src="${p.image}">
                <h4>${p.name}</h4>
                <small>₱${p.price.toLocaleString()}</small>
                <button class="btn-primary" onclick="add('${p.name}','${p.image}',${p.price})">
                    <i class='bx bx-plus'></i> Add to Cart
                </button>
            </div>
        `;
    });
    document.getElementById("productGrid").innerHTML = html;
}

function add(name,img,price){
    let item = cart.find(i=>i.name===name);
    if(item) item.qty++;
    else cart.push({name,img,price,qty:1});
    notify("Added: " + name);
    renderCart();
}

function renderCart(){
    let html="", total=0;
    if(cart.length === 0) {
        html = '<p style="text-align:center; color:var(--dim); padding: 20px 0;">Cart is empty</p>';
    } else {
        cart.forEach((c,i)=>{
            total += c.price*c.qty;
            html += `
                <div class="cart-item">
                    <img src="${c.img}">
                    <div class="cart-item-info">
                        <p>${c.name}</p>
                        <span style="font-size:12px; color:var(--green)">₱${c.price}</span>
                    </div>
                    <input class="qty-input" type="number" min="1" value="${c.qty}" onchange="updateQty(${i},this.value)">
                </div>
            `;
        });
    }
    document.getElementById("cart").innerHTML = html;
    document.getElementById("total").innerText = "₱"+total.toLocaleString();
}

function updateQty(i,v){
    cart[i].qty = Math.max(1, parseInt(v));
    renderCart();
}

function checkout(){
    if(cart.length === 0){ notify("Please add items first!"); return; }
    document.getElementById("paymentModal").style.display = "flex";
}

function closeModal(){ document.getElementById("paymentModal").style.display = "none"; }

function loadHistory(){
    fetch("get_orders.php")
    .then(r=>r.json())
    .then(data=>{
        let html="";
        data.forEach(o=>{
            let items=[];
            try{items=JSON.parse(o.items);}catch(e){}
            html += `
                <div class="history-item">
                    <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                        <b>#${o.tracking_code}</b>
                        <span style="color:var(--primary)">₱${o.total}</span>
                    </div>
                    <div style="font-size:11px; color:var(--dim)">
                        Status: <span style="color:var(--text)">${o.status}</span> • ${o.payment_method}<br>
                        ${items.map(i=>i.name+" x"+i.qty).join(", ")}
                    </div>
                </div>
            `;
        });
        document.getElementById("history").innerHTML = html || '<p style="font-size:12px; color:var(--dim)">No recent purchases.</p>';
    });
}

function notify(msg){
    let n=document.getElementById("notif");
    n.innerText=msg;
    n.style.display="block";
    setTimeout(()=>n.style.display="none",3000);
}

function filterCat(cat, el){
    currentCategory = cat;
    document.querySelectorAll(".cat-btn").forEach(b=>b.classList.remove("active"));
    el.classList.add("active");
    loadProducts();
}

function handlePaymentChange(){
    let method = document.getElementById("paymentMethod").value;
    document.getElementById("gcashBox").style.display = method === "GCash" ? "block" : "none";
    document.getElementById("cashConfirmBtn").style.display = method === "GCash" ? "none" : "block";
}

function confirmCash(){ processOrder("Cash"); closeModal(); }

function confirmGCash(){
    notify("Verifying transaction...");
    setTimeout(()=>{ processOrder("GCash"); closeModal(); },1500);
}

function processOrder(method){
    fetch("checkout.php",{
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body:JSON.stringify({
            items:cart,
            total:cart.reduce((a,b)=>a+b.price*b.qty,0),
            method:method
        })
    })
    .then(r=>r.json())
    .then((data)=>{
        notify("Order Placed Successfully!");
        cart=[];
        renderCart();
        loadHistory();
    });
}

function openTrackModal(){ document.getElementById("trackModal").style.display = "flex"; }
function closeTrackModal(){ document.getElementById("trackModal").style.display = "none"; }

function trackOrder(){
    let code = document.getElementById("trackInput").value;
    if(!code) return;
    fetch("track.php?code=" + code)
    .then(r=>r.json())
    .then(data=>{
        if(!data || !data.status){
            document.getElementById("trackResult").innerHTML = "<p style='color:red'>Invalid Code</p>";
            return;
        }
        document.getElementById("trackResult").innerHTML = `
            <div style="background:rgba(0,0,0,0.2); padding:15px; border-radius:10px;">
                <p>Status: <b style="color:var(--primary)">${data.status}</b></p>
                <small style="color:var(--dim)">Last Update: ${data.updated_at || 'Just now'}</small>
            </div>
        `;
    });
}

window.onload = ()=>{ loadProducts(); loadHistory(); }
</script>

</body>
</html>