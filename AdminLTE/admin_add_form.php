<?php
require_once("../db/connect.php");

$titlePage = "เพิ่มผู้ดูแลระบบ";

// แสดงข้อมูล Admin ทั้งหมด

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
                    <form id="form" novalidate action="admin_add.php" method="POST" enctype="multipart/form-data">
                        <div class="row">

                            <!-- left column -->
                            <div class="col-md-6">
                                <!-- jquery validation -->
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>ข้อมูลส่วนตัว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="adm_fname">ชื่อ : </label><span class="text-danger">*</span>
                                            <input type="text" name="adm_fname" class="form-control" placeholder="ระบุ ชื่อจริง ของคุณ" maxlength="70">
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_lname">นามสกุล : </label><span class="text-danger">*</span>
                                            <input type="text" name="adm_lname" class="form-control" placeholder="ระบุ นามสกุล ของคุณ" maxlength="70">
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_username">ชื่อผู้ใช้ (Username) : </label><span class="text-danger">*</span>
                                            <input type="text" name="adm_username" class="form-control" placeholder="ระบุ ชื่อผู้ใช้ (Username) ของคุณ" maxlength="70">
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_password" class="form-label">รหัสผ่าน<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="adm_password" id="adm_password" placeholder="ระบุ รหัสผ่าน" maxlength="255">
                                                <button class="btn btn-outline-secondary password-toggle" type="button">
                                                    <i class="fa-solid fa-eye-slash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_password" class="form-label">ยืนยันรหัสผ่าน<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="adm_confirmPassword" placeholder="กรุณายืนยันรหัสผ่าน อีกครั้ง" maxlength="255">
                                                <button class="btn btn-outline-secondary password-toggle" type="button">
                                                    <i class="fa-solid fa-eye-slash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="adm_email">อีเมล : </label><span class="text-danger">*</span>
                                            <input type="email" name="adm_email" class="form-control" placeholder="ระบุ อีเมล ของคุณ" maxlength="70">
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>

                            <!-- right column -->
                            <div class="col-md-6">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>รูปผู้ใช้งาน</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="adm_id">รูปภาพผู้ใช้งาน : </label>
                                            <img class="rounded-circle mx-auto d-block border" style="width:150px; height:150px" id="adm_profile" name="adm_profile" src="../uploads/profile_admin/default.png">
                                        </div>
                                        <div class="form-group">
                                            <label for="formFile" class="form-label">รูปภาพผู้ใช้งานใหม่ :</label><span class="text-danger"> (ขนาดไฟล์ไม่เกิน 2 MB)</span>
                                            <input class="form-control" id="adm_newProfile" name="adm_newProfile" type="file" accept="image/png,image/jpg,image/jpeg">
                                        </div>
                                    </div>

                                </div>
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>สถานะบัญชี</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="custom-control custom-radio py-2">
                                            <input class="custom-control-input custom-control-input-success" type="radio" name="adm_status" id="1" value="1">
                                            <label for="1" class="custom-control-label">ใช้งานได้</label>
                                        </div>
                                        <div class="custom-control custom-radio pb-5">
                                            <input class="custom-control-input custom-control-input-danger" type="radio" name="adm_status" id="0" value="0" checked>
                                            <label for="0" class="custom-control-label">ระงับการใช้งานได้</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>จัดการข้อมูล</span>
                                    </div>
                                    <div class="card-footer">
                                        <a href="admin_show.php" class="btn btn-secondary me-2">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>ยกเลิก</span>
                                        </a>
                                        <button type="submit" name="btn-add-admin" class="btn btn-primary">
                                            <i class="fa-regular fa-square-plus"></i>
                                            <span>บันทึกข้อมูล</span>
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

    <!-- form validation  -->
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
                    adm_username: {
                        required: true,
                        pattern: /^[a-zA-Z][a-zA-Z0-9_]+$/,
                        minlength: 6,
                        maxlength: 70
                    },
                    adm_password: {
                        required: true,
                        pattern: /^[^\u0E00-\u0E7F\s]+$/, // ถ้าต้องการให้เช็คภาษาไทยเป็นเงื่อนไข
                        minlength: 8,

                    },
                    adm_confirmPassword: {
                        required: true,
                        equalTo: "#adm_password" // ต้องเหมือนกับค่าของ adm_password

                    },
                    adm_email: {
                        required: true,
                        email: true,
                        minlength: 10,
                        maxlength: 65
                    },
                    adm_status: {
                        required: true,
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
                    adm_username: {
                        required: "กรุณาระบุ ชื่อผู้ใช้ (Username) ที่ต้องการ",
                        pattern: "ตัวแรกต้องเป็น a-z, A-Z และมีได้เฉพาะ a-z, A-Z, 0-9 และ _ เท่านั้น",
                        minlength: "ชื่อผู้ใช้ (Username) ต้องมี 6 ตัวอักษร ขึ้นไป",
                        maxlength: "ชื่อผู้ใช้ (Username) ต้องไม่เกิน 70 ตัวอักษร"
                    },
                    adm_password: {
                        required: "กรุณาระบุ รหัสผ่าน ที่ต้องการ",
                        pattern: "ห้ามมีภาษาไทย และเว้นวรรค",
                        minlength: "ชื่อผู้ใช้ (Username) ต้องมี 8 ตัวอักษร ขึ้นไป",
                    },
                    adm_confirmPassword: {
                        required: "กรุณา ยืนยันรหัสผ่าน อีกครั้ง",
                        equalTo: "รหัสผ่านไม่ถูกต้อง"
                    },
                    adm_email: {
                        required: "กรุณาระบุ อีเมล ที่ต้องการ",
                        email: "รูปแบบอีเมล ไม่ถูกต้อง",
                        minlength: "อีเมล ต้องมี 10 ตัวอักษร ขึ้นไป",
                        maxlength: "อีเมล ต้องไม่เกิน 65 ตัวอักษร"
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
                document.getElementById('adm_profile').src = '../uploads/profile_admin/default.png ?>';


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

</body>

</html>
<?php require_once("../include/sweetalert2.php") ?>