<?php
require_once("../db/connect.php");

$titlePage = "ประเภทสถานที่ท่องเที่ยว";

// แสดงข้อมูล Admin ทั้งหมด
try {
    $sql = "SELECT * FROM pbr_travel_type";
    $stmt = $conn->query($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <a href="travel_type_add_form.php" class="btn btn-primary">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>เพิ่มประเภทสถานที่ท่องเที่ยว</span>
                                    </a>
                                </div>
                                <!-- /.card-header -->

                                <div class="card-body">
                                    <?php if ($result) { ?>
                                        <table id="dataTable" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">รูป</th>
                                                    <th>ชื่อประเภท</th>
                                                    <th>สถานะ</th>
                                                    <th>รายละเอียด</th>
                                                    <th>แก้ไข</th>
                                                    <th>ลบ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($result as $row) { ?>
                                                    <tr>
                                                        <td class="text-center"><img class="rounded border" style="width: 70px; height: 50px;" src="../uploads/img_travel_type/<?php echo $row["tvt_img"]; ?>"></td>
                                                        <td>
                                                            <?php
                                                            $tvt_name = $row["tvt_name"];
                                                            if (mb_strlen($tvt_name, 'UTF-8') > 50) {
                                                                echo mb_substr($tvt_name, 0, 50, 'UTF-8') . '...';
                                                            } else {
                                                                echo $tvt_name;
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($row["tvt_status"] == 1) { ?>
                                                                <span class="badge rounded-pill text-bg-success">แสดง</span>
                                                            <?php } else { ?>
                                                                <span class="badge rounded-pill text-bg-danger">ไม่แสดง</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $originalId = $row["tvt_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="travel_type_detail_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-info">
                                                                <i class="fa-solid fa-circle-info"></i>
                                                                <span>รายละเอียด</span>
                                                            </a>
                                                        </td>

                                                        <td>
                                                            <?php
                                                            $originalId = $row["tvt_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="travel_type_edit_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-warning">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                                <span>แก้ไขข้อมูล</span>
                                                            </a>
                                                        </td>

                                                        <td>
                                                            <?php
                                                            $originalId = $row["tvt_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="travel_type_del_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-danger">
                                                                <i class="fa-solid fa-trash"></i>
                                                                <span>ลบข้อมูล</span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    <?php } else { ?>
                                        <?php require_once("include/no_information.php") ?>
                                    <?php } ?>
                                </div>

                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->
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
        new DataTable('#dataTable', {
            responsive: true
        });
    </script>
</body>

</html>
<?php require_once("../include/sweetalert2.php") ?>