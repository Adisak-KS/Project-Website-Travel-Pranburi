<?php
require_once("../db/connect.php");

$titlePage = "รายละเอียดช่องทางติดต่อ";

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

    $ctId = $originalId;

    try {
        $sql = "SELECT * FROM pbr_contact
                WHERE ct_id = :ct_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ct_id", $ctId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            header("Location: contact_show.php");
            exit();
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    header("Location: contact_show.php");
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
                                    <span>ข้อมูลช่องทางติดต่อ</span>
                                </div>

                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="ct_id">รหัสช่องทางติดต่อ : </label>
                                        <input type="text" name="ct_id" class="form-control" value="<?php echo $result["ct_id"]; ?>" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="ct_list">ชื่อช่องทางติดต่อ : </label>
                                        <input type="text" name="ct_list" class="form-control" value="<?php echo $result["ct_list"]; ?>" disabled>
                                    </div>
                                    <div class="form-group">

                                        <?php if ($result["ct_id"] == 1) { ?>

                                            <label for="ct_detail">อีเมล : </label>
                                            <?php if (empty($result["ct_detail"])) { ?>
                                                <p class="text-danger">*ไม่ได้กำหนดอีเมล</p>
                                            <?php } else { ?>
                                                <input type="email" name="ct_detail" class="form-control" value="<?php echo $result["ct_detail"]; ?>" disabled>
                                            <?php } ?>

                                        <?php } elseif ($result["ct_id"] == 2) { ?>

                                            <label for="ct_detail">เบอร์โทรติดต่อ : </label>
                                            <?php if (empty($result["ct_detail"])) { ?>
                                                <p class="text-danger">*ไม่ได้กำหนดเบอร์เบอร์โทรติดต่อ ของ <?php echo $result["ct_list"]; ?></p>
                                            <?php } else { ?>
                                                <input type="text" name="ct_detail" class="form-control" value="<?php echo $result["ct_detail"]; ?>" disabled>
                                            <?php } ?>

                                        <?php } elseif ($result["ct_id"] == 9) { ?>
                                            
                                            <label for="ct_detail">ที่อยู่ : </label>
                                            <?php if (empty($result["ct_detail"])) { ?>
                                                <p class="text-danger">*ไม่ได้กำหนดที่อยู่ติดต่อ</p>
                                            <?php } else { ?>
                                                <input type="text" name="ct_detail" class="form-control" value="<?php echo $result["ct_detail"]; ?>" disabled>
                                            <?php } ?>

                                        <?php } else { ?>

                                            <label for="ct_detail">ลิงค์ URL : </label>
                                            <?php if (empty($result["ct_detail"])) { ?>
                                                <p class="text-danger">*ไม่ได้กำหนดลิงค์ URL ของ <?php echo $result["ct_list"]; ?></p>
                                            <?php } else { ?>
                                                <input type="text" name="ct_detail" class="form-control" value="<?php echo $result["ct_detail"]; ?>" disabled>
                                            <?php } ?>

                                        <?php } ?>
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
                                    <span>สถานะการแสดงช่องทางติดต่อ</span>
                                </div>

                                <div class="card-body">
                                    <div class="custom-control custom-radio py-2">
                                        <input class="custom-control-input custom-control-input-success" type="radio" name="ct_status" id="1" value="1" <?php if ($result["ct_status"] == "1") echo "checked" ?> disabled>
                                        <label for="1" class="custom-control-label">แสดงช่องทางติดต่อ</label>
                                    </div>
                                    <div class="custom-control custom-radio pb-5">
                                        <input class="custom-control-input custom-control-input-danger" type="radio" name="ct_status" id="0" value="0" <?php if ($result["ct_status"] != "1") echo "checked" ?> disabled>
                                        <label for="0" class="custom-control-label">ไม่แสดงช่องทางติดต่อ</label>
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
                                    <a href="contact_show.php" class="btn btn-secondary me-2 my-2">
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