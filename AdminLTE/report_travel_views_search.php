<?php
require_once("../db/connect.php");

$titlePage = "สถานที่ท่องเที่ยว";


try {
    if (isset($_GET["time_start"]) || isset($_GET["time_end"]) || isset($_GET["tv_name"]) || isset($_GET["tvt_name"])) {
        // มีการส่งข้อมูลคำค้นหา
        $time_start = isset($_GET["time_start"]) ? $_GET["time_start"] : null;
        $time_end = isset($_GET["time_end"]) ? $_GET["time_end"] : null;
        $tv_name = isset($_GET["tv_name"]) ? $_GET["tv_name"] : null;
        $tvt_name = isset($_GET["tvt_name"]) ? $_GET["tvt_name"] : null;

        // สร้างคำสั่ง SQL ตามเงื่อนไขการค้นหา
        $sql = "SELECT pbr_travel_views.tvv_id,
                        pbr_travel.tv_name,
                        pbr_travel_views.tvv_view,
                        pbr_travel_type.tvt_name,
                        pbr_travel_views.time
                FROM pbr_travel_views
                LEFT JOIN pbr_travel ON pbr_travel_views.tv_id = pbr_travel.tv_id
                LEFT JOIN pbr_travel_type ON pbr_travel_views.tvt_id = pbr_travel_type.tvt_id
                WHERE 1=1";

        if (!empty($time_start)) {
            $sql .= " AND pbr_travel_views.time >= :time_start";
        }
        if (!empty($time_end)) {
            $sql .= " AND pbr_travel_views.time <= :time_end";
        }
        if (!empty($tv_name)) {
            $sql .= " AND pbr_travel.tv_name LIKE :tv_name";
        }
        if (!empty($tvt_name)) {
            $sql .= " AND pbr_travel_type.tvt_name LIKE :tvt_name";
        }

        // เตรียมคำสั่ง SQL
        $stmt = $conn->prepare($sql);

        // ผูกค่าพารามิเตอร์
        if (!empty($time_start)) {
            $time_start = date('Y-m-d H:i:s', strtotime($time_start));
            $stmt->bindParam(':time_start', $time_start);
        }
        if (!empty($time_end)) {
            $time_end = date('Y-m-d H:i:s', strtotime($time_end));
            $stmt->bindParam(':time_end', $time_end);
        }
        if (!empty($tv_name)) {
            $tv_name = "%" . $tv_name . "%";
            $stmt->bindParam(':tv_name', $tv_name);
        }
        if (!empty($tvt_name)) {
            $tvt_name = "%" . $tvt_name . "%";
            $stmt->bindParam(':tvt_name', $tvt_name);
        }

        // สั่งให้ PDO ทำงาน
        $stmt->execute();

        // ดึงข้อมูล
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // นำไปใช้ในการแสดงผลต่อไป
    }else{
        header("Location: report_travel_views.php");
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header mt-1">
                                    <form action="report_travel_views.php" method="GET">
                                        <label for="" class="mb-3">ตั้งค่าการค้นหา</label>
                                        <div class="d-flex align-items-center">
                                            <div class="col-md-3">
                                                <label for="">เริ่ม</label>
                                                <input type="datetime-local" id="time_start" name="time_start" class="form-control mb-3">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="">สิ้นสุด</label>
                                                <input type="datetime-local" name="time_end" class="form-control mb-3">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="">ชื่อสถานที่ท่องเที่ยว</label>
                                                <input type="text" name="tv_name" class="form-control mb-3" placeholder="ระบุ ชื่อสถานที่ท่องเที่ยว">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="">ประเภทสถานที่ท่องเที่ยว</label>
                                                <input type="text" name="tvt_name" class="form-control mb-3" placeholder="ระบุ ประเภทสถานที่ท่องเที่ยว">
                                            </div>
                                        </div>
                                        <div class="">
                                            <button type="submit" class="btn btn-primary ms-2 me-2">ค้นหาข้อมูล</button>
                                            <a href="report_travel_views.php" class="btn btn-danger">เริ่มใหม่</a>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card-header -->

                                <div class="card-body">
                                    <?php if (!empty($time_start) || !empty($time_end) || !empty($tv_name) || !empty($tvt_name)) { ?>
                                        <label for="" class="mb-3">ผลการค้นหา</label>
                                        <div class="d-flex align-items-center">
                                        
                                            <?php if (!empty($time_start)) { ?>
                                                <div class="col-md-3">
                                                    <label for="">เริ่ม</label>
                                                    <p><?php echo $time_start ?></p>
                                                </div>
                                            <?php } ?>

                                            <?php if (!empty($time_end)) { ?>
                                                <div class="col-md-3">
                                                    <label for="">สิ้นสุด</label>
                                                    <p><?php echo $time_end ?></p>
                                                </div>
                                            <?php } ?>

                                            <?php if (!empty($tv_name)) { ?>
                                                <div class="col-md-3">
                                                    <label for="">ชื่อสถานที่ท่องเที่ยว</label>
                                                    <p><?php echo substr($tv_name, 1, -1); ?></p>
                                                </div>
                                            <?php } ?>

                                            <?php if (!empty($tvt_name)) { ?>
                                                <div class="col-md-3">
                                                    <label for="">ประเภทสถานที่ท่องเที่ยว</label>
                                                    <p><?php echo substr($tvt_name, 1, -1); ?></p>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                 
                                    <?php if ($result) { ?>
                                        <table id="dataTable" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">รหัสรายการ</th>
                                                    <th>วัน / เวลา</th>
                                                    <th>ชื่อสถานที่ท่องเที่ยว</th>
                                                    <th>ประเภทสถานที่ท่องเที่ยว</th>
                                                    <th>ยอดเข้าชม(ครั้ง)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- ตัวแปรสำหรับเก็บค่ารวมของ views -->
                                                <?php $totalViews = 0; ?>
                                                <?php foreach ($result as $row) { ?>
                                                    <!-- // เพิ่มค่าของ tvv_view ในแต่ละ row ไปที่ตัวแปร totalViews -->
                                                    <?php $totalViews += $row['tvv_view']; ?>
                                                    <tr>
                                                        <td><?php echo $row["tvv_id"]; ?></td>
                                                        <td><?php echo $row["time"]; ?></td>
                                                        <td><?php echo $row["tv_name"]; ?></td>
                                                        <td><?php echo $row["tvt_name"]; ?></td>
                                                        <td><?php echo number_format($row['tvv_view']) ?></td>

                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="4" class="text-end">จำนวนเข้าชมรวมทั้งหมด :</th>
                                                    <th><?php echo number_format($totalViews); ?></th>
                                                </tr>
                                            </tfoot>
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