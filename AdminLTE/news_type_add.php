<?php
require_once("../db/connect.php");

if (isset($_POST["btn-add"])) {
    $nstName = $_POST["nst_name"];
    $nstStatus = $_POST["nst_status"];
    $nstNewImg = $_FILES["nst_newImg"]["name"];

    $locationError = "Location: news_type_add_form.php";
    $locationSuccess = "Location: news_type_show.php";

    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }

    if (empty($nstName) || !isset($nstStatus)) {
        messageError("กรุณากรอกข้อมูลให้ครบ", $locationError);
    } 
    
    if (mb_strlen($nstName, 'UTF-8') > 50) {
        messageError("ชื่อประเภทข่าว ต้องไม่เกิน 50 ตัวอักษร", $locationError);
    }
    

    if ($nstStatus !== "1" && $nstStatus !== "0") {
        messageError("สถานะต้องเป็นเลข 0 หรือ 1 เท่านั้น", $locationError);
    }


    // Check 
    $sql = "SELECT nst_name
            FROM pbr_news_type
            WHERE nst_name = :nst_name
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":nst_name", $nstName);
    $stmt->execute();
    $check = $stmt->fetch();

    if ($check) {
        messageError("ชื่อประเภทข่าวนี้ มีอยู่แล้ว", $locationError);
    } else {
        try {

            $folderUploads = "../uploads/img_news_type/";
            $allowedExtensions = ['png', 'jpg', 'jpeg'];
            $maxFileSize = 2 * 1024 * 1024; // Max file size (2 MB)


            // Function to generate unique file name
            function generateUniqueNewsTypeAdd($extension, $folder)
            {
                do {
                    $fileName = 'nst_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $extension;
                } while (file_exists($folder . $fileName));
                return $fileName;
            }

            if (empty($nstNewImg)) {
                $fileDefault = 'default.png'; // Default image file
                $filePath = $folderUploads . $fileDefault;

                // Check if default file exists
                if (!file_exists($filePath)) {
                    messageError("ไม่มีไฟล์ภาพชื่อ default.png ในโฟลเดอร์ uploads/img_news_type/", $locationError);
                }


                $fileSize = filesize($filePath); // Get file size (bytes)
                $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                // Validate file
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์ภาพต้องเป็นประเภท png, jpg, jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                $newImg = generateUniqueNewsTypeAdd($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newImg;

                // Copy default image to new file
                if (copy($filePath, $targetFilePath)) {
                    $sql = "INSERT INTO pbr_news_type (nst_name, nst_status, nst_img) 
                            VALUES (:nst_name, :nst_status, :nst_img)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":nst_name", $nstName);
                    $stmt->bindParam(":nst_status", $nstStatus);
                    $stmt->bindParam(":nst_img", $newImg);
                    $stmt->execute();
                } else {
                    messageError("คัดลอกไฟล์ผิดพลาด", $locationError);
                }
            } else {
                // มีการเปลี่ยนรูป
                $fileExtension = strtolower(pathinfo($nstNewImg, PATHINFO_EXTENSION));
                $fileSize = $_FILES["nst_newImg"]["size"];

                // Validate file type and size
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์รูปภาพต้องเป็น png, jpg หรือ jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                $newImg = generateUniqueNewsTypeAdd($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newImg;

                // Move uploaded file to target directory
                if (move_uploaded_file($_FILES["nst_newImg"]["tmp_name"], $targetFilePath)) {
                    $sql = "INSERT INTO pbr_news_type (nst_name, nst_status, nst_img) 
                            VALUES (:nst_name, :nst_status, :nst_img)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":nst_name", $nstName);
                    $stmt->bindParam(":nst_status", $nstStatus);
                    $stmt->bindParam(":nst_img", $newImg);
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
        header("Location:news_type_show.php");
    }
} else {
    header("Location: news_type_show.php");
    exit;
}
