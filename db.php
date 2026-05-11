<?php 
//$conn = nsqli ("server", "root","", "dbname")
$conn = new mysqli("localhost", "root", "", "flexcore_db"); 

//->connect_error check if connection failed
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error);//the "die" . peraconnect-> stop everthing 
 } ?>
