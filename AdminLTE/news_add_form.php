<?php
require_once("../db/connect.php");

$titlePage = "เพิ่มข่าว / ประชาสัมพันธ์";


// แสดงข้อมูล News Type
try {
    $sql = "SELECT nst_id, nst_name
            FROM pbr_news_type";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ไม่มีประเภทข่าว
    if (!$result) {
        $_SESSION["error"] = "กรุณาเพิ่มประเภทข่าวอย่างน้อย 1 รายการ";
        header("Location: news_type_show.php");
        exit;
    }
} catch (PDOException $e) {
    echo $e->getMessage();
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
                    <form id="form" novalidate action="news_add.php" method="POST" enctype="multipart/form-data">
                        <div class="row">

                            <!-- left column -->
                            <div class="col-md-6">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>ข้อมูลข่าว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="ns_title">หัวข้อข่าว : </label><span class="text-danger">*</span>
                                            <textarea name="ns_title" placeholder="ระบุ หัวข้อข่าว" maxlength="70" class="form-control"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="nsv_view">ยอดการเข้าชม (ครั้ง) : </label>
                                            <input type="text" name="nsv_view" class="form-control" maxlength="70" value="0" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="nst_id">ประเภทข่าว : </label><span class="text-danger">*</span>
                                            <select class="form-select" name="nst_id" aria-label="Default select example">
                                                <option value="" selected>กรุณาเลือก ประเภทข่าว</option>
                                                <?php foreach ($result as $row) { ?>
                                                    <option value="<?php echo $row["nst_id"]; ?>">
                                                        <?php echo $row["nst_name"]; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
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
                                        <span>รูปปกเกี่ยวกับข่าว</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="ns_cover">รูปปกข่าว :</label>
                                            <img class="rounded mx-auto d-block border" style="width:300px; height:200px" id="ns_cover" name="ns_cover" src="../uploads/img_travel/default.png">
                                        </div>
                                        <div class="form-group">
                                            <label for="formFile" class="form-label">รูปปกข่าวใหม่ :</label><span class="text-danger"> (ขนาดไฟล์ไม่เกิน 2 MB)</span>
                                            <input class="form-control" id="ns_newCover" name="ns_newCover" type="file" accept="image/png,image/jpg,image/jpeg">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>รายละเอียดเกี่ยวกับข่าว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="ns_detail">รายละเอียด : </label><span class="text-danger">*</span>
                                            <textarea name="ns_detail" class="form-control" id="ckEditor" cols="30" rows="4" placeholder="ระบุ รายละเอียดข่าว"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>สถานะข่าว</span>
                                    </div>

                                    <div class="card-body">
                                        <p class="text-danger">*จะแสดงให้ผู้ชมทั่วไปเมื่อ ประเภทข่าว มีสถานะ <span class="text-success">แสดง</span></p>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input custom-control-input-success" type="radio" name="ns_status" id="1" value="1">
                                            <label for="1" class="custom-control-label">แสดง ข่าว</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input custom-control-input-danger" type="radio" name="ns_status" id="0" value="0" checked>
                                            <label for="0" class="custom-control-label">ไม่แสดง สข่าว</label>
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
                                        <a href="news_show.php" class="btn btn-secondary me-2">
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
        ClassicEditor
            .create(document.querySelector('#ckEditor'))
            .catch(error => {
                console.error(error);
            });
    </script>

    <!-- preview New Img, check file type, file size  -->
    <script>
        document.getElementById('ns_newCover').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2 MB in bytes

            if (file && allowedTypes.includes(file.type) && file.size <= maxSize) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('ns_cover').src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                // Reset the input
                event.target.value = '';
                // Reset to the original image
                document.getElementById('ns_cover').src = '../uploads/img_travel/default.png';

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


    <!-- validation form  -->
    <script>
        $(function() {
            $('#form').validate({
                rules: {
                    ns_title: {
                        required: true,
                        maxlength: 70
                    },
                    nst_id: {
                        required: true,
                    },
                    ns_newCover: {
                        accept: "image/png,image/jpeg"
                    },
                    ns_detail: {
                        required: true
                    },
                },
                messages: {
                    ns_title: {
                        required: "กรุณาระบุ หัวข้อข่าว",
                        maxlength: "หัวข้อข่าว ต้องไม่เกิน 70 ตัวอักษร"
                    },
                    nst_id: {
                        required: "กรุณาระบุ ประเภทข่าว"
                    },
                    ns_newCover: {
                        accept: "ต้องเป็นไฟล์ประเภท png, jpg, jpeg เท่านั้น"
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