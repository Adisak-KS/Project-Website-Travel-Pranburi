<?php
require_once("../db/connect.php");

if (isset($_POST["ns_id"])) {
    $nsId = $_POST["ns_id"];
    $nsCover = $_POST["ns_cover"];

    try {
        $sql = "DELETE FROM pbr_news
                WHERE ns_id = :ns_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ns_id", $nsId);
        $stmt->execute();

        // Folder ที่เก็บไฟล์
        $folderUploads = '../uploads/img_news/';

        // ลบรูปเดิม
        if (!empty($nsCover) && file_exists($folderUploads . $nsCover)) {
            unlink($folderUploads . $nsCover);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $_SESSION["success"] = "ลบข้อมูลข่าวสำเร็จ";
    header("refresh:1; url=news_show.php");
} else {
    require_once("news_show.php");
    exit;
}
