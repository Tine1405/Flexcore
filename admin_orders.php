<?php
$conn = new mysqli("localhost","root","","flexcore_db");

$res = $conn->query("SELECT * FROM orders ORDER BY id DESC");

$orders = [];

while($row = $res->fetch_assoc()){
    $orders[] = $row;
}

echo json_encode($orders);
?>