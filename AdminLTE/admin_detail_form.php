<?php
require_once("../db/connect.php");

$titlePage = "รายละเอียดผู้ดูแลระบบ";

// แสดงข้อมูล Admin ตาม Adm_id
if (isset($_GET["id"])) {
    $base64Encoded = $_GET["id"];
    $base64Decoded = base64_decode($base64Encoded);

    $salt1 = $_SESSION["salt1"];
    $salt2 = $_SESSION["salt2"];

    // แยกส่วน salt1, ID ที่ไม่เข้ารหัส, และ salt2
    $salt1Length = mb_strlen($salt1, 'UTF-8');
    $salt2Length = mb_strlen($salt2, 'UTF-8');

    $salt1 = substr($base64Decoded, 0, $salt1Length);
    $saltedId = substr($base64Decoded, $salt1Length, -$salt2Length);
    $salt2 = substr($base64Decoded, -$salt2Length);

    // สร้างค่า originalId โดยตัดทิ้ง salt ทั้งสองด้าน
    $originalId = str_replace([$salt1, $salt2], '', $saltedId);

    $admId = $originalId;

    try {
        $sql = "SELECT * FROM pbr_admin 
                WHERE adm_id = :adm_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":adm_id", $admId);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            header("Location: admin_show.php");
            exit();
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    header("Location: admin_show.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("include/head.php") ?>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        <?php require_once("include/navbar.php") ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php require_once("include/aside.php") ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <?php require_once("include/page_header.php") ?>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <form novalidate action="admin_add.php" method="post" enctype="multipart/form-data">
                        <div class="row">

                            <!-- left column -->
                            <div class="col-md-6">
                                <!-- jquery validation -->
                                <div class="card card-info">
                                    <div class="card-header">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>ข้อมูลส่วนตัว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="adm_id">รหัสผู้ดูแลระบบ : </label>
                                            <input type="text" name="adm_id" class="form-control" value="<?php echo $result["adm_id"]; ?>" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_fname">ชื่อ : </label>
                                            <input type="text" name="adm_fname" class="form-control" value="<?php echo $result["adm_fname"]; ?>" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_lname">นามสกุล : </label>
                                            <input type="text" name="adm_lname" class="form-control" value="<?php echo $result["adm_lname"]; ?>" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_username">ชื่อผู้ใช้ (Username) : </label>
                                            <input type="text" name="adm_username" class="form-control" value="<?php echo $result["adm_username"] ?>" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_email">อีเมล : </label>
                                            <input type="email" name="adm_email" class="form-control" value="<?php echo $result["adm_email"]; ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>

                            <!-- right column -->
                            <div class="col-md-6">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>รูปผู้ใช้งาน</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="adm_id">รูปภาพผู้ใช้งาน : </label>
                                            <img class="rounded-circle mx-auto d-block border" style="width:150px; height:150px" id="adm_profile" name="adm_profile" src="../uploads/profile_admin/<?php echo $result["adm_profile"]; ?>">
                                        </div>
                                    </div>

                                </div>
                                <div class="card card-info">
                                    <div class="card-header">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>สถานะบัญชีผู้ใช้งาน</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="custom-control custom-radio py-2">
                                            <input class="custom-control-input custom-control-input-success" type="radio" name="adm_status" id="1" value="1" <?php if ($result["adm_status"] === 1) echo "checked" ?> disabled>
                                            <label for="1" class="custom-control-label">ใช้งานได้</label>
                                        </div>
                                        <div class="custom-control custom-radio pb-5">
                                            <input class="custom-control-input custom-control-input-danger" type="radio" name="adm_status" id="0" value="0" <?php if ($result["adm_status"] === 0) echo "checked" ?> disabled>
                                            <label for="0" class="custom-control-label">ระงับการใช้งานได้</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class="col-md-12">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>จัดการข้อมูลเมื่อ :</span>
                                        <span><strong><?php echo $result["time"]; ?></strong></span>
                                    </div>
                                    <div class="card-footer">
                                        <a href="admin_show.php" class="btn btn-secondary me-2">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>ยกเลิก</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <?php require_once("include/footer.php") ?>

    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <?php require_once("include/script.php") ?>
</body>

</html>