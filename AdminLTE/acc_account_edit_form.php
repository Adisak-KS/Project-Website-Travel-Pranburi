<?php
require_once("../db/connect.php");

$titlePage = "แก้ไขข้อมูลบัญชี";

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
        $sql = "SELECT adm_id, adm_username, adm_email, time
                FROM pbr_admin 
                WHERE adm_id = :adm_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":adm_id", $admId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            header("Location: acc_show.php");
            exit();
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
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
                    <form id="form" novalidate action="acc_account_edit.php" method="post">
                        <div class="row">

                            <!-- left column -->
                            <div class="col-md-6">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>ข้อมูลชื่อผู้ใช้</span>
                                    </div>

                                    <div class="card-body">
                                        <input type="hidden" name="adm_id" class="form-control" value="<?php echo $result["adm_id"]; ?>" readonly>
                                        <div class="form-group">
                                            <label for="adm_username">ชื่อผู้ใช้ (Username) : </label>
                                            <input type="text" name="adm_username" id="adm_username" class="form-control" value="<?php echo $result["adm_username"]; ?>" placeholder="ระบุ ชื่อผู้ใช้ ของคุณ" maxlength="70" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_newUsername">ชื่อผู้ใช้ใหม่ : </label>
                                            <input type="text" name="adm_newUsername" class="form-control" placeholder="ระบุ ชื่อผู้ใช้ใหม่ ของคุณ" maxlength="70">
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>

                            <!-- right column -->
                            <div class="col-md-6">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>ข้อมูลอีเมล</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="adm_email">อีเมล : </label>
                                            <input type="email" name="adm_email" id="adm_email" class="form-control" value="<?php echo $result["adm_email"]; ?>" placeholder="ระบุ อีเมล ของคุณ" maxlength="70" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_newEmail">อีเมลใหม่ : </label>
                                            <input type="email" name="adm_newEmail" class="form-control" placeholder="ระบุ อีเมลใหม่ ของคุณ" maxlength="70">
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class="col-md-12">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>จัดการข้อมูลเมื่อ :</span>
                                        <span><strong><?php echo $result["time"]; ?></strong></span>
                                    </div>
                                    <div class="card-footer">
                                        <a href="acc_show.php" class="btn btn-secondary me-2">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>ยกเลิก</span>
                                        </a>
                                        <button type="submit" name="btn-edit" class="btn btn-info">
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
            $('#form').validate({
                rules: {
                    adm_newUsername: {
                        pattern: /^[a-zA-Z][a-zA-Z0-9_]+$/,
                        minlength: 6,
                        notEqualTo: "#adm_username",
                        maxlength: 70
                    },
                    adm_newEmail: {
                        email: true,
                        minlength: 10,
                        notEqualTo: "#adm_email",
                        maxlength: 65
                    },
                },
                messages: {
                    adm_newUsername: {
                        pattern: "ตัวแรกต้องเป็น a-z, A-Z และมีได้เฉพาะ a-z, A-Z, 0-9 และ _ เท่านั้น",
                        minlength: "ชื่อผู้ใช้ (Username) ต้องมี 6 ตัวอักษร ขึ้นไป",
                        notEqualTo: "ชื่อผู้ใช้เดิม กรุณาใช้ชื่อผู้ใช้ใหม่",
                        maxlength: "ชื่อผู้ใช้ (Username) ต้องไม่เกิน 70 ตัวอักษร"
                    },
                    adm_newEmail: {
                        email: "รูปแบบอีเมล ไม่ถูกต้อง",
                        minlength: "อีเมล ต้องมี 10 ตัวอักษร ขึ้นไป",
                        notEqualTo: "อีเมลเดิม กรุณาใช้อีเมลใหม่",
                        maxlength: "อีเมล ต้องไม่เกิน 65 ตัวอักษร"
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
                        icon: 'info',
                        title: 'คำเตือน',
                        text: 'ขนาดไฟล์เกิน 2 MB',
                    });
                }
            }
        });
    </script>
</body>

</html>
<?php require_once("../include/sweetalert2.php") ?>