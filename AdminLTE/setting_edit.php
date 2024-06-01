<?php
require_once("../db/connect.php");

if (isset($_POST["btn-edit"])) {
    $stId = $_POST["st_id"];

    // ดึงค่าจากคำขอ POST
    $stNewWebName = isset($_POST["st_newWebName"]) ? $_POST["st_newWebName"] : '';
    $stNewFavicon = isset($_FILES["st_newFavicon"]["name"]) ? $_FILES["st_newFavicon"]["name"] : '';
    $stNewLogo = isset($_FILES["st_newLogo"]["name"]) ? $_FILES["st_newLogo"]["name"] : '';



    $locationError = "Location: setting_show.php";

    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }

    // ชื่อเว็บไซต์
    if (!empty($stNewWebName)) {
        $stNewWebName = $_POST["st_newWebName"];

        if (mb_strlen($stNewWebName, 'UTF-8') > 20) {
            messageError("ชื่อเว็บไซต์ต้องไม่เกิน 20 ตัวอักษร", $locationError);
        }

        $sql = "UPDATE pbr_setting
                SET st_detail = :st_detail,
                    time = NOW()
                WHERE st_id = :st_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":st_detail", $stNewWebName);
        $stmt->bindParam(":st_id", $stId);
        $stmt->execute();
    }

    // Favicon ใหม่
    if (!empty($stNewFavicon)) {

        $stDetail = $_POST["st_detail"];
        $stNewFavicon = $_FILES["st_newFavicon"]["name"];

        $folderUploads = "../uploads/img_web_setting/";
        $allowedExtensions = ['png', 'ico', 'svg'];
        $maxFileSize = 2 * 1024 * 1024; // Max file size (2 MB)

        // Function to generate unique file name
        function generateUniqueFileFavicon($extension, $folder)
        {
            do {
                $fileName = 'fvc_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $extension;
            } while (file_exists($folder . $fileName));
            return $fileName;
        }

        $fileExtension = strtolower(pathinfo($stNewFavicon, PATHINFO_EXTENSION));
        $fileSize = $_FILES["st_newFavicon"]["size"];

        // Validate file type and size
        if (!in_array($fileExtension, $allowedExtensions)) {
            messageError("ไฟล์รูปภาพต้องเป็น png, ico หรือ svg เท่านั้น", $locationError);
        } elseif ($fileSize > $maxFileSize) {
            messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
        }

        $newFavicon = generateUniqueFileFavicon($fileExtension, $folderUploads);
        $targetFilePath = $folderUploads . $newFavicon;


        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES["st_newFavicon"]["tmp_name"], $targetFilePath)) {

            // ลบรูปเดิม
            if (!empty($stDetail) && file_exists($folderUploads . $stDetail)) {
                unlink($folderUploads . $stDetail);
            }

            $sql = "UPDATE pbr_setting
                    SET st_detail = :st_detail,
                        time = NOW()
            WHERE st_id = :st_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":st_detail", $newFavicon);
            $stmt->bindParam(":st_id", $stId);
            $stmt->execute();
        } else {
            messageError("เกิดข้อผิดพลาดในการอัปโหลดไฟล์", $locationError);
        }
    }


    // Logo ใหม่
    if (!empty($stNewLogo)) {

        $stDetail = $_POST["st_detail"];
        $stNewLogo = $_FILES["st_newLogo"]["name"];

        $folderUploads = "../uploads/img_web_setting/";
        $allowedExtensions = ['png', 'jpg', 'jpeg'];
        $maxFileSize = 2 * 1024 * 1024; // Max file size (2 MB)

        // Function to generate unique file name
        function generateUniqueFileLogo($extension, $folder)
        {
            do {
                $fileName = 'lg_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $extension;
            } while (file_exists($folder . $fileName));
            return $fileName;
        }

        $fileExtension = strtolower(pathinfo($stNewLogo, PATHINFO_EXTENSION));
        $fileSize = $_FILES["st_newFavicon"]["size"];

        // Validate file type and size
        if (!in_array($fileExtension, $allowedExtensions)) {
            messageError("ไฟล์รูปภาพต้องเป็น png, ico หรือ svg เท่านั้น", $locationError);
        } elseif ($fileSize > $maxFileSize) {
            messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
        }

        $newLogo = generateUniqueFileLogo($fileExtension, $folderUploads);
        $targetFilePath = $folderUploads . $newLogo;


        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES["st_newLogo"]["tmp_name"], $targetFilePath)) {

            // ลบรูปเดิม
            if (!empty($stDetail) && file_exists($folderUploads . $stDetail)) {
                unlink($folderUploads . $stDetail);
            }

            $sql = "UPDATE pbr_setting
                    SET st_detail = :st_detail,
                        time = NOW()
            WHERE st_id = :st_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":st_detail", $newLogo);
            $stmt->bindParam(":st_id", $stId);
            $stmt->execute();
        } else {
            messageError("เกิดข้อผิดพลาดในการอัปโหลดไฟล์", $locationError);
        }
    }



    $_SESSION["success"] = "แก้ไขข้อมูลตั้งค่าเว็บไซต์สำเร็จ";
    header("Location: setting_show.php");
    exit;
} else {
    header("Location: setting_show.php");
    exit;
}
