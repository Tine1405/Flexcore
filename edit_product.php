<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"));

$conn->query("UPDATE products SET 
name='$data->name',
price='$data->price',
category='$data->category',
image='$data->image'
WHERE id=$data->id");

echo json_encode(["status"=>"updated"]);
?>