<?php
require_once("../db/connect.php");

if (isset($_POST["tv_id"])) {
    $tvId = $_POST["tv_id"];
    $tvCover = $_POST["tv_cover"];

    try {
        $sql = "DELETE FROM pbr_travel
                WHERE tv_id = :tv_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tv_id", $tvId);
        $stmt->execute();

        // Folder ที่เก็บไฟล์
        $folderUploads = '../uploads/img_travel/';

        // ลบรูปเดิม
        if (!empty($tvCover) && file_exists($folderUploads . $tvCover)) {
            unlink($folderUploads . $tvCover);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $_SESSION["success"] = "ลบข้อมูลสถานที่ท่องเที่ยวสำเร็จ";
    header("refresh:1; url=travel_show.php");
} else {
    require_once("travel_show.php");
    exit;
}
