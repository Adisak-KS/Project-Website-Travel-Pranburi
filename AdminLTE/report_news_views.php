<?php
require_once("../db/connect.php");

$titlePage = "รายงานยอดเข้าชมข่าว";


try {
    if (isset($_GET["time_start"]) || isset($_GET["time_end"]) || isset($_GET["ns_name"]) || isset($_GET["nst_name"])) {
        // มีการส่งข้อมูลคำค้นหา
        // มีการส่งข้อมูลคำค้นหา
        $time_start = isset($_GET["time_start"]) ? $_GET["time_start"] : null;
        $time_end = isset($_GET["time_end"]) ? $_GET["time_end"] : null;
        $ns_title = isset($_GET["ns_title"]) ? $_GET["ns_title"] : null;
        $nst_name = isset($_GET["nst_name"]) ? $_GET["nst_name"] : null;

        // สร้างคำสั่ง SQL ตามเงื่อนไขการค้นหา
        $sql = "SELECT pbr_news_views.nsv_id,
                        pbr_news.ns_title,
                        pbr_news_views.nsv_view,
                        pbr_news_type.nst_name,
                        pbr_news_views.time
                FROM pbr_news_views
                LEFT JOIN pbr_news ON pbr_news_views.ns_id = pbr_news.ns_id
                LEFT JOIN pbr_news_type ON pbr_news_views.nst_id = pbr_news_type.nst_id
                WHERE 1=1";

        if (!empty($time_start)) {
            $sql .= " AND pbr_news_views.time >= :time_start";
        }
        if (!empty($time_end)) {
            $sql .= " AND pbr_news_views.time <= :time_end";
        }
        if (!empty($ns_title)) {
            $sql .= " AND pbr_news.ns_title LIKE :ns_title";
        }
        if (!empty($nst_name)) {
            $sql .= " AND pbr_news_type.nst_name LIKE :nst_name";
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
        if (!empty($ns_title)) {
            $ns_title = "%" . $ns_title . "%";
            $stmt->bindParam(':ns_title', $ns_title);
        }
        if (!empty($nst_name)) {
            $nst_name = "%" . $nst_name . "%";
            $stmt->bindParam(':nst_name', $nst_name);
        }

        // สั่งให้ PDO ทำงาน
        $stmt->execute();

        // ดึงข้อมูล
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // นำไปใช้ในการแสดงผลต่อไป
    } else {
        // แสดงข้อมูล สถานที่ท่องเที่ยว ทั้งหมด
        $sql = "SELECT  pbr_news_views.nsv_id,
                        pbr_news.ns_title,
                        pbr_news_views.nsv_view,
                        pbr_news_type.nst_name,
                        pbr_news_views.time
                FROM pbr_news_views
                LEFT JOIN pbr_news ON pbr_news_views.ns_id = pbr_news.ns_id
                LEFT JOIN pbr_news_type ON pbr_news_views.nst_id = pbr_news_type.nst_id";
        $stmt = $conn->query($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                                    <form id="form" action="report_news_views.php" method="GET">
                                        <label for="" class="mb-3">ตั้งค่าการค้นหา</label>
                                        <div class="d-flex">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">วันเริ่ม</label>
                                                    <input type="datetime-local" name="time_start" id="time_start" value="<?php echo $time_start; ?>" class="form-control mb-3">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">วันสิ้นสุด</label>
                                                    <input type="datetime-local" name="time_end" value="<?php echo $time_end; ?>" class="form-control mb-3">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">หัวข้อข่าว</label>
                                                    <?php if (empty($ns_title)) { ?>
                                                        <input type="text" name="ns_title" class="form-control mb-3" placeholder="ระบุ ประเภทข่าว">
                                                    <?php } else { ?>
                                                        <input type="text" name="ns_title" value="<?php echo substr($ns_title, 1, -1); ?>" class="form-control mb-3" placeholder="ระบุ ชื่อข่าว">
                                                    <?php } ?>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">ประเภทข่าว</label>
                                                    <?php if (empty($nst_name)) { ?>
                                                        <input type="text" name="nst_name" class="form-control mb-3" placeholder="ระบุ ประเภทข่าว">
                                                    <?php } else { ?>
                                                        <input type="text" name="nst_name" value="<?php echo substr($nst_name, 1, -1); ?>" class="form-control mb-3" placeholder="ระบุ ประเภทข่าว">
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="">
                                            <button type="submit" class="btn btn-primary ms-2 me-2">ค้นหาข้อมูล</button>
                                            <a href="report_news_views.php" class="btn btn-danger">เริ่มใหม่</a>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card-header -->

                                <div class="card-body">
                                    <?php if (!empty($time_start) || !empty($time_end) || !empty($ns_title) || !empty($nst_name)) { ?>
                                        <label for="" class="mb-3">ผลการค้นหา</label>
                                        <div class="d-flex">

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

                                            <?php if (!empty($ns_title)) { ?>
                                                <div class="col-md-3">
                                                    <label for="">หัวข้อข่าว</label>
                                                    <p><?php echo substr($ns_title, 1, -1); ?></p>
                                                </div>
                                            <?php } ?>

                                            <?php if (!empty($nst_name)) { ?>
                                                <div class="col-md-3">
                                                    <label for="">ประเภทข่าว</label>
                                                    <p><?php echo substr($nst_name, 1, -1); ?></p>
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
                                                    <!-- // เพิ่มค่าของ nsv_view ในแต่ละ row ไปที่ตัวแปร totalViews -->
                                                    <?php $totalViews += $row['nsv_view']; ?>
                                                    <tr>
                                                        <td><?php echo $row["nsv_id"]; ?></td>
                                                        <td><?php echo $row["time"]; ?></td>
                                                        <td><?php echo $row["ns_title"]; ?></td>
                                                        <td><?php echo $row["nst_name"]; ?></td>
                                                        <td><?php echo number_format($row['nsv_view']) ?></td>

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

    <!-- Data Table  -->
    <script>
        new DataTable('#dataTable', {
            layout: {
                topStart: {
                    buttons: [{
                            extend: 'copy',
                            // text: 'คัดลอกข้อมูล', // ข้อความที่แสดงบนปุ่ม
                            className: 'rounded bg-info border-0', // คลาส CSS สำหรับปุ่ม
                        },
                        {
                            extend: 'csv',
                            // text: 'ส่งออกเป็น CSV',
                            className: 'rounded bg-warning border-0'
                        },
                        {
                            extend: 'excel',
                            // text: 'ส่งออกเป็น Excel',
                            className: 'rounded bg-success border-0'
                        },
                        {
                            extend: 'print',
                            text: 'พิมพ์',
                            className: 'rounded bg-secondary border-0'
                        }
                    ]
                }
            },
            responsive: true,
            encoding: 'UTF-8' // ระบุ encoding เป็น UTF-8
        });
    </script>

    <!-- validation form  -->
    <script>
        $(function() {
            // Custom validation method
            $.validator.addMethod("greaterThanOrEqualTo", function(value, element, params) {
                var startTime = $(params).val();
                return this.optional(element) || value >= startTime;
            }, "End time must be greater than or equal to start time.");


            $('#form').validate({
                rules: {
                    time_end: {
                        greaterThanOrEqualTo: "#time_start"
                    }
                },
                messages: {
                    time_end: {
                        greaterThanOrEqualTo: "วันสิ้นสุด ต้องมากกว่าหรือเท่ากับ วันเริ่ม"
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
</body>

</html>
<?php require_once("../include/sweetalert2.php") ?>