<?php
require_once("../db/connect.php");

$titlePage = "ตั้งค่าเว็บไซต์";

// แสดงข้อมูล สถานที่ท่องเที่ยว ทั้งหมด
try {
    $sql = "SELECT *
            FROM pbr_setting";
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

                                </div>
                                <!-- /.card-header -->

                                <div class="card-body">
                                    <?php if ($result) { ?>
                                        <table id="dataTable" class="table table-bordered table-hover">
                                            <thead>
                                                <th>#</th>
                                                <th>รายการ</th>
                                                <th>การกำหนดข้อมูล</th>
                                                <th>รายละเอียด</th>
                                                <th>แก้ไขข้อมูล</th>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($result as $row) { ?>
                                                    <tr>
                                                        <td><?php echo $row["st_id"] ?></td>
                                                        <td><?php echo $row["st_list"] ?></td>
                                                        <td>
                                                            <?php if (empty($row["st_detail"])) { ?>
                                                                <span class="badge rounded-pill text-bg-danger">ไม่ได้กำหนด</span>
                                                            <?php } else { ?>
                                                                <span class="badge rounded-pill text-bg-success">กำหนดแล้ว</span>
                                                            <?php  } ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $originalId = $row["st_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="setting_detail_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-info">
                                                                <i class="fa-solid fa-circle-info"></i>
                                                                <span>รายละเอียด</span>
                                                            </a>
                                                        </td>

                                                        <td>
                                                            <?php
                                                            $originalId = $row["st_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="setting_edit_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-warning">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                                <span>แก้ไขข้อมูล</span>
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