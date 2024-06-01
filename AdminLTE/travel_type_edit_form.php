<?php
require_once("../db/connect.php");

$titlePage = "แก้ไขประเภทสถานที่ท่องเที่ยว";

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

    $tvtId = $originalId;

    try {
        $sql = "SELECT * FROM pbr_travel_type 
                WHERE tvt_id = :tvt_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tvt_id", $tvtId);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            header("Location: travel_type_show.php");
            exit();
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    header("Location: travel_type_show.php");
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
                    <form id="form" novalidate action="travel_type_edit.php" method="post" enctype="multipart/form-data">
                        <div class="row">

                            <!-- left column -->
                            <div class="col-md-6">
                                <!-- jquery validation -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>ข้อมูลประเภทสถานที่ท่องเที่ยว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="tvt_id">รหัสประเภทสถานที่ท่องเที่ยว : </label>
                                            <input type="text" name="tvt_id" class="form-control" value="<?php echo $result["tvt_id"]; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="tvt_name">ชื่อประเภทสถานที่ท่องเที่ยว : </label>
                                            <input type="text" name="tvt_name" class="form-control" value="<?php echo $result["tvt_name"]; ?>"placeholder="ระบุ ชื่อประเภทสถานที่ท่องเที่ยว ของคุณ" maxlength="70">
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
                                        <span>รูปเกี่ยวกับประเภทสถานที่ท่องเที่ยว</span>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label py-2" for="tvt_id">รูปประเภทสถานที่ท่องเที่ยว :</label>
                                            <img class="rounded mx-auto d-block border" style="width:300px; height:200px" id="tvt_img" name="tvt_img" src="../uploads/img_travel_type/<?php echo $result["tvt_img"]; ?>">
                                            <input class="form-control" type="hidden" name="tvt_img" value="<?php echo $result["tvt_img"]; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="formFile" class="form-label">รูปประเภทสถานที่ท่องเที่ยวใหม่ :</label><span class="text-danger"> (ขนาดไฟล์ไม่เกิน 2 MB)</span>
                                            <input class="form-control" id="tvt_newImg" name="tvt_newImg" type="file" accept="image/png,image/jpg,image/jpeg">
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-warning">
                                    <div class="card-header">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                        <span>สถานะการแสดงประเภทสถานที่ท่องเที่ยว</span>
                                    </div>

                                    <div class="card-body">
                                        <div class="custom-control custom-radio py-2">
                                            <input class="custom-control-input custom-control-input-success" type="radio" name="tvt_status" id="1" value="1" <?php if ($result["tvt_status"] == "1") echo "checked" ?>>
                                            <label for="1" class="custom-control-label">แสดงประเภทสถานที่ท่องเที่ยว</label>
                                        </div>
                                        <div class="custom-control custom-radio pb-5">
                                            <input class="custom-control-input custom-control-input-danger" type="radio" name="tvt_status" id="0" value="0" <?php if ($result["tvt_status"] != "1") echo "checked" ?>>
                                            <label for="0" class="custom-control-label">ไม่แสดงประเภทสถานที่ท่องเที่ยว</label>
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
                                    <a href="travel_type_show.php" class="btn btn-secondary me-2">
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
                    tvt_name: {
                        required: true,
                        maxlength: 50
                    },
                    adm_sataus: {
                        required: true,
                    },
                    adm_newImg: {
                        accept: "image/png,image/jpg,image/jpeg",
                    },
                },
                messages: {
                    tvt_name: {
                        required: "กรุณาระบุ ชื่อประเภทสถานที่",
                        maxlength: "ชื่อประเภทสถานที่ ต้องไม่เกิน 50 ตัวอักษร"
                    },
                    adm_sataus: {
                        required: "กรุณาระบุ สถานะการแสดงประเภทสถานที่",
                    },
                    adm_newImg: {
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
        document.getElementById('tvt_newImg').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2 MB in bytes

            if (file && allowedTypes.includes(file.type) && file.size <= maxSize) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('tvt_img').src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                // Reset the input
                event.target.value = '';
                // Reset to the original image
                document.getElementById('tvt_img').src = '../uploads/img_travel_type/<?php echo $result["tvt_img"]; ?>';

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