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
                    <form id="form" action="setting_edit.php" method="post" enctype="multipart/form-data">
                        <div class="row">

                            <!-- left column -->
                            <div class="col-md-6">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>ข้อมูลการตั้งค่า</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="st_id">รหัสการตั้งค่า : </label>
                                            <input type="text" name="st_id" class="form-control" value="<?php echo $result["st_id"]; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="st_list">รายการตั้งค่า : </label>
                                            <input type="text" name="st_list" class="form-control" value="<?php echo $result["st_list"]; ?>" disabled>
                                        </div>
                                        <div class="form-group">

                                            <!-- Update Website Name  -->
                                            <?php if ($result["st_id"] == 1) { ?>
                                                <label for="st_detail">ชื่อเว็บไซต์ : </label>
                                                <input type="text" name="st_newWebName" class="form-control" value="<?php echo $result["st_detail"]; ?>" placeholder="กรุณาระบุ ชื่อเว็บไซต์">
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>

                            <!-- Update Favicon  -->
                            <?php if ($result["st_id"] == 2) { ?>
                                <!-- right column -->
                                <div class="col-md-6">
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            <span>สัญลักษณ์เว็บไซต์ (Favicon)</span>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label class="form-label py-2" for="st_detail">สัญลักษณ์เว็บไซต์ (Favicon) :</label>
                                                <img class="rounded-circle mx-auto d-block border" style="width:150px; height:150px" id="st_detail" name="st_detail" src="../uploads/img_web_setting/<?php echo $result["st_detail"]; ?>">
                                                <input class="form-control" type="hidden" name="st_detail" value="<?php echo $result["st_detail"]; ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="formFile" class="form-label">สัญลักษณ์เว็บไซต์ (Favicon) ใหม่ :</label><span class="text-danger"> (ขนาดไฟล์ไม่เกิน 2 MB)</span>
                                                <input class="form-control" id="st_newFavicon" name="st_newFavicon" type="file" accept=".png,.ico,.svg">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            <?php } ?>

                            <!-- Update Logo  -->
                            <?php if ($result["st_id"] == 3) { ?>
                                <!-- right column -->
                                <div class="col-md-6">
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            <span>สัญลักษณ์เว็บไซต์ (Logo)</span>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label class="form-label py-2" for="st_detail">สัญลักษณ์เว็บไซต์ (Logo) :</label>
                                                <img class="rounded-circle mx-auto d-block border" style="width:150px; height:150px" id="st_detail" name="st_detail" src="../uploads/img_web_setting/<?php echo $result["st_detail"]; ?>">
                                                <input class="form-control" type="hidden" name="st_detail" value="<?php echo $result["st_detail"]; ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="formFile" class="form-label">สัญลักษณ์เว็บไซต์ (Logo) ใหม่ :</label><span class="text-danger"> (ขนาดไฟล์ไม่เกิน 2 MB)</span>
                                                <input class="form-control" id="st_newLogo" name="st_newLogo" type="file" accept="image/png,image/jpg,image/jpeg">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            <?php } ?>


                            <div class="col-md-12">
                                <div class="card card-warning">
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
                                        <button type="submit" name="btn-edit" class="btn btn-warning">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            <span>แก้ไขข้อมูล</span>
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

    <!-- preview New Logo, check file type, file size  -->
    <script>
        document.getElementById('st_newLogo').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2 MB in bytes

            if (file && allowedTypes.includes(file.type) && file.size <= maxSize) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('st_detail').src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                // Reset the input
                event.target.value = '';
                // Reset to the original image
                document.getElementById('st_detail').src = '../uploads/img_web_setting/<?php echo $result["st_detail"]; ?>';

                // Show an alert if the file is not valid
                if (allowedTypes.includes(file.type) && file.size > maxSize) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'คำเตือน',
                        text: 'ขนาดไฟล์เกิน 2 MB',
                    });
                }
            }
        });
    </script>
    <!-- preview New Favico, check file type, file size  -->
    <script>
        document.getElementById('st_newFavicon').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const allowedTypes = ['image/png', 'image/x-icon', 'image/svg+xml'];
            const maxSize = 2 * 1024 * 1024; // 2 MB in bytes

            if (file && allowedTypes.includes(file.type) && file.size <= maxSize) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('st_detail').src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                // Reset the input
                event.target.value = '';
                // Reset to the original image
                document.getElementById('st_detail').src = '../uploads/img_web_setting/<?php echo $result["st_detail"]; ?>';

                // Show an alert if the file is not valid
                if (allowedTypes.includes(file.type) && file.size > maxSize) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'คำเตือน',
                        text: 'ขนาดไฟล์เกิน 2 MB',
                    });
                }
            }
        });
    </script>

    <script>
        $(function() {
            $('#form').validate({
                rules: {
                    st_id: {
                        required: true,
                    },
                    st_newWebName: {
                        required: true,
                        maxlength: 20
                    },
                    st_newFavicon: {
                        required: true,
                        accept: "image/png,image/x-icon,image/svg+xml"
                    },
                    st_newLogo: {
                        required: true,
                        accept: "image/png,image/jpg,image/jpeg",
                    },


                },
                messages: {
                    st_id: {
                        required: "กรุณาระบุ รหัส",
                    },
                    st_newWebName: {
                        required: "กรุณาระบุ ชื่อเว็บไซต์",
                        maxlength: "ชื่อเว็บไซต์ ต้องไม่เกิน 20 ตัวอักษร"
                    },
                    st_newFavicon: {
                        required: "กรุณาระบุ ไฟล์ Favicon",
                        accept: "ต้องเป็นไฟล์ประเภท .png .ico หรือ .svg เท่านั้น",
                    },
                    st_newLogo: {
                        required: "กรุณาระบุ ไฟล์ Logo",
                        accept: "ต้องเป็นไฟล์ประเภท .png .jpg หรือ .jpeg เท่านั้น",
                    },
                },

                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>


</body>

</html>