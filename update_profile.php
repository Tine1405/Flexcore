<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];

// SAFE INPUTS
$fullname = $conn->real_escape_string($_POST['fullname']);
$username = $conn->real_escape_string($_POST['username']);
$email = $conn->real_escape_string($_POST['email']);
$weight = $conn->real_escape_string($_POST['weight']);
$height = $conn->real_escape_string($_POST['height']);
$goal = $conn->real_escape_string($_POST['goal']);
$program = $conn->real_escape_string($_POST['program']);

// PASSWORD (OPTIONAL)
$passwordSQL = "";

if (!empty($_POST['password']) && $_POST['password'] === $_POST['confirmPassword']) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $passwordSQL = ", password='$password'";
}

// IMAGE UPLOAD
$imageSQL = "";

if (!empty($_FILES['image']['name'])) {
    $imageName = time() . "_" . basename($_FILES['image']['name']);
    $target = "uploads/" . $imageName;

    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $imageSQL = ", image='$imageName'";
}

// FINAL QUERY
$sql = "UPDATE users SET 
    fullname='$fullname',
    username='$username',
    email='$email',
    weight='$weight',
    height='$height',
    goal='$goal',
    program='$program'
    $passwordSQL
    $imageSQL
    WHERE id='$id'";

if ($conn->query($sql)) {
    header("Location: setting.php?success=1");
    exit();
} else {
    echo "Error updating profile: " . $conn->error;
}
?>