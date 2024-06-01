<?php
require_once("../db/connect.php");

$titlePage = "สถานที่ท่องเที่ยว";

// แสดงข้อมูล สถานที่ท่องเที่ยว ทั้งหมด
try {
    $sql = "SELECT pbr_travel.*, 
                pbr_travel_type.tvt_name, 
                SUM(pbr_travel_views.tvv_view) AS total_views
            FROM pbr_travel
            LEFT JOIN pbr_travel_type ON pbr_travel.tvt_id = pbr_travel_type.tvt_id
            LEFT JOIN pbr_travel_views ON pbr_travel.tv_id = pbr_travel_views.tv_id
            GROUP BY pbr_travel.tv_id, pbr_travel_type.tvt_name";
    $stmt = $conn->query($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // แสดงจำนวนประเภทสถานที่ท่องเที่ยว ที่อยู่ในประเภทนี้
    $sql = "SELECT COUNT(*) AS total_travelType 
            FROM pbr_travel_type";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    $totaltravelType = $total['total_travelType'];
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
                                    <?php if ($totaltravelType > 0) { ?>
                                        <a href="travel_add_form.php" class="btn btn-primary">
                                            <i class="fa-regular fa-square-plus"></i>
                                            <span>เพิ่มสถานที่ท่องเที่ยว</span>
                                        </a>

                                    <?php } else { ?>

                                        <p class="text-danger">*ต้องมีประเภทสถานที่ท่องเที่ยว อย่างน้อย 1 รายการ <a href="travel_type_add_form.php">เพิ่มประเภทสถานที่ท่องเที่ยวที่นี่</a></p>
                                        <button class="btn btn-primary" disabled>
                                            <i class="fa-regular fa-square-plus"></i>
                                            <span>เพิ่มสถานที่ท่องเที่ยว</span>
                                        </button>

                                    <?php } ?>

                                </div>
                                <!-- /.card-header -->

                                <div class="card-body">
                                    <?php if ($result) { ?>
                                        <table id="dataTable" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">รูปปก</th>
                                                    <th>ชื่อ</th>
                                                    <th>ยอดเข้าชม(ครั้ง)</th>
                                                    <th>ประเภทสถานที่ท่องเที่ยว</th>
                                                    <th>สถานะ</th>
                                                    <th>รายละเอียด</th>
                                                    <th>แก้ไข</th>
                                                    <th>ลบ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($result as $row) { ?>
                                                    <tr>
                                                        <td class="text-center"><img class="rounded border" style="width: 70px; height: 50px;" src="../uploads/img_travel/<?php echo $row["tv_cover"]; ?>"></td>
                                                        <td>
                                                            <?php
                                                            $tv_name = $row["tv_name"];
                                                            if (mb_strlen($tv_name, 'UTF-8') > 20) {
                                                                echo mb_substr($tv_name, 0, 20, 'UTF-8') . '...';
                                                            } else {
                                                                echo $tv_name;
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo number_format($row['total_views']) ?></td>
                                                        <td>
                                                            <?php
                                                            $tvt_name = $row["tvt_name"];
                                                            if (mb_strlen($tvt_name, 'UTF-8') > 15) {
                                                                echo mb_substr($tvt_name, 0, 15, 'UTF-8') . '...';
                                                            } else {
                                                                echo $tvt_name;
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($row["tv_status"] == 1) { ?>
                                                                <span class="badge rounded-pill text-bg-success">แสดง</span>
                                                            <?php } else { ?>
                                                                <span class="badge rounded-pill text-bg-danger">ไม่แสดง</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $originalId = $row["tv_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="travel_detail_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-info">
                                                                <i class="fa-solid fa-circle-info"></i>
                                                                <span>รายละเอียด</span>
                                                            </a>
                                                        </td>

                                                        <td>
                                                            <?php
                                                            $originalId = $row["tv_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="travel_edit_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-warning">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                                <span>แก้ไขข้อมูล</span>
                                                            </a>
                                                        </td>

                                                        <td>
                                                            <?php
                                                            $originalId = $row["tv_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="travel_del_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-danger">
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