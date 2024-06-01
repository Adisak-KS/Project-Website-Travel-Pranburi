<?php
require_once("../db/connect.php");

$titlePage = "ข่าว / ประชาสัมพันธ์";

// แสดงข้อมูล Admin ทั้งหมด
try {
    $sql = "SELECT pbr_news.*, 
                pbr_news_type.nst_name, 
                SUM(pbr_news_views.nsv_view) AS total_views
            FROM pbr_news
            LEFT JOIN pbr_news_type ON pbr_news.nst_id = pbr_news_type.nst_id
            LEFT JOIN pbr_news_views ON pbr_news.ns_id = pbr_news_views.ns_id
            GROUP BY pbr_news.ns_id, pbr_news_type.nst_name";
    $stmt = $conn->prepare($sql);
    $stmt = $conn->query($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // แสดงจำนวนประเภทข่าว ที่อยู่ในประเภทนี้
    $sql = "SELECT COUNT(*) AS total_newsType 
            FROM pbr_news_type";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalNewsType = $total['total_newsType'];
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
                                    <?php if ($totalNewsType > 0) { ?>
                                        <a href="news_add_form.php" class="btn btn-primary">
                                            <i class="fa-regular fa-square-plus"></i>
                                            <span>เพิ่มข่าว / ประชาสัมพันธ์</span>
                                        </a>
                                    <?php } else { ?>
                                        <p class="text-danger">*ต้องมีประเภทข่าวอย่างน้อย 1 รายการ <a href="news_type_add_form.php">เพิ่มประเภทข่าวที่นี่</a></p>
                                        <button class="btn btn-primary" disabled>
                                            <i class="fa-regular fa-square-plus"></i>
                                            <span>เพิ่มข่าว / ประชาสัมพันธ์</span>
                                        </button>
                                    <?php } ?>
                                </div>
                                <!-- /.card-header -->

                                <div class="card-body">
                                    <?php if (count($result) > 0) { ?>
                                        <table id="dataTable" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">รูปปก</th>
                                                    <th>หัวข้อ</th>
                                                    <th>ยอดเข้าชม (ครั้ง)</th>
                                                    <th>ประเภทข่าว</th>
                                                    <th>สถานะ</th>
                                                    <th>รายละเอียด</th>
                                                    <th>แก้ไข</th>
                                                    <th>ลบ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($result as $row) { ?>
                                                    <tr>
                                                        <td class="text-center"><img class="rounded border" style="width: 70px; height: 50px;" src="../uploads/img_news/<?php echo $row["ns_cover"]; ?>"></td>
                                                        <td>
                                                            <?php
                                                            $ns_title = $row["ns_title"];
                                                            if (mb_strlen($ns_title, 'UTF-8') > 20) {
                                                                echo mb_substr($ns_title, 0, 20, 'UTF-8') . '...';
                                                            } else {
                                                                echo $ns_title;
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo number_format($row['total_views']) ?></td>
                                                        <td><?php echo $row["nst_name"]; ?></td>
                                                        <td>
                                                            <?php if ($row["ns_status"] == 1) { ?>
                                                                <span class="badge rounded-pill text-bg-success">แสดง</span>
                                                            <?php } else { ?>
                                                                <span class="badge rounded-pill text-bg-danger">ไม่แสดง</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $originalId = $row["ns_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="news_detail_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-info">
                                                                <i class="fa-solid fa-circle-info"></i>
                                                                <span>รายละเอียด</span>
                                                            </a>
                                                        </td>

                                                        <td>
                                                            <?php
                                                            $originalId = $row["ns_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="news_edit_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-warning">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                                <span>แก้ไขข้อมูล</span>
                                                            </a>
                                                        </td>

                                                        <td>
                                                            <?php
                                                            $originalId = $row["ns_id"];
                                                            require_once("include/salt.php");   // รหัส Salte 
                                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                            ?>

                                                            <a href="news_del_form.php?id=<?php echo $base64Encoded ?>" class="btn btn-danger">
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