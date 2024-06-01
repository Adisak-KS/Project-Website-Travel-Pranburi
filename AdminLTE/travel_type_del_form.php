<?php
require_once("../db/connect.php");

$titlePage = "ลบประเภทสถานที่ท่องเที่ยว";

// แสดงข้อมูล ประเถทสถานที่ท่องเที่ยว ตาม tvt_id
if (isset($_GET["id"])) {
    $base64Encoded = $_GET["id"];

    $base64Encoded = $_SESSION["base64Encoded"] = $base64Encoded; // นำ base64Encoded มาเก็บใน session และเก็บใน
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


        // แสดงจำนวนสถานที่ท่องเที่ยวที่อยู่ในประเภทนี้
        $sql = "SELECT COUNT(*) AS total_travel 
                FROM pbr_travel
                WHERE tvt_id = :tvt_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tvt_id", $tvtId);
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalTravel = $total['total_travel'];
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
                    <form novalidate method="post">
                        <div class="row">

                            <!-- left column -->
                            <div class="col-md-6">
                                <!-- jquery validation -->
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>ข้อมูลประเภทสถานที่ท่องเที่ยว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="tvt_id">รหัสประเภทสถานที่ท่องเที่ยว : </label>
                                            <input type="text" name="tvt_id" class="form-control" value="<?php echo $result["tvt_id"]; ?>" readonly>
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
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>รูปเกี่ยวกับประเภทสถานที่ท่องเที่ยว</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="tvt_id">รูปประเภทสถานที่ท่องเที่ยว :</label>
                                            <img class="rounded mx-auto d-block border" style="width:300px; height:200px" id="tvt_img" name="tvt_img" src="../uploads/img_travel_type/<?php echo $result["tvt_img"]; ?>">
                                            <input class="form-control" type="hidden" name="tvt_img" value="<?php echo $result["tvt_img"]; ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <i class="fa-solid fa-trash"></i>
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
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>จัดการข้อมูลเมื่อ :</span>
                                        <span><strong><?php echo $result["time"]; ?></strong></span>
                                    </div>
                                    <div class="card-footer">
                                        <?php if (empty($totalTravel)) { ?>
                                            <p class="text-primary">ไม่มีสถานที่ท่องเที่ยว อยู่ในประเภทนี้ ลบได้เลย</p>
                                            <a href="travel_type_show.php" class="btn btn-secondary me-2">
                                                <i class="fa-solid fa-xmark"></i>
                                                <span>ยกเลิก</span>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-delete" data-tvt_id="<?php echo $result["tvt_id"]; ?>" data-tvt_img="<?php echo $result["tvt_img"]; ?>">
                                                <i class="fa-solid fa-trash"></i>
                                                <span>ลบข้อมูล</span>
                                            </button>
                                        <?php } else { ?>
                                            
                                            <p class="text-danger"><?php echo "*มีสถานที่ท่องเที่ยว " . $totalTravel . " แห่ง อยู่ในประเภทนี้ กรุณาลบ หรือ เปลี่ยนประเภทสถานที่ท่องเที่ยวก่อน <a href='travel_show.php'>คลิกที่นี่</a>" ?></p>
                                            <a href="travel_type_show.php" class="btn btn-secondary me-2">
                                                <i class="fa-solid fa-xmark"></i>
                                                <span>ยกเลิก</span>
                                            </a>
                                            <button type="button" class="btn btn-danger" disabled>
                                                <i class="fa-solid fa-trash"></i>
                                                <span>ลบข้อมูล</span>
                                            </button>
                                        <?php } ?>
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

    <script>
        $(".btn-delete").click(function(e) {
            e.preventDefault();
            let tvtId = $(this).data('tvt_id');
            let tvtImg = $(this).data('tvt_img');

            deleteConfirm(tvtId, tvtImg);
        });

        function deleteConfirm(tvtId, tvtImg) {
            Swal.fire({
                icon: "warning",
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณต้องการลบข้อมูลนี้ใช่ไหม!",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'ยกเลิก',
                confirmButtonText: 'ใช่, ลบข้อมูลเลย!',
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        $.ajax({
                                url: 'travel_type_del.php',
                                type: 'POST',
                                data: {
                                    tvt_id: tvtId,
                                    tvt_img: tvtImg
                                },
                            })
                            .done(function() {
                                // การลบสำเร็จ ทำการ redirect ไปยังหน้า travel_type_show.php
                                document.location.href = 'travel_type_show.php';
                            })
                            .fail(function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'ไม่สำเร็จ',
                                    text: 'เกิดข้อผิดพลาดที่ ajax !',
                                });
                            });
                    });
                },
            });
        }
    </script>

</body>

</html>
<?php require_once("../include/sweetalert2.php") ?>