<?php
require_once("../db/connect.php");

if (isset($_POST["btn-edit"])) {
    $tvtId = $_POST["tvt_id"];
    $tvtName = $_POST["tvt_name"];
    $tvtStatus = $_POST["tvt_status"];
    $tvtImg = $_POST["tvt_img"];
    $tvtNewImg = $_FILES["tvt_newImg"]["name"];

    $id = $_SESSION["base64Encoded"];

    $locationError = "Location: travel_type_edit_form.php?id=$id";

    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }

    // ตรวจสอบค่าว่าง
    if (empty($tvtId) || empty($tvtName) || !isset($tvtStatus)) {
        messageError("กรุณากรอกข้อมูลให้ครบทุกช่อง", $locationError);
    }

    // จำนวนตัวอักษร
    if (mb_strlen($tvtName, 'UTF-8') > 50) {
        messageError("ข้อมูลต้องไม่เกิน 50 ตัวอักษร", $locationError);
    }


    // ตรวจสอบค่าว่าเป็น 0 หรือ 1 หรือไม่
    if ($tvtStatus !== "0" && $tvtStatus !== "1") {
        messageError("สถานะต้องเป็นเลข 0 หรือ 1 เท่านั้น", $locationError);
    }


    try {
        $sql = "SELECT tvt_name
                FROM pbr_travel_type
                WHERE tvt_name = :tvt_name AND tvt_id != :tvt_id
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tvt_name", $tvtName);
        $stmt->bindParam(":tvt_id", $tvtId);
        $stmt->execute();
        $check = $stmt->fetch();

        if ($check) {
            messageError("ชื่อประเภทสถานที่ท่องเที่ยวนี้ มีอยู่ในระบบแล้ว", $locationError);
        } else {

            if (!empty($tvtNewImg)) {

                $folderUploads = '../uploads/img_travel_type/'; // Folder to store files
                $allowedExtensions = ['png', 'jpg', 'jpeg'];
                $maxFileSize = 2 * 1024 * 1024; // Max file size (2 MB)

                function generateUniqueTravelTypeCoverEdit($extension, $folder)
                {
                    do {
                        $fileName = 'tvt_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $extension;
                    } while (file_exists($folder . $fileName));
                    return $fileName;
                }

                $fileExtension = strtolower(pathinfo($tvtNewImg, PATHINFO_EXTENSION));
                $fileSize = $_FILES["tvt_newImg"]["size"];

                // Validate file type and size
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์รูปภาพต้องเป็น png, jpg หรือ jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                // Generate File name
                $newImg = generateUniqueTravelTypeCoverEdit($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newImg;

                // Move uploaded file to target directory
                if (move_uploaded_file($_FILES["tvt_newImg"]["tmp_name"], $targetFilePath)) {

                    // ลบรูปเดิม
                    if (!empty($tvtImg) && file_exists($folderUploads . $tvtImg)) {
                        unlink($folderUploads . $tvtImg);
                    }

                    $sql = "UPDATE pbr_travel_type
                            SET tvt_img = :tvt_img
                            WHERE tvt_id = :tvt_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":tvt_img", $newImg);
                    $stmt->bindParam(":tvt_id", $tvtId);
                    $stmt->execute();
                } else {
                    messageError("เกิดข้อผิดพลาดในการอัปโหลดไฟล์", $locationError);
                }
            }

            // Update 
            $sql = "UPDATE pbr_travel_type
                    SET tvt_name = :tvt_name,
                        tvt_status = :tvt_status,
                        time = NOW()
                    WHERE tvt_id = :tvt_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":tvt_name", $tvtName);
            $stmt->bindParam("tvt_status", $tvtStatus);
            $stmt->bindParam(":tvt_id", $tvtId);
            $stmt->execute();
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    if (empty($_SESSION["error"])) {
        $_SESSION["success"] = "แก้ไขข้อมูลประเภทสถานที่ท่องเที่ยวสำเร็จ";
        header("Location: travel_type_show.php");
    }
} else {
    header("Location: travel_type_show.php");
    exit;
}
