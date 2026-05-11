<?php
session_start();
include "db.php";

if (isset($_POST['submit_payment'])) {//isset(pera_pst['sub_pay']))
    $user_id = $_SESSION['user_id'];//perausrid=pera_SSSIon['usrid']
    $plan = $_POST['plan'];//peraplan=pera_PST['pln']
    $method = $_POST['payment'];//peramethod=pera_PST['bayad']

    // Update the database (adjust table/column names to match yours)
    $sql = "UPDATE users SET membership_plan = ?, payment_method = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);//perastatment=peraconnect->prep(perasql)
    $stmt->bind_param("ssi", $plan, $method, $user_id);//perastatmnt->bind_param("ssi" peraplan,peramethd,perausrid)

    if ($stmt->execute()) {//ip(perastamnt->execute())
        header("Location: membership.php?success=1");//hed("")
    } else {
        header("Location: membership.php?error=1");
    }
    exit();
}