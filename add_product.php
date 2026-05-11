<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"));

$conn->query("INSERT INTO products(name,category,price,image)
VALUES('$data->name','$data->category','$data->price','$data->image')");

echo json_encode(["status"=>"added"]);
?>