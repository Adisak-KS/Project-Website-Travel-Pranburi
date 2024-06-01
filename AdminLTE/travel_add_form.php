<?php
require_once("../db/connect.php");

$titlePage = "เพิ่มสถานที่ท่องเที่ยว";


// แสดงข้อมูล Travel Type
try {
    $sql = "SELECT tvt_id, tvt_name
            FROM pbr_travel_type";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ไม่มีประเภทสถานที่ท่องเที่ยว
    if(!$result) {
        $_SESSION["error"] = "กรุณาเพิ่มประเภทสถานที่ท่องเที่ยวอย่างน้อย 1 รายการ";
        header("Location: travel_type_show.php");
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
                    <form id="form" novalidate action="travel_add.php" method="POST" enctype="multipart/form-data">
                        <div class="row">

                            <!-- left column -->
                            <div class="col-md-6">
                                <!-- jquery validation -->
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>ข้อมูลสถานที่ท่องเที่ยว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="tv_name">ชื่อสถานที่ท่องเที่ยว : </label><span class="text-danger">*</span>
                                            <input type="text" name="tv_name" class="form-control" placeholder="ระบุ ชื่อสถานที่ท่องเที่ยว" maxlength="70">
                                        </div>
                                        <div class="form-group">
                                            <label for="tvv_view">ยอดการเข้าชม (ครั้ง) : </label>
                                            <input type="text" name="tvv_view" class="form-control" maxlength="70" value="0" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="tvt_name">ประเภทสถานที่ท่องเที่ยว : </label><span class="text-danger">*</span>
                                            <select class="form-select" name="tvt_id" aria-label="Default select example">
                                                <option value="" selected>กรุณาเลือก ประเภทสถานที่ท่องเที่ยว</option>
                                                <?php foreach ($result as $row) { ?>
                                                    <option value="<?php echo $row["tvt_id"]; ?>">
                                                        <?php echo $row["tvt_name"]; ?>
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
                                        <span>รูปปกเกี่ยวกับสถานที่ท่องเที่ยว</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="tv_cover">รูปปกสถานที่ท่องเที่ยว :</label>
                                            <img class="rounded mx-auto d-block border" style="width:300px; height:200px" id="tv_cover" name="tv_cover" src="../uploads/img_travel/default.png">
                                        </div>
                                        <div class="form-group">
                                            <label for="formFile" class="form-label">รูปปกสถานที่ท่องเที่ยวใหม่ :</label><span class="text-danger"> (ขนาดไฟล์ไม่เกิน 2 MB)</span>
                                            <input class="form-control" id="tv_newCover" name="tv_newCover" type="file" accept="image/png,image/jpg,image/jpeg">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <!-- jquery validation -->
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>รายละเอียดเกี่ยวกับสถานที่ท่องเที่ยว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="tv_detail">รายละเอียด : </label><span class="text-danger">*</span>
                                            <textarea name="tv_detail" class="form-control" id="ckEditor" cols="30" rows="4" placeholder="ระบุ รายละเอียดสถานที่ท่องเที่ยว"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class="col-md-12">
                                <!-- jquery validation -->
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>ฝังวิดิโอจาก Youtube</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <div class="text-center">
                                                <div id="videoPreview">
                                                    <!-- แสดงตัวอย่าง video ที่นี่  -->
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="tv_video" class="form-label">ฝังวิดิโอจาก Youtube :</label>
                                                <input type="text" class="form-control" id="tv_video" name="tv_video" placeholder="ฝังคลิปวิดิโอ Youtube" oninput="validateAndPreview()">
                                            </div>
                                        </div>
                                        <div class="form-group text-center">
                                            <div id="video-preview">
                                                <!-- This is where the Googlemap -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class="col-md-12">
                                <!-- jquery validation -->
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>ข้อมูลตำแหน่งสถานที่ท่องเที่ยว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="text-center">
                                            <!-- This is where the location will be embedded -->
                                            <div id="locationPreview">

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="tv_location" class="form-label">ฝังแผนที่จาก Google Map :</label>
                                            <input type="text" class="form-control" id="tv_location" name="tv_location" placeholder="ฝังแผนที่ Google Map" oninput="validateAndPreviewLocation()">
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>


                            <div class="col-md-12">
                                <!-- jquery validation -->
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>สถานะสถานที่ท่องเที่ยว</span>
                                    </div>

                                    <div class="card-body">
                                    <p class="text-danger">*จะแสดงให้ผู้ชมทั่วไปเมื่อ ประเภทสถานที่ท่องเที่ยว มีสถานะ <span class="text-success">แสดง</span></p>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input custom-control-input-success" type="radio" name="tv_status" id="1" value="1">
                                            <label for="1" class="custom-control-label">แสดง สถานที่ท่องเที่ยว</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input custom-control-input-danger" type="radio" name="tv_status" id="0" value="0" checked>
                                            <label for="0" class="custom-control-label">ไม่แสดง สถานที่ท่องเที่ยว</label>
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
                                        <a href="travel_show.php" class="btn btn-secondary me-2">
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

    <!-- preview New Embed Youtube  -->
    <script>
        function validateAndPreview() {
            const input = document.getElementById('tv_video').value;
            const preview = document.getElementById('videoPreview');
            const regex = /^<iframe\s+[^>]*src="https:\/\/www\.youtube\.com\/embed\/[^"]*"[^>]*><\/iframe>|<iframe\s+[^>]*src="https:\/\/www\.youtube-nocookie\.com\/embed\/[^"]*"[^>]*><\/iframe>$/;

            if (regex.test(input)) {
                preview.innerHTML = input;
            } else {
                preview.innerHTML = '';
            }
        }
    </script>

    <!-- preview New Embed Googel Map  -->
    <script>
        function validateAndPreviewLocation() {
            const input = document.getElementById('tv_location').value;
            const preview = document.getElementById('locationPreview');
            const regex = /^<iframe\s+src="https:\/\/www\.google\.com\/maps\/embed\?.*"><\/iframe>$/;

            if (regex.test(input)) {
                preview.innerHTML = input;
            } else {
                preview.innerHTML = '';
            }
        }
    </script>

    <!-- preview New Img, check file type, file size  -->
    <script>
        document.getElementById('tv_newCover').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2 MB in bytes

            if (file && allowedTypes.includes(file.type) && file.size <= maxSize) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('tv_cover').src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                // Reset the input
                event.target.value = '';
                // Reset to the original image
                document.getElementById('tv_cover').src = '../uploads/img_travel/default.png';

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
                    tv_name: {
                        required: true,
                        maxlength: 50
                    },
                    tvt_id: {
                        required: true,
                    },
                    tv_newCover: {
                        accept: "image/png,image/jpeg"
                    },
                    tv_detail: {
                        required: true
                    },
                    tv_video: {
                        pattern: /^<iframe\s+[^>]*src="https:\/\/www\.youtube\.com\/embed\/[^"]*"[^>]*><\/iframe>|<iframe\s+[^>]*src="https:\/\/www\.youtube-nocookie\.com\/embed\/[^"]*"[^>]*><\/iframe>$/,
                    },
                    tv_location: {
                        pattern: /^<iframe src="https:\/\/www\.google\.com\/maps\/embed\?.*"><\/iframe>$/
                    },


                },
                messages: {
                    tv_name: {
                        required: "กรุณาระบุ ชื่อสถานที่ท่องเที่ยว",
                        maxlength: "ชื่อสถานที่ ต้องไม่เกิน 50 ตัวอักษร"
                    },
                    tvt_id: {
                        required: "กรุณาระบุ ประเภทสถานที่ท่องเที่ยว"
                    },
                    tv_newCover: {
                        accept: "ต้องเป็นไฟล์ประเภท png, jpg, jpeg เท่านั้น"
                    },
                    tv_detail: {
                        required: "กรุณาระบุ รายละเอียดสถานที่ท่องเที่ยว"
                    },
                    tv_video: {
                        pattern: "รูปแบบ embed Youtube ไม่ถูกต้อง วิธีฝังวิดิโอ ไปที่ https://www.youtube.com/ > ค้นหาวิดิโอ > เลือกแชร์ (Share) > ฝัง(Embed) > คัดลอก (COPY)"
                    },
                    tv_location: {
                        pattern: "รูปแบบ embed Google Map ไม่ถูกต้อง วิธีฝังแผนที่ ไปที่ https://www.google.co.th/maps/ > ค้นหาสถานที่ > เลือกแชร์ (Share) > ฝังแผนที่ (Embed a map) > คัดลอก HTML (COPY HTML)"
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