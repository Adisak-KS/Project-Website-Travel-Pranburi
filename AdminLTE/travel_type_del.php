<?php
require_once("../db/connect.php");

if (isset($_POST["tvt_id"])) {
    $tvtId = $_POST["tvt_id"];
    $tvtImg = $_POST["tvt_img"];

    try {
        $sql = "DELETE FROM pbr_travel_type
                    WHERE tvt_id = :tvt_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tvt_id", $tvtId);
        $stmt->execute();

        // Folder ที่เก็บไฟล์
        $folderUploads = '../uploads/img_travel_type/';

        // ลบรูปเดิม
        if (!empty($tvtImg) && file_exists($folderUploads . $tvtImg)) {
            unlink($folderUploads . $tvtImg);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $_SESSION["success"] = "ลบข้อมูลประเภทสถานที่ท่องเที่ยวสำเร็จ";
    header("refresh:1; url=travel_type_show.php");
} else {
    require_once("travel_type_show.php");
    exit;
}
