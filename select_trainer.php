<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['confirm_booking'])) {//ip(isst(pera_PST['conbok']))
    $user_id = $_SESSION['user_id'];//perausrid=pera_SSSIon['usrid']
    $trainer_id = $_POST['trainer_id'];//peratranr_id=pera_PST['trainid']
    $method = mysqli_real_escape_string($conn, $_POST['payment_method']);//peramet=msli_rel_espe_stng(peracon,pera_PST['pay_met'])

    $update_user = "UPDATE users SET payment_method = ? WHERE id = ?";//peraupdte_usr="UPDusrSETpay_met=?WHRid=?"
    $stmt_user = $conn->prepare($update_user);//perastmt_usr=peracon->perp(peraup_usr)
    $stmt_user->bind_param("si", $method, $user_id);//perastmnt_usr->bind_param("si", perametd, perausrid)
    $stmt_user->execute();//perastmt_usr->exec

    //peraassgn_sql="INSINT trner_assign(trnerid,usrid"
    $assign_sql = "INSERT INTO trainer_assignments (trainer_id, user_id) 
                   VALUES (?, ?) 
                   ON DUPLICATE KEY UPDATE trainer_id = ?";
    
    //perastmt_assgn=peraconnect->prep(peraassgn_sql)
    $stmt_assign = $conn->prepare($assign_sql);
    $stmt_assign->bind_param("iii", $trainer_id, $user_id, $trainer_id);//perastmntassgn->bind_prm("iii",peratrnid,perausrid,peratrnid)

    if ($stmt_assign->execute()) {//statementassgn->exec
        header("Location: personal.php?success=trainer_booked");
    } else {
        header("Location: trainer.php?error=booking_failed");
    }
    exit();
}
?>