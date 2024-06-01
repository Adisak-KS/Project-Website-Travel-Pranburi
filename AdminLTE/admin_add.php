<?php
require_once("../db/connect.php");

if (isset($_POST["btn-add-admin"])) {
    $admFname = $_POST["adm_fname"];
    $admLname = $_POST["adm_lname"];
    $admUsername = $_POST["adm_username"];
    $admPassword = $_POST["adm_password"];
    $admConfirmPassword = $_POST["adm_confirmPassword"];
    $admEmail = $_POST["adm_email"];
    $admStatus = $_POST["adm_status"];
    $admNewProfile = $_FILES["adm_newProfile"]["name"];

    $locationError = "Location: admin_add_form.php";
    $locationSuccess = "Location: admin_show.php";


    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }
    // ตรวจสอบค่าว่าง
    if (empty($admFname) || empty($admLname) || empty($admUsername) || empty($admPassword) || empty($admConfirmPassword) || empty($admEmail) || !isset($admStatus)) {
        messageError("กรุณากรอกข้อมูลให้ครบ", $locationError);
    }

    // จำนวนตัวอักษร
    if (mb_strlen($admFname, 'UTF-8') > 70 || mb_strlen($admLname, 'UTF-8') > 70 || mb_strlen($admEmail, 'UTF-8') > 70) {
        messageError("ข้อมูลต้องไม่เกิน 70 ตัวอักษร", $locationError);
    }

    // ตรวจสอบ Username
    if (mb_strlen($admUsername, 'UTF-8') < 6 || mb_strlen($admUsername, 'UTF-8') > 70 || !preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $admUsername)) {
        messageError("Username ต้องขึ้นต้นด้วย a-z และสามารถใช้เฉพาะ a-z, A-Z, 0-9, และ _ เท่านั้น ห้ามมีช่องว่าง", $locationError);
    }

    // ตรวจสอบ Password
    if (mb_strlen($admPassword, 'UTF-8') < 8 || mb_strlen($admPassword, 'UTF-8') > 255 || strpos($admPassword, ' ') !== false) {
        messageError("รหัสผ่านต้องมี 8-255 ตัวอักษร และห้ามมีช่องว่าง", $locationError);
    } else if ($admPassword !== $admConfirmPassword) {
        messageError("รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน", $locationError);
    }

    // ตรวจสอบ Email
    if (!filter_var($admEmail, FILTER_VALIDATE_EMAIL)) {
        messageError("รูปแบบอีเมลไม่ถูกต้อง", $locationError);
    } elseif (mb_strlen($admEmail, 'UTF-8') < 10 || mb_strlen($admEmail, 'UTF-8') > 70) {
        messageError("อีเมลต้องมี 10-70 ตัวอักษร", $locationError);
    }

    // ตรวจสอบค่าว่าเป็น 0 หรือ 1 หรือไม่
    if ($admStatus !== "0" && $admStatus !== "1") {
        messageError("สถานะต้องเป็นเลข 0 หรือ 1 เท่านั้น", $locationError);
    }


    $sql = "SELECT adm_username, adm_email 
            FROM pbr_admin
            WHERE adm_username = :adm_username OR adm_email = :adm_email
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":adm_username", $admUsername);
    $stmt->bindParam(":adm_email", $admEmail);
    $stmt->execute();
    $check = $stmt->fetch();

    if ($check) {
        messageError("ไม่สามารถใช้ชื่อผู้ใช้ หรือ อีเมลนี้ได้", $locationError);
    } else {
        try {
            $folderUploads = '../uploads/profile_admin/'; // Folder to store files
            $allowedExtensions = ['png', 'jpg', 'jpeg'];
            $maxFileSize = 2 * 1024 * 1024; // Max file size (2 MB)

            // Hash Password
            $hashPassword = password_hash($admPassword, PASSWORD_DEFAULT);

            // Function to generate unique file name
            function generateUniqueAdminProfile($extension, $folder)
            {
                do {
                    $fileName = 'profile_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $extension;
                } while (file_exists($folder . $fileName));
                return $fileName;
            }

            if (empty($admNewProfile)) {
                $fileDefault = 'default.png'; // Default image file
                $filePath = $folderUploads . $fileDefault;

                // Check if default file exists
                if (!file_exists($filePath)) {
                    messageError("ไม่มีไฟล์ภาพชื่อ default.png ในโฟลเดอร์ uploads/profile_admin/", $locationError);
                }

                $fileSize = filesize($filePath); // Get file size (bytes)
                $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                // Validate file
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์ภาพต้องเป็นประเภท png, jpg, jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                $newProfile = generateUniqueAdminProfile($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newProfile;


                // Copy default image to new file
                if (copy($filePath, $targetFilePath)) {
                    $sql = "INSERT INTO pbr_admin
                                    (adm_profile, adm_fname, adm_lname, adm_username, adm_password, adm_email, adm_status)
                            VALUES  (:adm_profile, :adm_fname, :adm_lname, :adm_username, :adm_password, :adm_email, :adm_status)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":adm_profile", $newProfile);
                    $stmt->bindParam(":adm_fname", $admFname);
                    $stmt->bindParam(":adm_lname", $admLname);
                    $stmt->bindParam(":adm_username", $admUsername);
                    $stmt->bindParam(":adm_password", $hashPassword);
                    $stmt->bindParam(":adm_email", $admEmail);
                    $stmt->bindParam(":adm_status", $admStatus);
                    $stmt->execute();
                } else {
                    messageError("คัดลอกไฟล์ผิดพลาด", $locationError);
                }
            } else {
                // มีการเปลี่ยนรูป
                $fileExtension = strtolower(pathinfo($admNewProfile, PATHINFO_EXTENSION));
                $fileSize = $_FILES["adm_newProfile"]["size"];

                // Validate file type and size
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์รูปภาพต้องเป็น png, jpg หรือ jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                $newProfile = generateUniqueAdminProfile($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newProfile;

                // Move uploaded file to target directory
                if (move_uploaded_file($_FILES["adm_newProfile"]["tmp_name"], $targetFilePath)) {
                    $sql = "INSERT INTO pbr_admin
                                    (adm_profile, adm_fname, adm_lname, adm_username, adm_password, adm_email, adm_status)
                            VALUES  (:adm_profile, :adm_fname, :adm_lname, :adm_username, :adm_password, :adm_email, :adm_status)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":adm_profile", $newProfile);
                    $stmt->bindParam(":adm_fname", $admFname);
                    $stmt->bindParam(":adm_lname", $admLname);
                    $stmt->bindParam(":adm_username", $admUsername);
                    $stmt->bindParam(":adm_password", $hashPassword);
                    $stmt->bindParam(":adm_email", $admEmail);
                    $stmt->bindParam(":adm_status", $admStatus);
                    $stmt->execute();
                } else {
                    messageError("เกิดข้อผิดพลาดในการอัปโหลดไฟล์", $locationError);
                }
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    if (empty($_SESSION["error"])) {
        $_SESSION["success"] = "เพิ่มข้อมูลผู้ดูแลระบบสำเร็จ";
        header("Location:admin_show.php");
    }
} else {
    header("Location: admin_show.php");
    exit;
}
