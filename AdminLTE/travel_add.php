<?php
require_once("../db/connect.php");

if (isset($_POST["btn-add"])) {
    $tvName = $_POST["tv_name"];
    $tvtId = $_POST["tvt_id"];
    $tvVideo = $_POST["tv_video"];
    $tvLocation = $_POST["tv_location"];
    $tvStatus = $_POST["tv_status"];
    $tvDetail = $_POST["tv_detail"];
    $tvNewCover = $_FILES["tv_newCover"]["name"];

    $locationError = "Location:travel_add_form.php";
    $locationSuccess = "Location:travel_show.php";

    // echo "tvName :". $tvName ."<br>";
    // echo "tvtId :". $tvtId ."<br>";
    // echo "tvVideo :". $tvVideo ."<br>";
    // echo "tvLocation :". $tvLocation ."<br>";
    // echo "tvStatus :". $tvStatus ."<br>";
    // echo "tvDetail :". $tvDetail ."<br>";
    // echo "tvNewImg:". $tvNewCover ."<br>";

    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }

    // ตรวจสอบช่องว่าง
    if (empty($tvName) || empty($tvtId) || !isset($tvStatus)) {
        messageError("กรุณากรอก ข้อมูลให้ครบทุกรายการ", $locationError);
    }


    if (mb_strlen($tvName, 'UTF-8') > 50) {
        messageError("ชื่อสถานที่ท่องเที่ยว ไม่เกิน 50 ตัวอักษร", $locationError);
    }

    if (empty($tvDetail)) {
        messageError("กรุณากรอก รายละเอียดสถานที่ท่องเที่ยว", $locationError);
    }


    try {
        $sql = "SELECT tv_name
                FROM pbr_travel
                WHERE tv_name = :tv_name
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tv_name", $tvName);
        $stmt->execute();
        $check = $stmt->fetch();

        if ($check) {
            messageError("ชื่อสถานที่ท่องเที่ยวนี้ มีอยู่แล้ว", $locationError);
        } else {
            $folderUploads = '../uploads/img_travel/'; // Folder to store files
            $allowedExtensions = ['png', 'jpg', 'jpeg'];
            $maxFileSize = 2 * 1024 * 1024; // Max file size (2 MB)

            // Function to generate unique file name
            function generateUniqueTravelCoverAdd($extension, $folder)
            {
                do {
                    $fileName = 'tv_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $extension;
                } while (file_exists($folder . $fileName));
                return $fileName;
            }


            if (empty($tvNewCover)) {
                $fileDefault = 'default.png'; // Default image file
                $filePath = $folderUploads . $fileDefault;

                // Check if default file exists
                if (!file_exists($filePath)) {
                    messageError("ไม่มีไฟล์ภาพชื่อ default.png ในโฟลเดอร์ uploads/img_travel/", $locationError);
                }

                $fileSize = filesize($filePath); // Get file size (bytes)
                $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                // Validate file
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์ภาพต้องเป็นประเภท png, jpg, jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                $newCover = generateUniqueTravelCoverAdd($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newCover;

                // Copy default image to new file
                if (copy($filePath, $targetFilePath)) {
                    $sql = "INSERT INTO pbr_travel (tv_name, tvt_id, tv_video, tv_location, tv_cover, tv_detail, tv_status) 
                            VALUES (:tv_name, :tvt_id, :tv_video, :tv_location, :tv_cover, :tv_detail, :tv_status)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":tv_name", $tvName);
                    $stmt->bindParam(":tvt_id", $tvtId);
                    $stmt->bindParam(":tv_video", $tvVideo);
                    $stmt->bindParam(":tv_location", $tvLocation);
                    $stmt->bindParam(":tv_cover", $newCover);
                    $stmt->bindParam(":tv_detail", $tvDetail);
                    $stmt->bindParam(":tv_status", $tvStatus);
                    $stmt->execute();

                    $lastInsertId = $conn->lastInsertId();

                    $sql = "INSERT INTO pbr_travel_views(tv_id, tvt_id)
                            VALUES (:tv_id, :tvt_id)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":tv_id", $lastInsertId);
                    $stmt->bindParam(":tvt_id", $tvtId);
                    $stmt->execute();
                } else {
                    messageError("คัดลอกไฟล์ผิดพลาด", $locationError);
                }
            } else {
                // มีการเปลี่ยนรูป
                $fileExtension = strtolower(pathinfo($tvNewCover, PATHINFO_EXTENSION));
                $fileSize = $_FILES["tv_newCover"]["size"];

                // Validate file type and size
                if (!in_array($fileExtension, $allowedExtensions)) {
                    messageError("ไฟล์รูปภาพต้องเป็น png, jpg หรือ jpeg เท่านั้น", $locationError);
                } elseif ($fileSize > $maxFileSize) {
                    messageError("ไฟล์รูปภาพต้องมีขนาดไม่เกิน 2 MB", $locationError);
                }

                $newCover = generateUniqueTravelCoverAdd($fileExtension, $folderUploads);
                $targetFilePath = $folderUploads . $newCover;

                // Move uploaded file to target directory
                if (move_uploaded_file($_FILES["tv_newCover"]["tmp_name"], $targetFilePath)) {
                    $sql = "INSERT INTO pbr_travel (tv_name, tvt_id, tv_video, tv_location, tv_cover, tv_detail, tv_status) 
                            VALUES (:tv_name, :tvt_id, :tv_video, :tv_location, :tv_cover, :tv_detail, :tv_status)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":tv_name", $tvName);
                    $stmt->bindParam(":tvt_id", $tvtId);
                    $stmt->bindParam(":tv_video", $tvVideo);
                    $stmt->bindParam(":tv_location", $tvLocation);
                    $stmt->bindParam(":tv_cover", $newCover);
                    $stmt->bindParam(":tv_detail", $tvDetail);
                    $stmt->bindParam(":tv_status", $tvStatus);
                    $stmt->execute();

                    $lastInsertId = $conn->lastInsertId();

                    $sql = "INSERT INTO pbr_travel_views(tv_id, tvt_id)
                    VALUES (:tv_id, :tvt_id)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":tv_id", $lastInsertId);
                    $stmt->bindParam(":tvt_id", $tvtId);
                    $stmt->execute();
                } else {
                    messageError("เกิดข้อผิดพลาดในการอัปโหลดไฟล์", $locationError);
                }
            }
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    // ไม่มี error
    if (empty($_SESSION["error"])) {
        $_SESSION["success"] = "เพิ่มประเภทสถานที่ท่องเที่ยวสำเร็จ";
        header($locationSuccess);
    }
} else {
    header("Location: travel_show.php");
    exit;
}
