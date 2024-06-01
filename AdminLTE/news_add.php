<?php
require_once("../db/connect.php");

if (isset($_POST["btn-add"])) {
    $nsTitle = $_POST["ns_title"];
    $nstId = $_POST['nst_id'];
    $nsDetail = $_POST["ns_detail"];
    $nsStatus = $_POST["ns_status"];
    $nsNewCover = $_FILES["ns_newCover"]["name"];

    $locationError = "Location: news_add_form.php";
    $locationSuccess = "Location: news_show.php";

    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }

    if (empty($nsTitle)) {
        messageError("กรุณากรอกหัวข้อข่าว", $locationError);
    } elseif (mb_strlen($nsTitle, 'UTF-8') > 70) {
        messageError("หัวข้อข่าว ต้องไม่เกิน 70 ตัวอักษร", $locationError);
    }

    if (empty($nsDetail)) {
        messageError("กรุณากรอก รายละเอียดข่าว", $locationError);
    }

    try {
        // Check
        $sql = "SELECT ns_title
                FROM pbr_news
                WHERE ns_title = :ns_title
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ns_title", $nsTitle);
        $stmt->execute();
        $check = $stmt->fetch();

        if ($check) {
            messageError("หัวข้อข่าวนี้ มีอยู่แล้ว", $locationError);
        } else {
            $folderUploads = "../uploads/img_news/";
            $allowedExtensions = ['png', 'jpg', 'jpeg'];
            $maxFileSize = 2 * 1024 * 1024; // Max file size (2 MB)

            // Function to generate unique file name
            function generateUniqueNewsCover($extension, $folder)
            {
                do {
                    $fileName = 'ns_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $extension;
                } while (file_exists($folder . $fileName));
                return $fileName;
            }

            if (empty($nsNewCover)) {

                $fileDefault = 'default.png'; // Default image file
                $filePath = $folderUploads . $fileDefault;

                // Check if default file exists
                if (!file_exists($filePath)) {
                    messageError("ไม่มีไฟล์ภาพชื่อ default.png ในโฟลเดอร์ uploads/img_news/", $locationError);
                }


                $fileSize = filesize($filePath); // Get file size (bytes)
                $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                // Validate file
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์ภาพต้องเป็นประเภท png, jpg, jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                $newCover = generateUniqueNewsCover($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newCover;

                // Copy default image to new file
                if (copy($filePath, $targetFilePath)) {
                    $sql = "INSERT INTO pbr_news (ns_title, nst_id, ns_cover, ns_detail, ns_status) 
                            VALUES (:ns_title, :nst_id, :ns_cover, :ns_detail, :ns_status)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":ns_title", $nsTitle);
                    $stmt->bindParam(":nst_id", $nstId);
                    $stmt->bindParam(":ns_cover", $newCover);
                    $stmt->bindParam(":ns_detail", $nsDetail);
                    $stmt->bindParam(":ns_status", $nsStatus);
                    $stmt->execute();

                    // Insert Success
                    if ($stmt->rowCount() > 0) {

                        $lastInsertId = $conn->lastInsertId();

                        $sql = "INSERT pbr_news_views (ns_id, nst_id)
                                VALUES (:ns_id, :nst_id)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(":ns_id",  $lastInsertId);
                        $stmt->bindParam(":nst_id", $nstId);
                        $stmt->execute();
                    }
                } else {
                    messageError("คัดลอกไฟล์ผิดพลาด", $locationError);
                }
            } else {
                // มีการเปลี่ยนรูป
                $fileExtension = strtolower(pathinfo($nsNewCover, PATHINFO_EXTENSION));
                $fileSize = $_FILES["ns_newCover"]["size"];

                // Validate file type and size
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์รูปภาพต้องเป็น png, jpg หรือ jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                $newCover = generateUniqueNewsCover($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newCover;

                // Move uploaded file to target directory
                if (move_uploaded_file($_FILES["ns_newCover"]["tmp_name"], $targetFilePath)) {
                    $sql = "INSERT INTO pbr_news (ns_title, nst_id, ns_cover, ns_detail, ns_status) 
                            VALUES (:ns_title, :nst_id, :ns_cover, :ns_detail, :ns_status)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":ns_title", $nsTitle);
                    $stmt->bindParam(":nst_id", $nstId);
                    $stmt->bindParam(":ns_cover", $newCover);
                    $stmt->bindParam(":ns_detail", $nsDetail);
                    $stmt->bindParam(":ns_status", $nsStatus);
                    $stmt->execute();

                    // Insert Success
                    if ($stmt->rowCount() > 0) {

                        $lastInsertId = $conn->lastInsertId();

                        $sql = "INSERT pbr_news_views (ns_id, nst_id)
                        VALUES (:ns_id, :nst_id)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(":ns_id",  $lastInsertId);
                        $stmt->bindParam(":nst_id", $nstId);
                        $stmt->execute();
                    }
                } else {
                    messageError("เกิดข้อผิดพลาดในการอัปโหลดไฟล์", $locationError);
                }
            }
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }


    if (empty($_SESSION["error"])) {
        $_SESSION["success"] = "เพิ่มข้อมูลข่าวสำเร็จ";
        header("Location:news_show.php");
    }
} else {
    header("Location: news_show.php");
    exit();
}
