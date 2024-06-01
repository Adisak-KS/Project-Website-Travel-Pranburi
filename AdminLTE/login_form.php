<?php
$titlePage = "เข้าสู่ระบบ";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("include/head.php") ?>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="" class="h1">
                    <?php
                    foreach ($settings as $setting) {
                        // ตรวจสอบว่า $setting["st_id"] เท่ากับ 1 หรือไม่
                        if ($setting["st_id"] == 1) {
                            // ถ้า $setting["st_id"] เท่ากับ 1 ตรวจสอบค่าว่างของ $setting["st_detail"]
                            $webName = !empty($setting["st_detail"]) ? $setting["st_detail"] : "ชื่อเว็บไซต์";
                            // กำหนดค่าให้กับ web name
                            echo $webName;
                            // เมื่อพบ $setting["st_id"] เท่ากับ 1 ให้หยุดการทำงานของลูป foreach
                            break;
                        }
                    }
                    ?>
                </a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">เข้าสู่ระบบ</p>
                <form id="form" action="login_check.php" method="POST">
                    <div class="input-group mb-3">
                        <label for="adm_username" class="form-label">ชื่อผู้ใช้ : </label><span class="text-danger">*</span>
                        <div class="input-group">
                            <input type="text" class="form-control" name="adm_username" placeholder="กรุณากรอกชื่อผู้ใช้ หรือ อีเมล">
                            <button class="btn btn-outline-secondary bg-secondary rounded" type="button" disabled>
                                <i class="fa-solid fa-envelope"></i>
                            </button>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <label for="adm_password" class="form-label">รหัสผ่าน : </label><span class="text-danger">*</span>
                        <div class="input-group">
                            <input type="password" class="form-control" name="adm_password" placeholder="กรุณากรอกรหัสผ่าน">
                            <button class="btn btn-outline-secondary password-toggle rounded" type="button">
                                <i class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-center mt-2 mb-3">
                        <button type="submit" name="btn-login" class="btn btn-block btn-primary">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>เข้าสู่ระบบ</span>
                        </button>
                    </div>
                </form>
                <!-- <p class="mb-1">
                    <a href="forgot-password.html">I forgot my password</a>
                </p> -->
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

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


    <!-- form validation  -->
    <script>
        $(function() {
            $('#form').validate({
                rules: {
                    adm_username: {
                        required: true,
                    },
                    adm_password: {
                        required: true,

                    },
                },
                messages: {
                    adm_username: {
                        required: "กรุณาระบุ ชื่อผู้ใช้ (Username) หรือ อีเมล",
                    },
                    adm_password: {
                        required: "กรุณาระบุ รหัสผ่าน",
                    },
                },

                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.input-group').append(error);
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