<?php
session_start();
//connector to db
include "db.php";

//Gets user input
$fullname = $_POST['fullname'];
$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);//$pass= pass_hash (pera_PST['pas'],pass_def)
$role = $_POST['role'] ?? 'user';// $rol=$_pos['rol'] ??'usr'

//user image
$imageName = $_FILES['image']['name'];//$imgNme=$=FLS['img']['nam']
$tmpName = $_FILES['image']['tmp_name'];//$tmpNme=$_FLS['img']['tmp_nam']
$folder = "uploads/" . $imageName;//perapolder="uplods/".$imgNam
move_uploaded_file($tmpName, $folder);//move_upload_file($tmpNam,$fol)

//insert to database
$sql = "INSERT INTO users (fullname, username, email, password, image, role)
        VALUES ('$fullname', '$username', '$email', '$password', '$imageName', '$role')";//fname,uname,email,pass,imgnam,rol

//if connector->to query($sql))
if ($conn->query($sql)) {

    $_SESSION['username'] = $username;
    $_SESSION['fullname'] = $fullname;
    $_SESSION['image'] = $imageName;
    $_SESSION['role'] = $role;

    //redirect/auto login
    if ($role === "admin") {
        header("Location: admindashboard.php");
    } else {
        header("Location: index.php");
    }

    exit();

} else {
    echo "Error: " . $conn->error;//ech"oeror.$connector->er
}
?>