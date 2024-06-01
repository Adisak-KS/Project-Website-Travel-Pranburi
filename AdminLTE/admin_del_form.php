<?php
require_once("../db/connect.php");

$titlePage = "ลบผู้ดูแลระบบ";

// แสดงข้อมูล Admin ตาม Adm_id
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

    $admId = $originalId;

    try {
        $sql = "SELECT * FROM pbr_admin 
                WHERE adm_id = :adm_id AND adm_id != 1";
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
                    <form novalidate action="admin_del.php" method="post">
                        <div class="row">

                            <!-- left column -->
                            <div class="col-md-6">
                                <!-- jquery validation -->
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>ข้อมูลส่วนตัว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="adm_id">รหัสผู้ดูแลระบบ : </label><span class="text-danger">*</span>
                                            <input type="text" name="adm_id" class="form-control" value="<?php echo $result["adm_id"]; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_fname">ชื่อ : </label><span class="text-danger">*</span>
                                            <input type="text" name="adm_fname" class="form-control" value="<?php echo $result["adm_fname"]; ?>" placeholder="ระบุ ชื่อจริง ของคุณ" maxlength="70" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_lname">นามสกุล : </label><span class="text-danger">*</span>
                                            <input type="text" name="adm_lname" class="form-control" value="<?php echo $result["adm_lname"]; ?>" placeholder="ระบุ นามสกุล ของคุณ" maxlength="70" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_username">ชื่อผู้ใช้ (Username) : </label><span class="text-danger">*</span>
                                            <input type="text" name="adm_username" class="form-control" value="<?php echo $result["adm_username"] ?>" placeholder="ระบุ ชื่อผู้ใช้ (Username) ของคุณ" maxlength="70" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_email">อีเมล : </label><span class="text-danger">*</span>
                                            <input type="email" name="adm_email" class="form-control" value="<?php echo $result["adm_email"]; ?>" placeholder="ระบุ อีเมล ของคุณ" maxlength="70" disabled>
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
                                        <span>รูปผู้ใช้งาน</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="adm_id">รูปภาพผู้ใช้งาน :</label>
                                            <img class="rounded-circle mx-auto d-block border" style="width:150px; height:150px" id="adm_profile" name="adm_profile" src="../uploads/profile_admin/<?php echo $result["adm_profile"]; ?>">
                                            <input class="form-control" type="hidden" name="adm_profile" value="<?php echo $result["adm_profile"]; ?>" readonly>
                                        </div>

                                    </div>
                                </div>
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>สถานะบัญชีผู้ใช้งาน</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="custom-control custom-radio py-2">
                                            <input class="custom-control-input custom-control-input-success" type="radio" name="adm_status" id="1" value="1" <?php if ($result["adm_status"] == "1") echo "checked" ?> disabled>
                                            <label for="1" class="custom-control-label">ใช้งานได้</label>
                                        </div>
                                        <div class="custom-control custom-radio pb-5">
                                            <input class="custom-control-input custom-control-input-danger" type="radio" name="adm_status" id="0" value="0" <?php if ($result["adm_status"] != "1") echo "checked" ?> disabled>
                                            <label for="0" class="custom-control-label">ระงับการใช้งานได้</label>
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
                                        <a href="admin_show.php" class="btn btn-secondary me-2">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>ยกเลิก</span>
                                        </a>

                                        <button type="button" class="btn btn-danger btn-delete" data-adm_id="<?php echo $result["adm_id"]; ?>" data-adm_profile="<?php echo $result["adm_profile"]; ?>">
                                            <i class="fa-solid fa-trash"></i>
                                            <span>ลบข้อมูล</span>
                                        </button>

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
            let admId = $(this).data('adm_id');
            let admProfile = $(this).data('adm_profile');

            deleteConfirm(admId, admProfile);
        });

        function deleteConfirm(admId, admProfile) {
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
                                url: 'admin_del.php',
                                type: 'POST',
                                data: {
                                    adm_id: admId,
                                    adm_profile: admProfile
                                },
                            })
                            .done(function() {
                                // การลบสำเร็จ ทำการ redirect ไปยังหน้า admin_show.php
                                document.location.href = 'admin_show.php';
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