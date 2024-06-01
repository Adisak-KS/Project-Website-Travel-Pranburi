<?php
require_once("../db/connect.php");

$titlePage = "แก้ไขข่าว";

// แสดงข้อมูล travel ตาม tv_id
if (isset($_GET["id"])) {
    $base64Encoded = $_GET["id"];

    $_SESSION["base64Encoded"] = $_GET["id"];
    // นำ base64Encoded มาเก็บใน session และเก็บใน
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

    $nsId = $originalId;

    try {

        $sql = "SELECT pbr_news.*, 
                    pbr_news_type.nst_name, 
                    SUM(pbr_news_views.nsv_view) AS total_views
                FROM pbr_news
                LEFT JOIN pbr_news_type ON pbr_news.nst_id = pbr_news_type.nst_id
                LEFT JOIN pbr_news_views ON pbr_news.ns_id = pbr_news_views.ns_id
                WHERE pbr_news.ns_id = :ns_id
                GROUP BY pbr_news.ns_id, pbr_news_type.nst_name";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ns_id", $nsId);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            header("Location: news_show.php");
            exit();
        }

        $sql = "SELECT nst_id, nst_name
                FROM pbr_news_type";
        $stmt = $conn->query($sql);
        $stmt->execute();
        $newsType = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    header("Location: news_show.php");
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
                    <form id="form" novalidate action="news_edit.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- left column -->
                            <div class="col-md-6">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>ข้อมูลข่าว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="ns_id">รหัสข่าว : </label><span class="text-danger">*</span>
                                            <input type="text" name="ns_id" class="form-control" placeholder="ระบุ หัวข้อข่าว" maxlength="70" value="<?php echo $result["ns_id"]; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="ns_title">หัวข้อข่าว : </label><span class="text-danger">*</span>
                                            <textarea name="ns_title" placeholder="ระบุ หัวข้อข่าว" maxlength="70" class="form-control"><?php echo $result["ns_title"]; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="nsv_view">ยอดการเข้าชม (ครั้ง) : </label>
                                            <input type="text" name="nsv_view" class="form-control" maxlength="70" value="<?php echo number_format($result["total_views"]) ?>" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="nst_id">ประเภทข่าว : </label><span class="text-danger">*</span>
                                            <select class="form-select" name="nst_id" aria-label="Default select example">
                                                <?php if ($newsType) { ?>
                                                    <option value="" selected>กรุณาเลือก ประเภทข่าว</option>
                                                    <?php foreach ($newsType as $row) { ?>
                                                        <option value="<?php echo $row["nst_id"]; ?>" <?php if ($row["nst_id"] ==  $result["nst_id"]) echo "selected" ?>>
                                                            <?php echo $row["nst_name"]; ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <option value="" selected>กรุณาเลือก ประเภทข่าว</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class="col-md-6">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>รูปปกข่าว</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="ns_cover">รูปปกภาพข่าว : </label>
                                            <img class="rounded mx-auto d-block border " style="width:250px; height:150px" id="ns_cover" name="ns_cover" src="../uploads/img_news/<?php echo $result["ns_cover"]; ?>">
                                            <input type="hidden" class="form-control" id="ns_cover" name="ns_cover" value="<?php echo $result["ns_cover"]; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="formFile" class="form-label">รูปปกข่าวใหม่ :</label><span class="text-danger"> (ขนาดไฟล์ไม่เกิน 2 MB)</span>
                                            <input class="form-control" id="ns_newCover" name="ns_newCover" id="ns_newCover" type="file" accept="image/png,image/jpg,image/jpeg">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>รายละเอียดเกี่ยวกับข่าว</span>
                                </div>

                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="ns_detail">รายละเอียด : </label><span class="text-danger">*</span>
                                        <textarea name="ns_detail" class="form-control" id="ckEditor" cols="30" rows="4" placeholder="ระบุ รายละเอียดสถานที่ท่องเที่ยว"><?php echo $result["ns_detail"] ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>

                        <div class="col-md-12">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>สถานะการแสดงข่าว</span>
                                </div>

                                <div class="card-body">
                                <p class="text-danger">*จะแสดงให้ผู้ชมทั่วไปเมื่อ ประเภทข่าว มีสถานะ <span class="text-success">แสดง</span></p>
                                    <div class="custom-control custom-radio py-2">
                                        <input class="custom-control-input custom-control-input-success" type="radio" name="ns_status" id="1" value="1" <?php if ($result["ns_status"] == "1") echo "checked" ?>>
                                        <label for="1" class="custom-control-label">แสดงข่าว</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input custom-control-input-danger" type="radio" name="ns_status" id="0" value="0" <?php if ($result["ns_status"] != "1") echo "checked" ?>>
                                        <label for="0" class="custom-control-label">ไม่แสดงข่าว</label>
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
                                    <a href="news_show.php" class="btn btn-secondary me-2">
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

    <!-- ckEditor  -->
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
                document.getElementById('ns_cover').src = '../uploads/img_news/<?php echo $result["ns_cover"] ?>';

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