<?php
$conn = new mysqli("localhost","root","","flexcore_db");

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"];
$status = $data["status"];

$stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

echo json_encode(["success"=>true]);
?>