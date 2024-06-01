<?php
require_once("../db/connect.php");

if (isset($_POST["adm_id"])) {
    $admId = $_POST["adm_id"];
    $admProfile = $_POST["adm_profile"];


    try {
        $sql = "DELETE FROM pbr_admin
                WHERE adm_id = :adm_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":adm_id", $admId);
        $stmt->execute();

        // Folder ที่เก็บไฟล์
        $folderUploads = '../uploads/profile_admin/';

        // ลบรูปเดิม
        if (!empty($admProfile) && file_exists($folderUploads . $admProfile)) {
            unlink($folderUploads . $admProfile);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $_SESSION["success"] = "ลบข้อมูลผู้ดูแลระบบสำเร็จ";
    header("refresh:1; url=admin_show.php");
} else {
    header("Location: admin_show.php");
    exit;
}
