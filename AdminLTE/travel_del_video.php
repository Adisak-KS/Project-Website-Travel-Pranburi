<?php
require_once("../db/connect.php");

if (isset($_POST["tv_id"])) {
    $tvId = $_POST["tv_id"];

    $id = $_SESSION["base64Encoded"];
    // SET ค่าว่าง
    $nullValue = '';

    try {
        $sql = "UPDATE pbr_travel
                SET tv_video = :tv_video
                WHERE tv_id = :tv_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tv_video", $nullValue);
        $stmt->bindParam(":tv_id", $tvId);
        $stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $_SESSION["success"] = "ลบวิดิโอจาก ํYoutube สำเร็จ";
    header("url=travel_detail_form.php?id=" . $id);
} else {
    require_once("travel_show.php");
    exit;
}
