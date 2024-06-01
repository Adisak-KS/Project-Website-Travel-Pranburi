<?php
require_once("../db/connect.php");

if (isset($_POST["btn-edit"])) {
    $tvId = $_POST["tv_id"];
    $tvName = $_POST["tv_name"];
    $tvtId  = $_POST["tvt_id"];
    $tvDetail = $_POST["tv_detail"];
    $tvStatus = $_POST["tv_status"];
    $tvCover = $_POST["tv_cover"];
    $tvNewCover = $_FILES["tv_newCover"]["name"];
    $tvNewVideo = $_POST["tv_newVideo"];
    $tvNewLocation = $_POST["tv_newLocation"];

    $id = $_SESSION["base64Encoded"];

    $locationError = "Location:travel_edit_form.php?id=$id";
    $locationSuccess = "Location:travel_show.php";

    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }

    //ตรวจสอบค่าว่าง
    if (empty($tvName) || empty($tvtId) || !isset($tvDetail)) {
        messageError("กรุณากรอก ชื่อสถานที่ท่องเที่ยว, ประเภทสถานที่ท่องเที่ยว, รายละเอียด, สถานะ", $locationError);
    } elseif (mb_strlen($tvName, 'UTF-8') > 50) {
        messageError("ชื่อสถานที่ท่องเที่ยวต้องไม่เกิน 50 ตัวอักษร", $locationError);
    }


    try {
        // ตรวจสอบ ชื่อสถานที่ท่องเที่ยวซ้ำ
        $sql = "SELECT tv_name
                FROM pbr_travel
                WHERE tv_name = :tv_name AND tv_id != :tv_id
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tv_name", $tvName);
        $stmt->bindParam(":tv_id", $tvId);
        $stmt->execute();
        $check = $stmt->fetch();

        if ($check) {
            messageError("ชื่อสถานที่ท่องเที่ยวนี้ มีอยู่แล้ว", $locationError);
        } else {

            // หากมีการเปลี่ยนรูปปก
            if (!empty($tvNewCover)) {

                $folderUploads = '../uploads/img_travel/'; // Folder to store files
                $allowedExtensions = ['png', 'jpg', 'jpeg'];
                $maxFileSize = 2 * 1024 * 1024; // Max file size (2 MB)

                // Function to generate unique file name
                function generateUniqueTravelCoverEdit($extension, $folder)
                {
                    do {
                        $fileName = 'tv_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $extension;
                    } while (file_exists($folder . $fileName));
                    return $fileName;
                }

                // มีการเปลี่ยนรูป
                $fileExtension = strtolower(pathinfo($tvNewCover, PATHINFO_EXTENSION));
                $fileSize = $_FILES["tv_newCover"]["size"];

                // Validate file type and size
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์รูปภาพต้องเป็น png, jpg หรือ jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                $newCover = generateUniqueTravelCoverEdit($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newCover;


                // Move uploaded file to target directory
                if (move_uploaded_file($_FILES["tv_newCover"]["tmp_name"], $targetFilePath)) {

                    // ลบรูปเดิม
                    if (!empty($tvCover) && file_exists($folderUploads . $tvCover)) {
                        unlink($folderUploads . $tvCover);
                    }

                    $sql = "UPDATE pbr_travel
                            SET tv_cover = :tv_cover
                            WHERE tv_id = :tv_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":tv_cover", $newCover);
                    $stmt->bindParam(":tv_id", $tvId);
                    $stmt->execute();
                } else {
                    messageError("เกิดข้อผิดพลาดในการอัปโหลดไฟล์", $locationError);
                }
            }

            // หากมีการเปลี่ยนวิดิโอ
            if (!empty($tvNewVideo)) {

                if (!preg_match('/^<iframe\s+[^>]*src="https:\/\/www\.youtube\.com\/embed\/[^"]*"[^>]*><\/iframe>|<iframe\s+[^>]*src="https:\/\/www\.youtube-nocookie\.com\/embed\/[^"]*"[^>]*><\/iframe>$/', $tvNewVideo)) {
                    messageError("รูปแบบ Embed Youtube ไม่ถูกต้อง", $locationError);
                } else {

                    $sql = "UPDATE pbr_travel
                            SET tv_video = :tv_video
                            WHERE tv_id = :tv_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":tv_video", $tvNewVideo);
                    $stmt->bindParam(":tv_id", $tvId);
                    $stmt->execute();
                }
            }

            // หากมีการเปลี่ยน Google map
            if (!empty($tvNewLocation)) {

                if (!preg_match('/^<iframe src="https:\/\/www\.google\.com\/maps\/embed\?.*"><\/iframe>$/', $tvNewLocation)) {
                    messageError("รูปแบบ Embed Google Map ไม่ถูกต้อง", $locationError);
                } else {

                    $sql = "UPDATE pbr_travel
                            SET tv_location = :tv_location
                            WHERE tv_id = :tv_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":tv_location", $tvNewLocation);
                    $stmt->bindParam(":tv_id", $tvId);
                    $stmt->execute();
                }
            }

            // update Travel
            $sql = "UPDATE pbr_travel
                    SET tv_name = :tv_name,
                        tvt_id = :tvt_id,
                        tv_detail = :tv_detail,
                        tv_status = :tv_status,
                        time = NOW()
                    WHERE tv_id = :tv_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":tv_name", $tvName);
            $stmt->bindParam(":tvt_id", $tvtId);
            $stmt->bindParam(":tv_detail", $tvDetail);
            $stmt->bindParam(":tv_status", $tvStatus);
            $stmt->bindParam(":tv_id", $tvId);
            $stmt->execute();


            // update travel views
            $sql = "UPDATE pbr_travel_views
                    SET tvt_id = :tvt_id
                    WHERE tv_id = :tv_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":tvt_id", $tvtId);
            $stmt->bindParam(":tv_id", $tvId);
            $stmt->execute();
        }

        if (empty($_SESSION["error"])) {
            $_SESSION["success"] = "แก้ไขข้อมูลสถานที่ท่องเที่ยวสำเร็จ";
            header("Location: travel_show.php");
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    header("Location: travel_show.php");
    exit;
}
