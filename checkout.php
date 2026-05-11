<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","flexcore_db");

if($conn->connect_error){
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// ✅ safety checks (prevents undefined errors)
if(!isset($data["items"]) || !isset($data["total"]) || !isset($data["method"])){
    echo json_encode([
        "success" => false,
        "message" => "Invalid request data"
    ]);
    exit;
}

$items = json_encode($data["items"]);
$total = floatval($data["total"]);
$method = $data["method"];

// ✅ tracking + status
$tracking = "FXC-" . strtoupper(uniqid());
$status = "Pending";

// ✅ FIXED PREPARED STATEMENT (correct types)
$stmt = $conn->prepare("
INSERT INTO orders (items, total, payment_method, status, tracking_code)
VALUES (?, ?, ?, ?, ?)
");

if(!$stmt){
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit;
}

// ✔ correct bind types: string, double, string, string, string
$stmt->bind_param("sdsss", $items, $total, $method, $status, $tracking);

$success = $stmt->execute();

if($success){
    echo json_encode([
        "success" => true,
        "tracking" => $tracking
    ]);
}else{
    echo json_encode([
        "success" => false,
        "message" => "Insert failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>