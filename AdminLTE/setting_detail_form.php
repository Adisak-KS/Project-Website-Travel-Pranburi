<?php
require_once("../db/connect.php");

$titlePage = "รายละเอียดตั้งค่าเว็บไซต์";

// แสดงข้อมูล contact ตาม id
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

    $stId = $originalId;

    try {
        $sql = "SELECT * FROM pbr_setting
                WHERE st_id = :st_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":st_id", $stId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            header("Location: setting_show.php");
            exit();
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    header("Location: setting_show.php");
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

                    <div class="row">

                        <!-- left column -->
                        <div class="col-md-6">
                            <!-- jquery validation -->
                            <div class="card card-info">
                                <div class="card-header">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <span>ข้อมูลการตั้งค่า</span>
                                </div>

                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="st_id">รหัสการตั้งค่า : </label>
                                        <input type="text" name="st_id" class="form-control" value="<?php echo $result["st_id"]; ?>" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="st_detail">รายการตั้งค่า : </label>
                                        <input type="text" name="st_detail" class="form-control" value="<?php echo $result["st_list"]; ?>" disabled>
                                    </div>
                                    <div class="form-group">

                                        <?php if ($result["st_id"] == 1) { ?>
                                            <?php if (empty($result["st_detail"])) { ?>
                                                <label for="st_detail">ชื่อเว็บไซต์ : </label>
                                                <p class="text-danger">*ไม่ได้กำหนดชื่อเว็บไซต์</p>
                                            <?php } else { ?>
                                                <label for="st_detail">ชื่อเว็บไซต์ : </label>
                                                <input type="text" name="st_detail" class="form-control" value="<?php echo $result["st_detail"]; ?>" disabled>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- Show Favicon  -->
                        <?php if ($result["st_id"] == 2) { ?>
                            <div class="col-md-6">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>สัญลักษณ์เว็บไซต์ (Favicon)</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="ct_favicon">สัญลักษณ์เว็บไซต์ (Favicon) : </label>
                                            <img class="rounded-circle mx-auto d-block border" style="width:150px; height:150px" id="ct_detail" name="ct_detail" src="../uploads/img_web_setting/<?php echo $result["st_detail"]; ?>">
                                            <input type="hidden" name="ct_detail" value="<?php echo $result["st_detail"]; ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Show Logo  -->
                        <?php if ($result["st_id"] == 3) { ?>
                            <div class="col-md-6">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>สัญลักษณ์เว็บไซต์ (Logo)</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="ct_favicon">สัญลักษณ์เว็บไซต์ (Logo) : </label>
                                            <img class="rounded-circle mx-auto d-block border" style="width:150px; height:150px" id="ct_detail" name="ct_detail" src="../uploads/img_web_setting/<?php echo $result["st_detail"]; ?>">
                                            <input type="hidden" name="ct_detail" value="<?php echo $result["st_detail"]; ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="col-md-12">
                            <div class="card card-info">
                                <div class="card-header">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <span>จัดการข้อมูลเมื่อ :</span>
                                    <span><strong><?php echo $result["time"]; ?></strong></span>
                                </div>
                                <div class="card-footer">
                                    <a href="setting_show.php" class="btn btn-secondary me-2 my-2">
                                        <i class="fa-solid fa-xmark"></i>
                                        <span>ยกเลิก</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>

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