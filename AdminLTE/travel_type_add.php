<?php
require_once("../db/connect.php");

if (isset($_POST["btn-add"])) {
    $tvtName = $_POST["tvt_name"];
    $tvtStatus = $_POST["tvt_status"];
    $tvtNewImg = $_FILES["tvt_newImg"]["name"];

    $locationError = "Location:travel_type_add_form.php";
    $locationSuccess = "Location:travel_type_show.php";


    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }


    if (empty($tvtName)) {
        messageError("กรุณากรอก ชื่อประเภทสถานที่ท่องเที่ยว", $locationError);
    } elseif (mb_strlen($tvtName, 'UTF-8') > 50) {
        messageError("ชื่อประเภทสถานที่ท่องเที่ยว ต้องไม่เกิน 50 ตัวอักษร", $locationError);
    }

    if (!isset($tvtStatus)) {
        messageError("กรุณาระบุ สถานะประเภทสถานที่ท่องเที่ยว", $locationError);
    } elseif ($tvtStatus !== "1" && $tvtStatus !== "0") {
        messageError("สถานะต้องเป็นเลข 0 หรือ 1 เท่านั้น", $locationError);
    }

    $sql = "SELECT tvt_name
            FROM pbr_travel_type
            WHERE tvt_name = :tvt_name
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":tvt_name", $tvtName);
    $stmt->execute();
    $check = $stmt->fetch();

    if ($check) {
        messageError("ชื่อประเภทสถานที่ท่องเที่ยวนี้ มีอยู่แล้ว", $locationError);
    } else {
        try {
            $folderUploads = '../uploads/img_travel_type/'; // Folder to store files
            $allowedExtensions = ['png', 'jpg', 'jpeg'];
            $maxFileSize = 2 * 1024 * 1024; // Max file size (2 MB)

            // Function to generate unique file name
            function generateUniqueTravelTypeCoverEdit($extension, $folder)
            {
                do {
                    $fileName = 'tvt_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $extension;
                } while (file_exists($folder . $fileName));
                return $fileName;
            }

            if (empty($tvtNewImg)) {
                $fileDefault = 'default.png'; // Default image file
                $filePath = $folderUploads . $fileDefault;

                // Check if default file exists
                if (!file_exists($filePath)) {
                    messageError("ไม่มีไฟล์ภาพชื่อ default.png ในโฟลเดอร์ uploads/img_travel_type/", $locationError);
                }

                $fileSize = filesize($filePath); // Get file size (bytes)
                $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                // Validate file
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์ภาพต้องเป็นประเภท png, jpg, jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                $newImg = generateUniqueTravelTypeCoverEdit($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newImg;

                // Copy default image to new file
                if (copy($filePath, $targetFilePath)) {
                    $sql = "INSERT INTO pbr_travel_type (tvt_name, tvt_status, tvt_img) 
                            VALUES (:tvt_name, :tvt_status, :tvt_img)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":tvt_name", $tvtName);
                    $stmt->bindParam(":tvt_status", $tvtStatus);
                    $stmt->bindParam(":tvt_img", $newImg);
                    $stmt->execute();
                } else {
                    messageError("คัดลอกไฟล์ผิดพลาด", $locationError);
                }
            } else {
                // มีการเปลี่ยนรูป
                $fileExtension = strtolower(pathinfo($tvtNewImg, PATHINFO_EXTENSION));
                $fileSize = $_FILES["tvt_newImg"]["size"];

                // Validate file type and size
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์รูปภาพต้องเป็น png, jpg หรือ jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                $newImg = generateUniqueTravelTypeCoverEdit($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newImg;

                // Move uploaded file to target directory
                if (move_uploaded_file($_FILES["tvt_newImg"]["tmp_name"], $targetFilePath)) {
                    $sql = "INSERT INTO pbr_travel_type (tvt_name, tvt_status, tvt_img) 
                            VALUES (:tvt_name, :tvt_status, :tvt_img)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":tvt_name", $tvtName);
                    $stmt->bindParam(":tvt_status", $tvtStatus);
                    $stmt->bindParam(":tvt_img", $newImg);
                    $stmt->execute();
                } else {
                    messageError("เกิดข้อผิดพลาดในการอัปโหลดไฟล์", $locationError);
                }
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // ไม่มี error
    if (empty($_SESSION["error"])) {
        $_SESSION["success"] = "เพิ่มประเภทสถานที่ท่องเที่ยวสำเร็จ";
        header($locationSuccess);
    }
} else {
    header("Location: travel_type_show.php");
    exit;
}
