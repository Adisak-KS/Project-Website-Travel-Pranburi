<?php
require_once("../db/connect.php");

$titlePage = "แก้ไขข้อมูลส่วนตัว";

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
        $sql = "SELECT adm_id, adm_fname, adm_lname, adm_profile, time
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
                    <form id="form" novalidate action="acc_password_edit.php" method="post">
                        <div class="row">
                            <!-- left column -->
                            <div class="col-md-6">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>ข้อมูลส่วนตัว</span>
                                    </div>

                                    <div class="card-body">
                                        <input type="hidden" name="adm_id" class="form-control" value="<?php echo $result["adm_id"]; ?>" readonly>
                                        <div class="form-group">
                                            <label for="adm_password" class="form-label">รหัสผ่านเดิม :<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="adm_password" id="adm_password" placeholder="ระบุ รหัสผ่านเดิม" maxlength="255">
                                                <button class="btn btn-outline-secondary password-toggle" type="button">
                                                    <i class="fa-solid fa-eye-slash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_newPassword" class="form-label">รหัสผ่านใหม่ :<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="adm_newPassword" id="adm_newPassword" placeholder="ระบุ รหัสผ่านใหม่" maxlength="255">
                                                <button class="btn btn-outline-secondary password-toggle" type="button">
                                                    <i class="fa-solid fa-eye-slash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_confirmNewPassword" class="form-label">ยืนยันรหัสผ่านใหม่ :<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="adm_confirmNewPassword" id="adm_confirmNewPassword" placeholder="ระบุ ยืนยันรหัสผ่านใหม่ อีกครั้ง" maxlength="255">
                                                <button class="btn btn-outline-secondary password-toggle" type="button">
                                                    <i class="fa-solid fa-eye-slash"></i>
                                                </button>
                                            </div>
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
                                        <a href="acc_show.php" class="btn btn-secondary me-2">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>ยกเลิก</span>
                                        </a>
                                        <button type="submit" name="btn-edit" class="btn btn-warning">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            แก้ไขรหัสผ่าน
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

    <!-- Show/Hidden Password  -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggles = document.querySelectorAll('.password-toggle');

            passwordToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    const passwordField = this.previousElementSibling;
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);

                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye-slash', type === 'password');
                    icon.classList.toggle('fa-eye', type !== 'password');
                });
            });
        });
    </script>
    <script>
        $(function() {
            $('#form').validate({
                rules: {
                    adm_password: {
                        required: true,
                        pattern: /^[^\u0E00-\u0E7F\s]+$/,
                        minlength: 8,
                    },
                    adm_newPassword: {
                        required: true,
                        pattern: /^[^\u0E00-\u0E7F\s]+$/, // ถ้าต้องการให้เช็คภาษาไทยเป็นเงื่อนไข
                        minlength: 8,
                        notEqualTo: "#adm_password",
                    },
                    adm_confirmNewPassword: {
                        required: true,
                        equalTo: "#adm_newPassword",
                    },
                },
                messages: {
                    adm_password: {
                        required: "กรุณาระบุ รหัสผ่านเดิม",
                        pattern: "ห้ามมีภาษาไทย และเว้นวรรค",
                        minlength: "ชื่อผู้ใช้ (Username) ต้องมี 8 ตัวอักษร ขึ้นไป",
                    },
                    adm_newPassword: {
                        required: "กรุณาระบุ รหัสผ่านใหม่",
                        pattern: "ห้ามมีภาษาไทย และเว้นวรรค",
                        minlength: "ชื่อผู้ใช้ (Username) ต้องมี 8 ตัวอักษร ขึ้นไป",
                        notEqualTo: "รหัสผ่านเดิม กรุณาใช้รหัสผ่านใหม่"
                    },
                    adm_confirmNewPassword: {
                        required: "กรุณาระบุ รหัสผ่านใหม่อีกครั้ง",
                        equalTo: "ยืนยันรหัสผ่านใหม่ ไม่ถูกต้อง"
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
<?php require_once("../include/sweetalert2.php") ?>