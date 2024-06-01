<?php

if (empty($_SESSION["adm_id"])) {
    $_SESSION["error"] = "กรุณาเข้าสู่ระบบ ก่อนใช้งาน";
    header("Location: login_form.php");
    exit;
}



if (isset($_SESSION["adm_id"])) {
    $admId = $_SESSION["adm_id"];
    $sql = "SELECT *
            FROM pbr_admin
            WHERE adm_id = :adm_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":adm_id", $admId);
    $stmt->execute();
    $use = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$use) {
        $_SESSION["error"] = "กรุณาเข้าสู่ระบบ ก่อนใช้งาน";
        header("Location: login_form.php");
        exit;
    } elseif ($use["adm_status"] != 1) {
        $_SESSION["error"] = "ไม่ได้รับอนุญาติให้ใช้งาน";
        header("Location: blocked.php");
        exit;
    }
}

?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="acc_show.php" class="nav-link">ช้อมูลส่วนตัว</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Full Screen  -->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
    </ul>
</nav>