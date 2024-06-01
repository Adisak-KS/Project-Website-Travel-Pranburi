<?php
require_once("../db/connect.php");

$titlePage = "ผู้ดูแลระบบ";

// แสดงข้อมูล Admin ทั้งหมด
try {
    $admId = $_SESSION["adm_id"];

    $sql = "SELECT * 
            FROM pbr_admin
         WHERE adm_id != :adm_id AND adm_id != 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":adm_id",$admId);
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
                                    <a href="admin_add_form.php" class="btn btn-primary">
                                        <i class="fa-regular fa-square-plus"></i>
                                        <span>เพิ่มผู้ดูแลระบบ</span>
                                    </a>
                                </div>
                                <!-- /.card-header -->
                                <?php if ($result) { ?>
                                    <!-- <?php //echo var_dump($result); 
                                            ?> -->

                                    <div class="card-body">
                                        <table id="dataTable" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">รูป</th>
                                                    <th>ชื่อ</th>
                                                    <th>นามสกุล</th>
                                                    <th>ชื่อผู้ใช้</th>
                                                    <th>อีเมล</th>
                                                    <th>สถานะ</th>
                                                    <th>รายละเอียด</th>
                                                    <th>แก้ไข</th>
                                                    <?php if ($_SESSION["adm_id"] == "1") { ?>
                                                    <th>ลบ</th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($result as $row) { ?>
                                                    <tr>
                                                        <td class="text-center"><img class="rounded-circle border" style="width: 50px; height: 50px;" src="../uploads/profile_admin/<?php echo $row["adm_profile"]; ?>"></td>
                                                        <td><?php echo $row["adm_fname"]; ?></td>
                                                        <td><?php echo $row["adm_lname"]; ?></td>
                                                        <td><?php echo $row["adm_username"]; ?></td>
                                                        <td><?php echo $row["adm_email"]; ?></td>
                                                        <td>
                                                            <?php if ($row["adm_status"] == 1) { ?>
                                                                <span class="badge rounded-pill text-bg-success">ใช้งานได้</span>
                                                            <?php } else { ?>
                                                                <span class="badge rounded-pill text-bg-danger">ระงับการใช้งาน</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $originalId = $row["adm_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="admin_detail_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-info">
                                                                <i class="fa-solid fa-circle-info"></i>
                                                                <span>รายละเอียด</span>
                                                            </a>
                                                        </td>

                                                        <td>
                                                            <?php
                                                            $originalId = $row["adm_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="admin_edit_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-warning">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                                <span>แก้ไขข้อมูล</span>
                                                            </a>
                                                        </td>

                                                    <?php if($_SESSION["adm_id"] == "1") { ?>
                                                        <td>
                                                            <?php
                                                            $originalId = $row["adm_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="admin_del_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-danger">
                                                                <i class="fa-solid fa-trash"></i>
                                                                <span>ลบข้อมูล</span>
                                                            </a>
                                                        </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } else { ?>
                                    <?php require_once("include/no_information.php") ?>
                                <?php } ?>
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