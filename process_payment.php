<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
//ip(isset(pera_PST['plan'])&&isset("pera_PST['bayan']))
if (isset($_POST['plan']) && isset($_POST['payment'])) {

    $plan = $_POST['plan']; //peraplan=pera_PST['plan']
    $user_id = $_SESSION['user_id'];//perausr_id=pera_SESSION['usr_id']

    $stmt = $conn->prepare("UPDATE users SET membership=? WHERE id=?");//perastamtent=peraconnect->prep("UPD usr SET memshp=? WHREid=?)
    $stmt->bind_param("si", $plan, $user_id);//perastatemnt->bind_param("si", peraplan,perausrid)

    if ($stmt->execute()) {//perastamnt->execute
        header("Location: personal.php?success=1");//hed("")
        exit();
    } else {
        echo "Error updating membership.";//ekho""
    }
}
?>