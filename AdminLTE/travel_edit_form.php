<?php
require_once("../db/connect.php");

$titlePage = "แก้ไขสถานที่ท่องเที่ยว";

// แสดงข้อมูล travel ตาม tv_id
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

    $tvId = $originalId;

    try {

        $sql = "SELECT pbr_travel.*, 
                        pbr_travel_type.tvt_name, 
                        SUM(pbr_travel_views.tvv_view) AS total_views
                FROM pbr_travel
                LEFT JOIN pbr_travel_type ON pbr_travel.tvt_id = pbr_travel_type.tvt_id
                INNER JOIN pbr_travel_views ON pbr_travel.tv_id = pbr_travel_views.tv_id
                WHERE pbr_travel.tv_id = :tv_id
                GROUP BY pbr_travel.tv_id, pbr_travel_type.tvt_name";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tv_id", $tvId);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            header("Location: travel_show.php");
            exit();
        }

        $sql = "SELECT tvt_id, tvt_name
                FROM pbr_travel_type";
        $stmt = $conn->query($sql);
        $stmt->execute();
        $travelType = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    header("Location: travel_show.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("include/head.php") ?>
    <script src="../ckEditor/build/ckeditor.js"></script>
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
                    <form id="form" novalidate action="travel_edit.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- left column -->
                            <div class="col-md-6">
                                <!-- jquery validation -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>ข้อมูลสถานที่ท่องเที่ยว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="tv_id">รหัสสถานที่ท่องเที่ยว : </label><span class="text-danger">*</span>
                                            <input type="text" name="tv_id" class="form-control" placeholder="ระบุ ชื่อสถานที่ท่องเที่ยว" maxlength="70" value="<?php echo $result["tv_id"] ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="tv_name">ชื่อสถานที่ท่องเที่ยว : </label><span class="text-danger">*</span>
                                            <input type="text" name="tv_name" class="form-control" placeholder="ระบุ ชื่อสถานที่ท่องเที่ยว" maxlength="70" value="<?php echo $result["tv_name"] ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="tvv_view">ยอดการเข้าชม (ครั้ง) : </label><span class="text-danger">*</span>
                                            <input type="text" name="tvv_view" class="form-control" maxlength="70" value="<?php echo number_format($result["total_views"]) ?>" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="tvt_name">ประเภทสถานที่ท่องเที่ยว : </label><span class="text-danger">*</span>
                                            <select class="form-select" name="tvt_id" aria-label="Default select example">
                                                <?php if ($travelType) { ?>
                                                    <option value="" selected>กรุณาเลือก ประเภทสถานที่ท่องเที่ยว</option>
                                                    <?php foreach ($travelType as $row) { ?>
                                                        <option value="<?php echo $row["tvt_id"]; ?>" <?php if ($row["tvt_id"] ==  $result["tvt_id"]) echo "selected" ?>>
                                                            <?php echo $row["tvt_name"]; ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <option value="" selected>กรุณาเลือก ประเภทสถานที่ท่องเที่ยว</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class=" col-md-6">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>รูปปกสถานที่ท่องเที่ยว</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body ">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="tv_cover">รูปปกภาพสถานที่ท่องเที่ยว : </label>
                                            <img class="rounded mx-auto d-block border" style="width:250px; height:150px" id="tv_cover" name="tv_cover" src="../uploads/img_travel/<?php echo $result["tv_cover"]; ?>">
                                            <input type="hidden" class="form-control" id="tv_cover" name="tv_cover" value="<?php echo $result["tv_cover"]; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="formFile" class="form-label">รูปปกสถานที่ท่องเที่ยวใหม่ :</label><span class="text-danger"> (ขนาดไฟล์ไม่เกิน 2 MB)</span>
                                            <input class="form-control" id="tv_newCover" name="tv_newCover" type="file" accept="image/png,image/jpg,image/jpeg">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>รายละเอียดเกี่ยวกับสถานที่ท่องเที่ยว</span>
                                </div>

                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="tv_detail">รายละเอียด : </label><span class="text-danger">*</span>
                                        <textarea name="tv_detail" class="form-control" id="ckEditor" cols="30" rows="4" placeholder="ระบุ รายละเอียดสถานที่ท่องเที่ยว"><?php echo $result["tv_detail"] ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>

                        <div class="col-md-12">
                            <!-- jquery validation -->
                            <div class="card card-warning">
                                <div class="card-header">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>ฝังวิดิโอจาก Youtube</span>
                                </div>

                                <div class="card-body">
                                    <?php if (empty($result["tv_video"])) { ?>
                                        <div class="text-center">
                                            <div id="videoPreview">
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="form-group text-center">
                                            <div id="videoPreview">
                                                <?php echo $result["tv_video"] ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label for="tv_newVideo" class="form-label">ฝัง URL Embed Youtube ใหม่ :</label>
                                        <input type="text" class="form-control" id="tv_newVideo" name="tv_newVideo" placeholder="ฝังคลิปวิดิโอ Youtube ใหม่" oninput="validateAndPreview()">
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>

                        <div class="col-md-12">
                            <!-- jquery validation -->
                            <div class="card card-warning">
                                <div class="card-header">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>ข้อมูลตำแหน่งสถานที่ท่องเที่ยว</span>
                                </div>

                                <div class="card-body">
                                    <div class="form-group text-center">
                                        <!-- This is where the location will be embedded -->
                                        <div id="locationPreview">
                                            <?php echo $result["tv_location"]; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="tv_newLocation" class="form-label">ฝังแผนที่ Google Map ใหม่ :</label>
                                        <input type="text" class="form-control" id="tv_newLocation" name="tv_newLocation" placeholder="ฝังแผนที่ Google Map ใหม่" oninput="validateAndPreviewLocation()">
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>

                        <div class="col-md-12">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>สถานะการแสดงสถานที่ท่องเที่ยว</span>
                                </div>

                                <div class="card-body">
                                <p class="text-danger">*จะแสดงให้ผู้ชมทั่วไปเมื่อ ประเภทสถานที่ท่องเที่ยว มีสถานะ <span class="text-success">แสดง</span></p>
                                    <div class="custom-control custom-radio py-2">
                                        <input class="custom-control-input custom-control-input-success" type="radio" name="tv_status" id="1" value="1" <?php if ($result["tv_status"] == "1") echo "checked" ?>>
                                        <label for="1" class="custom-control-label">แสดงสถานที่ท่องเที่ยว</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input custom-control-input-danger" type="radio" name="tv_status" id="0" value="0" <?php if ($result["tv_status"] != "1") echo "checked" ?>>
                                        <label for="0" class="custom-control-label">ไม่แสดงสถานที่ท่องเที่ยว</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>จัดการข้อมูลเมื่อ :</span>
                                    <span><strong><?php echo $result["time"]; ?></strong></span>
                                </div>
                                <div class="card-footer">
                                    <a href="travel_show.php" class="btn btn-secondary me-2">
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
                    </form>
                </div>

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
                document.getElementById('tv_cover').src = '../uploads/img_travel/<?php echo $result["tv_cover"] ?>';

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

    <!-- preview New Embed Youtube  -->
    <script>
        function validateAndPreview() {
            const input = document.getElementById('tv_newVideo').value;
            const preview = document.getElementById('videoPreview');
            const regex = /^<iframe\s+[^>]*src="https:\/\/www\.youtube\.com\/embed\/[^"]*"[^>]*><\/iframe>|<iframe\s+[^>]*src="https:\/\/www\.youtube-nocookie\.com\/embed\/[^"]*"[^>]*><\/iframe>$/;

            if (regex.test(input)) {
                preview.innerHTML = input;
            } else {
                preview.innerHTML = '<?php echo $result["tv_video"] ?>';
            }
        }
    </script>

    <!-- preview New Embed Googel Map  -->
    <script>
        function validateAndPreviewLocation() {
            const input = document.getElementById('tv_newLocation').value;
            const preview = document.getElementById('locationPreview');
            const regex = /^<iframe\s+src="https:\/\/www\.google\.com\/maps\/embed\?.*"><\/iframe>$/;

            if (regex.test(input)) {
                preview.innerHTML = input;
            } else {
                preview.innerHTML = '<?php echo $result["tv_location"]; ?>';
            }
        }
    </script>

    <!-- Validation  -->
    <script>
        $(function() {
            // $.validator.setDefaults({
            //     submitHandler: function() {
            //         alert("Form successful submitted!");
            //     }
            // });
            $('#form').validate({
                rules: {
                    tv_id: {
                        required: true,
                    },
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
                    tv_newVideo: {
                        pattern: /^<iframe\s+[^>]*src="https:\/\/www\.youtube\.com\/embed\/[^"]*"[^>]*><\/iframe>|<iframe\s+[^>]*src="https:\/\/www\.youtube-nocookie\.com\/embed\/[^"]*"[^>]*><\/iframe>$/,
                    },
                    tv_newLocation: {
                        pattern: /^<iframe src="https:\/\/www\.google\.com\/maps\/embed\?.*"><\/iframe>$/
                    },
                },
                messages: {
                    tv_id: {
                        required: "กรุณาระบุ รหัสสถานที่ท่องเที่ยว",
                    },
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
                    tv_newVideo: {
                        // url: "กรุณาระบุ เป็นลิงค์ URL เท่านั้น เช่น https://www.google.co.th"
                        pattern: "รูปแบบ embed Youtube ไม่ถูกต้อง วิธีฝังวิดิโอ ไปที่ https://www.youtube.com/ > ค้นหาวิดิโอ > เลือกแชร์ (Share) > ฝัง(Embed) > คัดลอก (COPY)"
                    },
                    tv_newLocation: {
                        required: "กรุณาระบุ ตำแหน่งสถานที่ท่องเที่ยว",
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