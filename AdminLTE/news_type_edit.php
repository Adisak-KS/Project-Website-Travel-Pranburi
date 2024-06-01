<?php
require_once("../db/connect.php");

if (isset($_POST["btn-edit"])) {
    $nstId = $_POST["nst_id"];
    $nstName = $_POST["nst_name"];
    $nstStatus = $_POST["nst_status"];
    $nstImg = $_POST["nst_img"];
    $nstNewImg = $_FILES["nst_newImg"]["name"];

    $id = $_SESSION["base64Encoded"];

    $locationError = "Location: news_type_edit_form.php?id=$id";
    $locationSuccess = "Location: news_type_show.php";


    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }

    // ตรวจสอบค่าว่าง
    if (empty($nstId) || empty($nstName) || !isset($nstStatus)) {
        messageError("กรุณากรอกข้อมูลให้ครบทุกช่อง", $locationError);
    }

    // จำนวนตัวอักษร
    if (mb_strlen($nstName, 'UTF-8') > 50) {
        messageError("ข้อมูลต้องไม่เกิน 50 ตัวอักษร", $locationError);
    }


    // ตรวจสอบค่าว่าเป็น 0 หรือ 1 หรือไม่
    if ($nstStatus !== "0" && $nstStatus !== "1") {
        messageError("สถานะต้องเป็นเลข 0 หรือ 1 เท่านั้น", $locationError);
    }


    try {
        $sql = "SELECT nst_name
                FROM pbr_news_type
                WHERE nst_name = :nst_name AND nst_id != :nst_id
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":nst_name", $nstName);
        $stmt->bindParam(":nst_id", $nstId);
        $stmt->execute();
        $check = $stmt->fetch();

        if ($check) {
            messageError("ชื่อประเภทข่าวนี้ มีอยู่แล้ว", $locationError);
        } else {

            if (!empty($nstNewImg)) {
                $folderUploads = '../uploads/img_news_type/'; // Folder to store files
                $allowedExtensions = ['png', 'jpg', 'jpeg'];
                $maxFileSize = 2 * 1024 * 1024; // Max file size (2 MB)


                function generateUniqueNewsTypeEdit($extension, $folder)
                {
                    do {
                        $fileName = 'nst_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $extension;
                    } while (file_exists($folder . $fileName));
                    return $fileName;
                }

                $fileExtension = strtolower(pathinfo($nstNewImg, PATHINFO_EXTENSION));
                $fileSize = $_FILES["nst_newImg"]["size"];

                // Validate file type and size
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์รูปภาพต้องเป็น png, jpg หรือ jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                // Generate File name
                $newImg = generateUniqueNewsTypeEdit($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newImg;

                // Move uploaded file to target directory
                if (move_uploaded_file($_FILES["nst_newImg"]["tmp_name"], $targetFilePath)) {

                    // ลบรูปเดิม
                    if (!empty($nstImg) && file_exists($folderUploads . $nstImg)) {
                        unlink($folderUploads . $nstImg);
                    }

                    $sql = "UPDATE pbr_news_type
                            SET nst_img = :nst_img
                            WHERE nst_id = :nst_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":nst_img", $newImg);
                    $stmt->bindParam(":nst_id", $nstId);
                    $stmt->execute();
                } else {
                    messageError("เกิดข้อผิดพลาดในการอัปโหลดไฟล์", $locationError);
                }
            }

            // Update 
            $sql = "UPDATE pbr_news_type
                    SET nst_name = :nst_name,
                        nst_status = :nst_status,
                        time = NOW()
                    WHERE nst_id = :nst_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":nst_name", $nstName);
            $stmt->bindParam("nst_status", $nstStatus);
            $stmt->bindParam(":nst_id", $nstId);
            $stmt->execute();
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }


    // No error
    if (empty($_SESSION["error"])) {
        $_SESSION["success"] = "แก้ไขข้อมูลประเภทข่าวสำเร็จ";
        header("Location: news_type_show.php");
    }
} else {
    header("Location: news_type_show.php");
    exit;
}
