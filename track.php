<?php
$conn = new mysqli("localhost","root","","flexcore_db");

if($conn->connect_error){
    die(json_encode(["error"=>"DB error"]));
}

$code = $_GET["code"] ?? "";

$stmt = $conn->prepare("SELECT status, updated_at FROM orders WHERE tracking_code=?");
$stmt->bind_param("s", $code);
$stmt->execute();

$result = $stmt->get_result()->fetch_assoc();

echo json_encode($result ?: []);
?>