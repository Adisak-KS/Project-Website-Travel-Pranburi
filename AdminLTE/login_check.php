<?php
require_once("../db/connect.php");

if (isset($_POST["btn-login"])) {
    $admUsername = $_POST["adm_username"];
    $admPassword = $_POST["adm_password"];

    // check Admin
    try {
        $sql = "SELECT adm_id, adm_username, adm_email, adm_password, adm_status
                    FROM pbr_admin
                    WHERE adm_username = :adm_username OR adm_email = :adm_email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":adm_username", $admUsername);
        $stmt->bindParam(":adm_email", $admUsername);
        $stmt->execute();
        $check = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($check) {

            // ตรวจสอบรหัสผ่าน
            if (password_verify($admPassword, $check['adm_password'])) {
                if ($check['adm_status'] == 1) {
                    // เก็บ session สำหรับผู้ใช้ที่เข้าสู่ระบบ
                    $_SESSION['adm_id'] = $check['adm_id'];
                    $_SESSION['success'] = "เข้าสู่ระบบโดยผู้ใช้ " . $check['adm_username'] . " สำเร็จ";
                    header('Location: index.php');
                    exit();
                } 
                else {
                    // เก็บ session สำหรับผู้ใช้ที่เข้าสู่ระบบ
                    $_SESSION['adm_id'] = $check['adm_id'];
                    $_SESSION['error'] = "ไม่มีสิทธิ์ใช้งานระบบ";
                    header('Location: blocked.php');
                    exit();
                }
            } else {
                // กรณี Password ไม่ถูกต้อง
                $_SESSION["error"] = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
                header("Location: login_form.php");
                exit();
            }
        } else {
            // กรณีไม่พบผู้ใช้ในระบบ
            $_SESSION["error"] = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
            header("Location: login_form.php");
            exit();
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    header("Location: Login_form.php");
    exit();
}
