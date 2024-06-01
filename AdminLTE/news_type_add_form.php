<?php
require_once("../db/connect.php");

$titlePage = "เพิ่มประเภทข่าว";

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
                    <form id="form" novalidate action="news_type_add.php" method="POST" enctype="multipart/form-data">
                        <div class="row">

                            <!-- left column -->
                            <div class="col-md-6">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        ข้อมูลประเภทข่าว
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="nst_name">ชื่อประเภทข่าว : </label><span class="text-danger">*</span>
                                            <input type="text" name="nst_name" class="form-control" placeholder="ระบุ ชื่อประเภทข่าวท่องเที่ยว ของคุณ" maxlength="70">
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
                                        รูปเกี่ยวกับประเภทข่าว
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="nst_id">รูปประเภทข่าว :</label>
                                            <img class="rounded mx-auto d-block border" style="width:300px; height:200px" id="nst_img" name="nst_img" src="../uploads/img_news_type/default.png">
                                        </div>
                                        <div class="form-group">
                                            <label for="formFile" class="form-label">รูปประเภทข่าวใหม่ :</label><span class="text-danger"> (ขนาดไฟล์ไม่เกิน 2 MB)</span>
                                            <input class="form-control" id="nst_newImg" name="nst_newImg" type="file" accept="image/png,image/jpg,image/jpeg">
                                        </div>
                                    </div>

                                </div>
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>สถานะประเภทข่าว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="custom-control custom-radio py-2">
                                            <input class="custom-control-input custom-control-input-success" type="radio" name="nst_status" id="1" value="1" checked>
                                            <label for="1" class="custom-control-label">แสดง</label>
                                        </div>
                                        <div class="custom-control custom-radio pb-5">
                                            <input class="custom-control-input custom-control-input-danger" type="radio" name="nst_status" id="0" value="0">
                                            <label for="0" class="custom-control-label">ไม่แสดง</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        จัดการข้อมูล
                                    </div>
                                    <div class="card-footer">
                                        <a href="news_type_show.php" class="btn btn-secondary me-2">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>ยกเลิก</span>
                                        </a>
                                        <button type="submit" name="btn-add" class="btn btn-primary">
                                            <i class="fa-regular fa-square-plus"></i>
                                            บันทึกข้อมูล
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
                    nst_name: {
                        required: true,
                        maxlength: 50
                    },

                },
                messages: {
                    nst_name: {
                        required: "กรุณาระบุ ชื่อประเภทข่าว",
                        maxlength: "ชื่อประเภทข่าว ต้องไม่เกิน 50 ตัวอักษร"
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


    <!-- preview New Img, check file type, file size  -->
    <script>
        document.getElementById('nst_newImg').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2 MB in bytes

            if (file && allowedTypes.includes(file.type) && file.size <= maxSize) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('nst_img').src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                // Reset the input
                event.target.value = '';
                // Reset to the original image
                document.getElementById('nst_img').src = '../uploads/img_news_type/default.png';

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