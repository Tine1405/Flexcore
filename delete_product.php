<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"));

$conn->query("DELETE FROM products WHERE id=$data->id");

echo json_encode(["status"=>"deleted"]);
?>