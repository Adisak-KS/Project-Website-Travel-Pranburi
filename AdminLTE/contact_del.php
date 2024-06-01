<?php
require_once("../db/connect.php");

if (isset($_POST["ct_id"])) {
    $ctId = $_POST["ct_id"];
    $ctDetail = "";
    $ctStatus = 0;

    try {
        $sql = "UPDATE pbr_contact
                SET ct_detail = :ct_detail,
                    ct_status = :ct_status,
                    time = NOW()
                WHERE ct_id = :ct_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ct_detail", $ctDetail);
        $stmt->bindParam(":ct_status", $ctStatus);
        $stmt->bindParam(":ct_id", $ctId);
        $stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $_SESSION["success"] = "ลบข้อมูลช่องทางติดต่อสำเร็จ";
    header("refresh:1; url=contact_show.php");
    exit;
} else {
    header("Location: contact_show.php");
    exit;
}
