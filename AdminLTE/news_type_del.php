<?php
require_once("../db/connect.php");

if (isset($_POST["nst_id"])) {
    $nstId = $_POST["nst_id"];
    $nstImg = $_POST["nst_img"];

    try {
        $sql = "DELETE FROM pbr_news_type
                    WHERE nst_id = :nst_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":nst_id", $nstId);
        $stmt->execute();

        // Folder ที่เก็บไฟล์
        $folderUploads = '../uploads/img_news_type/';

        // ลบรูปเดิม
        if (!empty($nstImg) && file_exists($folderUploads . $nstImg)) {
            unlink($folderUploads . $nstImg);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $_SESSION["success"] = "ลบข้อมูลประเภทข่าวสำเร็จ";
    header("refresh:1; url=news_type_show.php");
} else {
    require_once("news_type_show.php");
    exit;
}
