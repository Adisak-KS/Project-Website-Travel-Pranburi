<?php
require_once("../db/connect.php");

if (isset($_POST["btn-edit"])) {
    $nsId = $_POST["ns_id"];
    $nsTitle = $_POST["ns_title"];
    $nstId = $_POST["nst_id"];
    $nsDetail = $_POST["ns_detail"];
    $nsStatus = $_POST["ns_status"];
    $nsCover = $_POST["ns_cover"];
    $nsNewCover = $_FILES["ns_newCover"]["name"];

    $id = $_SESSION["base64Encoded"];

    $locationError = "Location:news_edit_form.php?id=$id";
    $locationSuccess = "Location:news_show.php";

    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }

    if (empty($nsTitle)) {
        messageError("กรุณาระบุ หัวข้อข่าว", $locationError);
    } elseif (mb_strlen($nsTitle, 'UTF-8') > 70) {
        messageError("หัวข้อข่าว ไม่เกิน 70 ตัวอักษร", $locationError);
    }


    if (empty($nstId)) {
        messageError("กรุณาระบุ ประเภทข่าว", $locationError);
    }

    if (empty($nsDetail)) {
        messageError("กรุณาระบุ รายละเอียดข่าว", $locationError);
    }

    try {
        $sql = "SELECT ns_title
                FROM pbr_news
                WHERE ns_title = :ns_title AND ns_id != :ns_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ns_title", $nsTitle);
        $stmt->bindParam(":ns_id", $nsId);
        $stmt->execute();
        $check = $stmt->fetch();


        if ($check) {
            messageError("หัวข้อข่าวนี้ มีอยู่แล้ว", $locationError);
        } else {

            if (!empty($nsNewCover)) {

                $folderUploads = "../uploads/img_news/";
                $allowedExtensions = ['png', 'jpg', 'jpeg'];
                $maxFileSize = 2 * 1024 * 1024; // Max file size (2 MB)

                // Function to generate unique file name
                function generateUniqueNewsEditCover($extension, $folder)
                {
                    do {
                        $fileName = 'ns_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $extension;
                    } while (file_exists($folder . $fileName));
                    return $fileName;
                }

                // มีการเปลี่ยนรูป
                $fileExtension = strtolower(pathinfo($nsNewCover, PATHINFO_EXTENSION));
                $fileSize = $_FILES["ns_newCover"]["size"];

                // Validate file type and size
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์รูปภาพต้องเป็น png, jpg หรือ jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                $newCover = generateUniqueNewsEditCover($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newCover;

                // Move uploaded file to target directory
                if (move_uploaded_file($_FILES["ns_newCover"]["tmp_name"], $targetFilePath)) {

                    // ลบรูปเดิม
                    if (!empty($nsCover) && file_exists($folderUploads . $nsCover)) {
                        unlink($folderUploads . $nsCover);
                    }

                    $sql = "UPDATE pbr_news
                            SET ns_cover = :ns_cover
                            WHERE ns_id = :ns_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":ns_cover", $newCover);
                    $stmt->bindParam(":ns_id", $nsId);
                    $stmt->execute();
                } else {
                    messageError("เกิดข้อผิดพลาดในการอัปโหลดไฟล์", $locationError);
                }
            }

            $sql = "UPDATE pbr_news
                    SET ns_title = :ns_title,
                        nst_id = :nst_id,
                        ns_detail = :ns_detail,
                        ns_status = :ns_status,
                        time = NOW()
                    WHERE ns_id = :ns_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":ns_title", $nsTitle);
            $stmt->bindParam(":nst_id", $nstId);
            $stmt->bindParam(":ns_detail", $nsDetail);
            $stmt->bindParam(":ns_status", $nsStatus);
            $stmt->bindParam(":ns_id", $nsId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                // update News Views
                if (!empty($nstId)) {
                    $sql = "UPDATE pbr_news_views
                        SET nst_id = :nst_id
                        WHERE ns_id = :ns_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":nst_id", $nstId);
                    $stmt->bindParam(":ns_id", $nsId);
                    $stmt->execute();
                }
            }
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    if (empty($_SESSION["error"])) {
        $_SESSION["success"] = "แก้ไขข้อมูลข่าวสำเร็จ";
        header("Location: news_show.php");
    }
} else {
    header("Location: news_show.php");
    exit;
}
