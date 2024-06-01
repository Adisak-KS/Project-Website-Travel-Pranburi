<?php
require_once("../db/connect.php");

if (isset($_POST["btn-edit"])) {
    $admId = $_POST["adm_id"];
    $admPassword = $_POST["adm_password"];
    $admNewPassword = $_POST["adm_newPassword"];
    $admConfirmNewPassword = $_POST["adm_confirmNewPassword"];


    $id = $_SESSION["base64Encoded"];

    $locationError = "Location: acc_password_edit_form.php?id=$id";

    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }

    if (empty($admId) || empty($admPassword) || empty($admNewPassword) || empty($admConfirmNewPassword)) {
        messageError("กรุณากรอกข้อมูลให้ครบทุกช่อง", $locationError);
    }

    if (mb_strlen($admPassword, 'UTF-8') < 8 || mb_strlen($admNewPassword, 'UTF-8') < 8 || mb_strlen($admConfirmNewPassword, 'UTF-8') < 8) {
        messageError("รหัสผ่านต้องมี 8 ตัวอักษรขึ้นไป", $locationError);
    }


    if ($admPassword == $admNewPassword) {
        messageError("รหัสผ่านใหม่เหมือนกับรหัสผ่านเดิม กรุณาตั้งรหัสผ่านใหม่ ", $locationError);
    }

    if ($admConfirmNewPassword != $admNewPassword) {
        messageError("ยืนยันรหัสผ่านไม่ถูกต้อง", $locationError);
    }


    try {
        $sql = "SELECT adm_password
                FROM pbr_admin
                WHERE adm_id = :adm_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":adm_id", $admId);
        $stmt->execute();
        $check = $stmt->fetch(PDO::FETCH_ASSOC);


        // ตรวจสอบว่ารหัสผ่านที่ถูก hash ตรงกับรหัสผ่านที่ผู้ใช้ป้อนเข้ามาหรือไม่
        if (password_verify($admPassword, $check["adm_password"])) {

            // ทำการ hash รหัสผ่านใหม่
            $hashedPassword = password_hash($admNewPassword, PASSWORD_DEFAULT);

            // Update Password
            $sql = "UPDATE pbr_admin
                    SET adm_password = :adm_password,
                        time = NOW()
                    WHERE adm_id = :adm_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":adm_password", $hashedPassword);
            $stmt->bindParam(":adm_id", $admId);
            $stmt->execute();
        } else {
            // Password เดิมไม่ถูกต้อง
            messageError("รหัสผ่านเดิมไม่ถูกต้อง", $locationError);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    if (empty($_SESSION["error"])) {
        $_SESSION["success"] = "ออกจากระบบเพื่อใช้งานรหัสผ่านใหม่";
        header("Location: acc_show.php");
    }
} else {
    header("Location: acc_show.php");
    exit;
}
