<?php
$conn = new mysqli("localhost", "root", "", "flexcore_db");
if ($conn->connect_error) {
    die("DB Error: " . $conn->connect_error);
}

// --- ADD PRODUCT ---
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $targetFile;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products(name,category,price,image) VALUES (?,?,?,?)");
    $stmt->bind_param("ssds", $name, $category, $price, $image);
    $stmt->execute();
    header("Location: adminshop.php");
    exit();
}

// --- DELETE PRODUCT ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res && !empty($res['image']) && file_exists($res['image'])) {
        unlink($res['image']);
    }
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: adminshop.php");
    exit();
}

// --- EDIT PRODUCT ---
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = $_POST['old_image'] ?? '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            if (!empty($_POST['old_image']) && file_exists($_POST['old_image'])) {
                unlink($_POST['old_image']);
            }
            $image = $targetFile;
        }
    }

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, category=?, image=? WHERE id=?");
    $stmt->bind_param("sdssi", $name, $price, $category, $image, $id);
    $stmt->execute();
    header("Location: adminshop.php");
    exit();
}

$result = $conn->query("SELECT * FROM products");
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flexcore | Admin Shop</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #a855f7; --primary-glow: rgba(168, 85, 247, 0.4);
            --bg-dark: #0f172a; --card-bg: rgba(30, 41, 59, 0.4);
            --sidebar-bg: rgba(15, 23, 42, 0.9); --text-main: #f8fafc;
            --text-dim: #94a3b8; --border: rgba(255, 255, 255, 0.08);
            --blue: #3b82f6; --red: #ef4444; --green: #22c55e;
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans',sans-serif; }
        body { background:var(--bg-dark); color:var(--text-main); min-height:100vh; overflow-x: hidden; }
        .shop-layout { display:flex; min-height: 100vh; }
        .sidebar { width:260px; height:100vh; background:var(--sidebar-bg); backdrop-filter:blur(12px); border-right:1px solid var(--border); padding:30px 20px; position:sticky; top:0; flex-shrink: 0; }
        .logo { font-size:22px; font-weight:800; color:var(--primary); margin-bottom:40px; display:flex; align-items:center; gap:10px; }
        .sidebar a { text-decoration:none; color:var(--text-dim); padding:12px 15px; border-radius:12px; margin:8px 0; display:flex; align-items:center; gap:12px; font-weight:600; transition:0.3s; }
        .sidebar a:hover { background:rgba(168, 85, 247, 0.1); color:var(--primary); }
        .sidebar a.active { background:var(--primary); color:white; }
        .main { flex:1; padding:40px; min-width: 0; }
        .header-section { text-align:center; margin-bottom:40px; }
        .header-section h1 { font-size:28px; font-weight:800; margin-bottom:10px; }
        .category-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-bottom:40px; }
        .category-card { background:var(--card-bg); border:1px solid var(--border); padding:30px; border-radius:20px; text-align:center; cursor:pointer; transition:0.3s; }
        .category-card i { font-size:40px; color:var(--primary); margin-bottom:15px; display:block; }
        .category-card:hover, .category-card.active { transform:translateY(-5px); border-color:var(--primary); background:rgba(168,85,247,0.1); }
        .product-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:25px; }
        .product-card { background:var(--card-bg); border:1px solid var(--border); border-radius:20px; padding:20px; text-align:center; transition:0.3s; }
        .product-card img { width:100%; height:160px; object-fit:cover; border-radius:15px; margin-bottom:15px; background: #000; }
        .price-tag { display:inline-block; background:rgba(34,197,94,0.1); color:var(--green); padding:4px 12px; border-radius:20px; font-weight:700; margin-bottom:15px; }
        .btn-group { display:none; gap:10px; justify-content:center; margin-top: 10px; }
        .btn { padding:8px 16px; border:none; border-radius:10px; font-weight:600; cursor:pointer; font-size:13px; text-decoration: none; }
        .btn.edit { background:rgba(59,130,246,0.1); color:var(--blue); }
        .btn.delete { background:rgba(239,68,68,0.1); color:var(--red); }
        #productModal { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); display:none; justify-content:center; align-items:center; z-index:999; }
        .modal-content { background:#1e293b; padding:30px; border-radius:20px; width:400px; border: 1px solid var(--border); }
        .modal-content input, .modal-content select { width:100%; margin-bottom:15px; padding:12px; border-radius:10px; border:1px solid var(--border); background:#0f172a; color:#fff; }
        .manage-btn { padding:10px 20px; border:none; border-radius:12px; background:var(--primary); color:#fff; cursor:pointer; margin: 5px; }
    </style>
</head>
<body>

<div class="shop-layout">
    <aside class="sidebar">
        <div class="logo"><i class='bx bx-dumbbell'></i> FLEXCORE</div>
        <a href="admindashboad.php"><i class='bx bx-grid-alt'></i> Dashboard</a>
        <a href="adminmembers.php"><i class='bx bx-user'></i> Members</a>
        <a href="adminshop.php" class="active"><i class='bx bx-store'></i> Shop</a>
        <a href="adminorders.php"><i class='bx bx-package'></i> Orders</a>
    </aside>

    <div class="main">
        <div class="header-section">
            <h1>Shop Inventory</h1>
            <p>Manage your products and categories.</p>
            <div style="margin-top: 20px;">
                <button class="manage-btn" onclick="toggleAdminMode()">Toggle Admin Actions</button>
                <button id="addBtn" class="manage-btn" onclick="openModal()" style="background: var(--green);">+ Add New Product</button>
            </div>
        </div>

        <div class="category-grid">
            <div class="category-card active" onclick="filterCategory('All', this)">
                <i class='bx bx-category'></i>
                <h3>All Items</h3>
            </div>
            <div class="category-card" onclick="filterCategory('Supplements', this)">
                <i class='bx bx-capsule'></i>
                <h3>Supplements</h3>
            </div>
            <div class="category-card" onclick="filterCategory('Accessories', this)">
                <i class='bx bx-dumbbell'></i>
                <h3>Accessories</h3>
            </div>
        </div>

        <div id="productSection" class="product-grid">
            <!-- Products injected by JS -->
        </div>

        <div class="footer">© 2026 FLEXCORE Premium Fitness</div>
    </div>
</div>

<!-- PRODUCT MODAL -->
<div id="productModal">
    <div class="modal-content">
        <h3 id="modalTitle" style="margin-bottom:20px;">Add Product</h3>
        <form id="productForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="product_id" name="id">
            <input type="hidden" id="old_image" name="old_image">
            <input type="hidden" id="form_mode" name="add" value="1">
            
            <label>Product Name</label>
            <input id="pname" name="name" required>
            
            <label>Price (PHP)</label>
            <input id="pprice" name="price" type="number" step="0.01" required>
            
            <label>Category</label>
            <select id="pcategory" name="category" required>
                <option value="Supplements">Supplements</option>
                <option value="Accessories">Accessories</option>
                <option value="Snacks">Snacks</option>
            </select>
            
            <label>Product Image</label>
            <input id="pimage" name="image" type="file">
            
            <div style="display:flex; gap:10px; margin-top:10px;">
                <button type="submit" class="manage-btn" style="flex:1;">Save Product</button>
                <button type="button" class="manage-btn" onclick="closeModal()" style="background:#444;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
let allProducts = <?php echo json_encode($products); ?>;
let isAdminMode = false;

function renderProducts(filter = 'All') {
    const container = document.getElementById("productSection");
    let html = "";
    
    const filtered = filter === 'All' 
        ? allProducts 
        : allProducts.filter(p => p.category === filter);

    if(filtered.length === 0) {
        container.innerHTML = `<p style="grid-column: 1/-1; text-align:center; color:var(--text-dim);">No products found in this category.</p>`;
        return;
    }

    filtered.forEach(p => {
        html += `
            <div class="product-card">
                <img src="${p.image || 'https://via.placeholder.com/150'}" alt="${p.name}">
                <h4>${p.name}</h4>
                <div class="price-tag">₱${parseFloat(p.price).toLocaleString()}</div>
                <div class="btn-group" style="display: ${isAdminMode ? 'flex' : 'none'}">
                    <button class="btn edit" onclick="editProduct(${p.id})"><i class='bx bx-edit'></i> Edit</button>
                    <a href="?delete=${p.id}" class="btn delete" onclick="return confirm('Delete this product?')"><i class='bx bx-trash'></i></a>
                </div>
            </div>
        `;
    });
    container.innerHTML = html;
}

function filterCategory(cat, element) {
    document.querySelectorAll('.category-card').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
    renderProducts(cat);
}

function toggleAdminMode() {
    isAdminMode = !isAdminMode;
    renderProducts(document.querySelector('.category-card.active h3').innerText.replace(' Items', ''));
}

function openModal() {
    document.getElementById("modalTitle").innerText = "Add Product";
    document.getElementById("productForm").reset();
    document.getElementById("form_mode").name = "add";
    document.getElementById("productModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("productModal").style.display = "none";
}

function editProduct(id) {
    const p = allProducts.find(item => item.id == id);
    if(!p) return;

    document.getElementById("modalTitle").innerText = "Edit Product";
    document.getElementById("product_id").value = p.id;
    document.getElementById("pname").value = p.name;
    document.getElementById("pprice").value = p.price;
    document.getElementById("pcategory").value = p.category;
    document.getElementById("old_image").value = p.image;
    document.getElementById("form_mode").name = "edit";
    
    document.getElementById("productModal").style.display = "flex";
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target == document.getElementById("productModal")) closeModal();
}

// Initial Load
window.onload = () => renderProducts('All');
</script>

</body>
</html>