<?php
$conn = new mysqli("localhost","root","","flexcore_db");
if($conn->connect_error){
    die(json_encode([]));
}

$result = $conn->query("SELECT * FROM products");
$products = [];
while($row = $result->fetch_assoc()){
    $products[] = $row;
}

header('Content-Type: application/json');
echo json_encode($products);
?>