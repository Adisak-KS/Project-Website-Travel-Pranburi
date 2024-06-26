<?php
require_once("../db/connect.php");

$titlePage = "แก้ไขผู้ดูแลระบบ";

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
                    <form id="form" novalidate action="admin_edit.php" method="post" enctype="multipart/form-data">
                        <div class="row">

                            <!-- left column -->
                            <div class="col-md-6">
                                <!-- jquery validation -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>ข้อมูลส่วนตัว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="adm_id">รหัสผู้ดูแลระบบ : </label>
                                            <input type="text" name="adm_id" class="form-control" value="<?php echo $result["adm_id"]; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_fname">ชื่อ : </label><span class="text-danger">*</span>
                                            <input type="text" name="adm_fname" class="form-control" value="<?php echo $result["adm_fname"]; ?>" placeholder="ระบุ ชื่อจริง ของคุณ" maxlength="70">
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_lname">นามสกุล : </label><span class="text-danger">*</span>
                                            <input type="text" name="adm_lname" class="form-control" value="<?php echo $result["adm_lname"]; ?>" placeholder="ระบุ นามสกุล ของคุณ" maxlength="70">
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_username">ชื่อผู้ใช้ (Username) : </label>
                                            <input type="text" name="adm_username" class="form-control" value="<?php echo $result["adm_username"] ?>" placeholder="ระบุ ชื่อผู้ใช้ (Username) ของคุณ" maxlength="70" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_email">อีเมล : </label>
                                            <input type="email" name="adm_email" class="form-control" value="<?php echo $result["adm_email"]; ?>" placeholder="ระบุ อีเมล ของคุณ" maxlength="70" readonly>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>

                            <!-- right column -->
                            <div class="col-md-6">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>รูปผู้ใช้งาน</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="adm_id">รูปภาพผู้ใช้งาน :</label>
                                            <img class="rounded-circle mx-auto d-block border" style="width:150px; height:150px" id="adm_profile" src="../uploads/profile_admin/<?php echo $result["adm_profile"]; ?>">
                                            <input class="form-control" type="hidden" name="adm_profile" value="<?php echo $result["adm_profile"]; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="formFile" class="form-label">รูปภาพผู้ใช้ใหม่ :</label><span class="text-danger"> (ขนาดไฟล์ไม่เกิน 2 MB)</span>
                                            <input class="form-control" id="adm_newProfile" name="adm_newProfile" type="file" accept="image/png,image/jpg,image/jpeg">
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>สถานะบัญชีผู้ใช้งาน</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="custom-control custom-radio py-2">
                                            <input class="custom-control-input custom-control-input-success" type="radio" name="adm_status" id="1" value="1" <?php if ($result["adm_status"] == "1") echo "checked" ?>>
                                            <label for="1" class="custom-control-label">ใช้งานได้</label>
                                        </div>
                                        <div class="custom-control custom-radio pb-5">
                                            <input class="custom-control-input custom-control-input-danger" type="radio" name="adm_status" id="0" value="0" <?php if ($result["adm_status"] != "1") echo "checked" ?> >
                                            <label for="0" class="custom-control-label">ระงับการใช้งานได้</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class="col-md-12">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>จัดการข้อมูลเมื่อ :</span>
                                        <span><strong><?php echo $result["time"]; ?></strong></span>
                                    </div>
                                    <div class="card-footer">
                                        <a href="admin_show.php" class="btn btn-secondary me-2">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>ยกเลิก</span>
                                        </a>
                                        <button type="submit" name="btn-edit" class="btn btn-warning">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            แก้ไขข้อมูล
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
        $(function() {
            // $.validator.setDefaults({
            //     submitHandler: function() {
            //         alert("Form successful submitted!");
            //     }
            // });
            $('#form').validate({
                rules: {
                    adm_fname: {
                        required: true,
                        pattern: /^[a-zA-Zก-๙\s']+$/,
                        maxlength: 70
                    },
                    adm_lname: {
                        required: true,
                        pattern: /^[a-zA-Zก-๙\s']+$/,
                        maxlength: 70
                    },
                    adm_status: {
                        required: true,
                    },
                    adm_email: {
                        email: true
                    },
                    adm_newProfile: {
                        accept: "image/png,image/jpg,image/jpeg",
                    },
                },
                messages: {
                    adm_fname: {
                        required: "กรุณาระบุ ชื่อจริง",
                        pattern: "ห้ามมีตัวเลขหรือสัญลักษณ์",
                        maxlength: "ชื่อจริงต้องไม่เกิน 70 ตัวอักษร"
                    },
                    adm_lname: {
                        required: "กรุณาระบุ นามสกุลจริง",
                        pattern: "ห้ามมีตัวเลขหรือสัญลักษณ์",
                        maxlength: "ชื่อจริงต้องไม่เกิน 70 ตัวอักษร"
                    },
                    adm_email: {
                        email: "รูปแบบอีเมล ไม่ถูกต้อง"
                    },
                    adm_status: {
                        required: "กรุณาระบุ สถานะการใช้งาน",
                    },
                    adm_newProfile: {
                        accept: "ต้องเป็นไฟล์ประเภท .png .jpg หรือ .jpeg เท่านั้น",
                    }
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

    <!-- preview New Profile, check file type, file size  -->
    <script>
        document.getElementById('adm_newProfile').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2 MB in bytes

            if (file && allowedTypes.includes(file.type) && file.size <= maxSize) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('adm_profile').src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                // Reset the input
                event.target.value = '';
                // Reset to the original image
                document.getElementById('adm_profile').src = '../uploads/profile_admin/<?php echo $result["adm_profile"]; ?>';

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

    <!-- preview New Profile, check file type-->
    <!-- <script>
        document.getElementById('adm_newProfile').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];

            if (file && allowedTypes.includes(file.type)) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('adm_profile').src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                event.target.value = ''; // Reset the input
                // Reset to the original image
                document.getElementById('adm_profile').src = '../uploads/profile_admin/<?php echo $result["adm_profile"]; ?>';
            }
        });
    </script> -->

    <!-- <script>
        document.getElementById('adm_newProfile').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('adm_profile').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script> -->
</body>

</html>
<?php require_once("../include/sweetalert2.php") ?>