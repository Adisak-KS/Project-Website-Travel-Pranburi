<?php
$titlePage = "ระงับการใช้งาน";
require_once("../db/connect.php");

if (isset($_SESSION["adm_id"])) {
    try {
        $admId = $_SESSION["adm_id"];

        $sql = "SELECT adm_id, adm_username, adm_status
                FROM pbr_admin
                WHERE adm_id = :adm_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":adm_id", $admId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            $_SESSION["error"] = "ไม่พบผู้ดูแลระบบนี้";
            header("Location: index.php");
            exit;
        }elseif($result["adm_status"] == 1){
            $_SESSION["adm_id"] = $result["adm_id"];
            header("Location: index.php");
            exit;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    header("Location: login_form.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("include/head.php") ?>
</head>

<body class="hold-transition login-page">

    <!-- /.login-logo -->
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="alert alert-light text-center w-100" role="alert">
            <h1 class="pt-2">ไม่มีสิทธิ์เข้าใช้งานระบบ</h1>
            <i class="fa-solid fa-face-tired fa-10x text-danger py-2"></i>
            <hr class="border border-white">
            <p class="text-dark">ชื่อผู้ใช้ : <?php echo $result["adm_username"]; ?></p>
            <p class="text-dark">
                <?php if ($result["adm_status"] != 1) { ?>
                    <p>สถานะ : <span class="text-danger">  ระงับการใช้งาน</span></p>
                <?php } ?>
            </p>
            <p class="text-danger">!! คุณเข้าสู่ระบบสำเร็จ แต่ไม่มีสิทธิ์ใช้งาน กรุณาติดต่อผู้ดูแลระบบ !!</p>

            <div class="text-center">
                <a href="../index.php" class="text-dark me-3">กลับหน้าหลัก</a>
                <a href="logout.php" class="text-dark">เข้าสู่ระบบอีกครั้ง</a>
            </div>
        </div>
    </div>
    <!-- /.card -->

    <!-- /.login-box -->

    <?php require_once("include/script.php") ?>

</body>

</html>
<?php require_once("../include/sweetalert2.php") ?>