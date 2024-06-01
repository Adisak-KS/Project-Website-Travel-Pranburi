<?php
require_once("../db/connect.php");

$titlePage = "ลบสถานที่ท่องเที่ยว";

// แสดงข้อมูล travel ตาม tv_id
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

    $tvId = $originalId;

    try {

        $sql = "SELECT pbr_travel.*, 
                        pbr_travel_type.tvt_name, 
                        SUM(pbr_travel_views.tvv_view) AS total_views
                FROM pbr_travel
                LEFT JOIN pbr_travel_type ON pbr_travel.tvt_id = pbr_travel_type.tvt_id
                INNER JOIN pbr_travel_views ON pbr_travel.tv_id = pbr_travel_views.tv_id
                WHERE pbr_travel.tv_id = :tv_id
                GROUP BY pbr_travel.tv_id, pbr_travel_type.tvt_name";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tv_id", $tvId);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            header("Location: travel_show.php");
            exit();
        }

        $sql = "SELECT tvt_id, tvt_name
                FROM pbr_travel_type";
        $stmt = $conn->query($sql);
        $stmt->execute();
        $travelType = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    header("Location: travel_show.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("include/head.php") ?>
    <script src="../ckEditor5-41.4.2/build/ckeditor.js"></script>
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
                    <form id="form" novalidate action="travel_edit.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- left column -->
                            <div class="col-md-6">
                                <!-- jquery validation -->
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>ข้อมูลสถานที่ท่องเที่ยว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="tv_id">รหัสสถานที่ท่องเที่ยว : </label><span class="text-danger">*</span>
                                            <input type="text" name="tv_id" class="form-control" placeholder="ระบุ ชื่อสถานที่ท่องเที่ยว" maxlength="70" value="<?php echo $result["tv_id"] ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="tv_name">ชื่อสถานที่ท่องเที่ยว : </label><span class="text-danger">*</span>
                                            <input type="text" name="tv_name" class="form-control" placeholder="ระบุ ชื่อสถานที่ท่องเที่ยว" maxlength="70" value="<?php echo $result["tv_name"] ?>" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="tvv_view">ยอดการเข้าชม (ครั้ง) : </label><span class="text-danger">*</span>
                                            <input type="text" name="tvv_view" class="form-control" maxlength="70" value="<?php echo number_format($result["total_views"]) ?>" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="tvt_name">ประเภทสถานที่ท่องเที่ยว : </label><span class="text-danger">*</span>
                                            <select class="form-select" name="tvt_id" aria-label="Default select example" disabled>
                                                <?php if ($travelType) { ?>
                                                    <option value="" selected>กรุณาเลือก ประเภทสถานที่ท่องเที่ยว</option>
                                                    <?php foreach ($travelType as $row) { ?>
                                                        <option value="<?php echo $row["tvt_id"]; ?>" <?php if ($row["tvt_id"] ==  $result["tvt_id"]) echo "selected" ?>>
                                                            <?php echo $row["tvt_name"]; ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <option value="">กรุณาเลือก ประเภทสถานที่ท่องเที่ยว</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class=" col-md-6">
                                <div class="card card-danger pb-5">
                                    <div class="card-header">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>รูปปกสถานที่ท่องเที่ยว</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="tv_cover">รูปปกภาพสถานที่ท่องเที่ยว : </label>
                                            <img class="rounded mx-auto d-block border" style="width:250px; height:150px" id="tv_cover" name="tv_cover" src="../uploads/img_travel/<?php echo $result["tv_cover"]; ?>">
                                            <input type="hidden" class="form-control" id="tv_cover" name="tv_cover" value="<?php echo $result["tv_cover"]; ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card card-danger">
                                <div class="card-header">
                                    <i class="fa-solid fa-trash"></i>
                                    <span>รายละเอียดเกี่ยวกับสถานที่ท่องเที่ยว</span>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($result["tv_detail"])) { ?>
                                        <p class="text-danger">*ไม่มีรายละเอียดเกี่ยวกับสถานที่ท่องเที่ยว</p>
                                    <?php } else { ?>
                                        <div class="form-group">
                                            <label for="tv_detail">รายละเอียด : </label><span class="text-danger">*</span>
                                        </div>
                                        <div class="border border-5 px-5 py-2">
                                            <?php echo $result["tv_detail"]; ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>

                        <div class="col-md-12">
                            <!-- jquery validation -->
                            <div class="card card-danger">
                                <div class="card-header">
                                    <i class="fa-solid fa-trash"></i>
                                    <span>ฝังวิดิโอจาก Youtube</span>
                                </div>

                                <div class="card-body">
                                    <?php if (empty($result["tv_video"])) { ?>
                                        <p class="text-danger">*ไม่มีการฝังวิดิโอ Youtube</p>
                                    <?php } else { ?>
                                        <div class="form-group">
                                            <div class="form-group text-center">
                                                <?php echo $result["tv_video"] ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>

                        <div class="col-md-12">
                            <!-- jquery validation -->
                            <div class="card card-danger">
                                <div class="card-header">
                                    <i class="fa-solid fa-trash"></i>
                                    <span>ข้อมูลตำแหน่งสถานที่ท่องเที่ยว</span>
                                </div>

                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="form-group text-center">
                                            <!-- This is where the location will be embedded -->
                                            <?php
                                            if (empty($result["tv_location"])) {
                                                echo '<p class="text-danger text-start">*ไม่มีการฝังแผนที่ Google Map</p>';
                                            } else {
                                                echo $result["tv_location"];
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>

                        <div class="col-md-12">
                            <div class="card card-danger">
                                <div class="card-header">
                                    <i class="fa-solid fa-trash"></i>
                                    <span>สถานะการแสดงสถานที่ท่องเที่ยว</span>
                                </div>

                                <div class="card-body">
                                <p class="text-danger">*จะแสดงให้ผู้ชมทั่วไปเมื่อ ประเภทสถานที่ท่องเที่ยว มีสถานะ <span class="text-success">แสดง</span></p>
                                    <div class="custom-control custom-radio py-2">
                                        <input class="custom-control-input custom-control-input-success" type="radio" name="tv_status" id="1" value="1" <?php if ($result["tv_status"] == "1") echo "checked" ?> disabled>
                                        <label for="1" class="custom-control-label">แสดงสถานที่ท่องเที่ยว</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input custom-control-input-danger" type="radio" name="tv_status" id="0" value="0" <?php if ($result["tv_status"] != "1") echo "checked" ?> disabled>
                                        <label for="0" class="custom-control-label">ไม่แสดงสถานที่ท่องเที่ยว</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card card-danger">
                                <div class="card-header">
                                    <i class="fa-solid fa-trash"></i>
                                    <span>จัดการข้อมูลเมื่อ :</span>
                                    <span><strong><?php echo $result["time"]; ?></strong></span>
                                </div>
                                <div class="card-footer">
                                    <a href="travel_show.php" class="btn btn-secondary me-2">
                                        <i class="fa-solid fa-xmark"></i>
                                        <span>ยกเลิก</span>
                                    </a>
                                    <button type="button" data-tv_id="<?php echo $result["tv_id"]; ?>" data-tv_cover="<?php echo $result["tv_cover"]; ?>" class="btn btn-danger btn-delete">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>ลบข้อมูล</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
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

    <script>
        $(".btn-delete").click(function(e) {
            e.preventDefault();
            let tvId = $(this).data('tv_id');
            let tvCover = $(this).data('tv_cover');

            deleteConfirm(tvId, tvCover);
        });

        function deleteConfirm(tvId, tvCover) {
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
                                url: 'travel_del.php',
                                type: 'POST',
                                data: {
                                    tv_id: tvId,
                                    tv_cover: tvCover
                                },
                            })
                            .done(function() {
                                // การลบสำเร็จ ทำการ redirect ไปยังหน้า travel_show.php
                                document.location.href = 'travel_show.php';
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