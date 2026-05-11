<?php
$conn = new mysqli("localhost","root","","flexcore_db");
if($conn->connect_error){
    die(json_encode([]));
}

$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

header("Content-Type: application/json");
echo json_encode($data);
?>