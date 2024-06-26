<?php
require_once("../db/connect.php");

if (isset($_POST["btn-edit"])) {
    $admId = $_POST["adm_id"];
    $admFname = $_POST["adm_fname"];
    $admLname = $_POST["adm_lname"];
    $admStatus = $_POST["adm_status"];
    $admProfile = $_POST["adm_profile"];
    $admNewProfile = $_FILES["adm_newProfile"]["name"];
    // echo $admProfile . "<br>";
    // echo $admNewProfile;
    $id = $_SESSION["base64Encoded"];

    $locationError = "Location: admin_edit_form.php?id=$id";

    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }

    // ตรวจสอบค่าว่าง
    if (empty($admId) || empty($admFname) || empty($admLname) || !isset($admStatus)) {
        messageError("กรุณากรอกข้อมูลให้ครบทุกช่อง", $locationError);
    }

    // จำนวนตัวอักษร
    if (mb_strlen($admFname, 'UTF-8') > 70 || mb_strlen($admLname, 'UTF-8') > 70) {
        messageError("ข้อมูลต้องไม่เกิน 70 ตัวอักษร", $locationError);
    }


    // ตรวจสอบค่าว่าเป็น 0 หรือ 1 หรือไม่
    if ($admStatus !== "0" && $admStatus !== "1") {
        messageError("สถานะต้องเป็นเลข 0 หรือ 1 เท่านั้น", $locationError);
    }

    try {
        if (!empty($admNewProfile)) {

            $allowedExtensions = ['png', 'jpg', 'jpeg'];
            $fileExtension = strtolower(pathinfo($admNewProfile, PATHINFO_EXTENSION));
            $fileSize = $_FILES["adm_newProfile"]["size"];
            $maxFileSize = 2 * 1024 * 1024; // 2 MB in bytes

            // ตรวจสอบประเภทไฟล์และขนาด
            if (!in_array($fileExtension, $allowedExtensions)) {
                messageError("ไฟล์รูปภาพต้องเป็น png, jpg หรือ jpeg เท่านั้น", $locationError);
            } elseif ($fileSize > $maxFileSize) {
                messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
            }

            // Folder ที่เก็บไฟล์
            $folderUploads = '../uploads/profile_admin/';

            // สุ่มชื่อรูป -> ตรวจสอบไฟล์
            $newProfile = 'profile_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $fileExtension;
            while (file_exists($folderUploads . $newProfile)) {
                $newProfile = 'profile_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $fileExtension;
            }
            $targetFilePath = $folderUploads . $newProfile;

            // ย้ายไฟล์ไป folder uploads
            if (move_uploaded_file($_FILES["adm_newProfile"]["tmp_name"], $targetFilePath)) {
                // ลบรูปเดิม
                if (!empty($admProfile) && file_exists($folderUploads . $admProfile)) {
                    unlink($folderUploads . $admProfile);
                }

                // อัปเดทชื่อรูปใหม่
                $sql = "UPDATE pbr_admin 
                        SET adm_profile = :adm_profile
                        WHERE adm_id = :adm_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":adm_profile", $newProfile);
                $stmt->bindParam(":adm_id", $admId);
                $stmt->execute();
            } else {
                messageError("เกิดข้อผิดพลาดในการอัปโหลดไฟล์", $locationError);
            }
        }

        $sql = "UPDATE pbr_admin 
                SET adm_fname = :adm_fname,
                    adm_lname = :adm_lname,
                    adm_status = :adm_status,
                    time = NOW()
                WHERE adm_id = :adm_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":adm_fname", $admFname);
        $stmt->bindParam(":adm_lname", $admLname);
        $stmt->bindParam(":adm_status", $admStatus);
        $stmt->bindParam(":adm_id", $admId);
        $stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    if (empty($_SESSION["error"])) {
        $_SESSION["success"] = "แก้ไขข้อมูลผู้ดูแลระบบสำเร็จ";
        header("Location:admin_show.php");
    }
} else {
    header("Location: admin_show.php");
    exit;
}
