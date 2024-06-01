<?php
require_once("../db/connect.php");
if (isset($_POST["btn-edit"])) {
    $admId = $_POST["adm_id"];
    $admUsername = $_POST["adm_username"];
    $admNewUsername = $_POST["adm_newUsername"];

    $admEmail = $_POST["adm_email"];
    $admNewEmail = $_POST["adm_newEmail"];

    $id = $_SESSION["base64Encoded"];

    $locationError = "Location: acc_account_edit_form.php?id=$id";

    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }

    if (!empty($admNewUsername)) {
        $admUsername = $_POST["adm_username"];
        $admNewUsername = $_POST["adm_newUsername"];

        // ตรวจสอบ Username
        if (mb_strlen($admNewUsername,'UTF-8') < 6 || mb_strlen($admNewUsername,) > 70 || !preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $admNewUsername)) {
            messageError("Username ต้องขึ้นต้นด้วย a-z และสามารถใช้เฉพาะ a-z, A-Z, 0-9, และ _ เท่านั้น ห้ามมีช่องว่าง", $locationError);
        }
        if ($admUsername == $admNewUsername) {
            messageError("ชื่อผู้ใช้ (Username) เดิม กรุณาใช้ชื่อผู้ใช้ใหม่", $locationError);
        }

        $sql = "SELECT adm_username 
                FROM pbr_admin
                WHERE adm_username = :adm_username
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":adm_username", $admNewUsername);
        $stmt->execute();
        $check = $stmt->fetch();

        if ($check) {
            messageError("ไม่สามารถใช้ชื่อผู้ใช้ (Username) นี้ได้", $locationError);
        } else {
            $sql = "UPDATE pbr_admin
                    SET adm_username = :adm_username
                    WHERE adm_id = :adm_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":adm_username", $admNewUsername);
            $stmt->bindParam(":adm_id", $admId);
            $stmt->execute();

            $_SESSION["success"] = "แก้ไขข้อมูลบัญชีสำเร็จ";
            header("Location: acc_show.php");
        }
    }

    if (!empty($admNewEmail)) {
        // ตรวจสอบ Email
        if (!filter_var($admNewEmail, FILTER_VALIDATE_EMAIL)) {
            messageError("รูปแบบอีเมลไม่ถูกต้อง", $locationError);
        } elseif (mb_strlen($admNewEmail, 'UTF-8') < 10 || mb_strlen($admNewEmail, 'UTF-8') > 65) {
            messageError("อีเมลต้องมี 10-65 ตัวอักษร", $locationError);
        }        

        if ($admEmail == $admNewEmail) {
            messageError("อีเมลเดิม กรุณาใช้อีเมลใหม่", $locationError);
        }

        $sql = "SELECT adm_email 
                FROM pbr_admin
                WHERE adm_email = :adm_email
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":adm_email", $admNewEmail);
        $stmt->execute();
        $check = $stmt->fetch();

        if ($check) {
            messageError("ไม่สามารถใช้อีเมลนี้ได่", $locationError);
        } else {
            $sql = "UPDATE pbr_admin
                    SET adm_email = :adm_email
                    WHERE adm_id = :adm_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":adm_email", $admNewEmail);
            $stmt->bindParam(":adm_id", $admId);
            $stmt->execute();

            $_SESSION["success"] = "แก้ไขข้อมูลบัญชีสำเร็จ";
            header("Location: acc_show.php");
        }
    }

    if(empty($admNewUsername) || empty($admNewEmail)) {
        header("Location: acc_show.php");
        exit;
    }
} else {
    header("Location: acc_show.php");
    exit;
}
