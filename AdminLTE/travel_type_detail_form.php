<?php
require_once("../db/connect.php");

$titlePage = "รายละเอียดประเภทสถานที่ท่องเที่ยว";

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

    $tvtId = $originalId;

    try {
        $sql = "SELECT * FROM pbr_travel_type
                WHERE tvt_id = :tvt_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tvt_id", $tvtId);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            header("Location: travel_type_show.php");
            exit();
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    header("Location: travel_type_show.php");
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
                                        <span>ข้อมูลประเภทสถานที่ท่องเที่ยว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="tvt_id">รหัสประเภทสถานที่ท่องเที่ยว : </label>
                                            <input type="text" name="tvt_id" class="form-control" value="<?php echo $result["tvt_id"]; ?>" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="tvt_name">ชื่อประเภทสถานที่ท่องเที่ยว : </label>
                                            <input type="text" name="tvt_name" class="form-control" value="<?php echo $result["tvt_name"]; ?>" disabled>
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
                                        <span>รูปประเภทสถานที่ท่องเที่ยว</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="adm_id">รูปภาพประเภทสถานที่ท่องเที่ยว : </label>
                                            <img class="rounded mx-auto d-block border" style="width:250px; height:150px" id="tvt_img" name="tvt_img" src="../uploads/img_travel_type/<?php echo $result["tvt_img"]; ?>">
                                        </div>
                                        <!-- <div class="form-group">
                                            <label for="formFile" class="form-label">รูปภาพผู้ใช้ใหม่ :</label>
                                            <input class="form-control" name="adm_newProfile" type="file" accept="image/png ,image/jpg, image/jpeg">
                                        </div> -->
                                    </div>

                                </div>
                                <div class="card card-info">
                                    <div class="card-header">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>สถานะการแสดงประเภทสถานที่ท่องเที่ยว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="custom-control custom-radio py-2">
                                            <input class="custom-control-input custom-control-input-success" type="radio" name="tvt_status" id="1" value="1" <?php if ($result["tvt_status"] == "1") echo "checked" ?> disabled>
                                            <label for="1" class="custom-control-label">แสดงประเภทสถานที่ท่องเที่ยว</label>
                                        </div>
                                        <div class="custom-control custom-radio pb-5">
                                            <input class="custom-control-input custom-control-input-danger" type="radio" name="tvt_status" id="0" value="0" <?php if ($result["tvt_status"] != "1") echo "checked" ?> disabled>
                                            <label for="0" class="custom-control-label">ไม่แสดงประเภทสถานที่ท่องเที่ยว</label>
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
                                        <a href="travel_type_show.php" class="btn btn-secondary me-2">
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