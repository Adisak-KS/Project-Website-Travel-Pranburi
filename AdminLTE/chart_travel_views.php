<?php
require_once("../db/connect.php");

$titlePage = "แผนภูมิแสดงยอดเข้าชมสถานที่ท่องเที่ยว";

// แสดงข้อมูล Travel ทั้งหมด
try {
    $sql = "SELECT pbr_travel.tv_name, 
                SUM(pbr_travel_views.tvv_view) AS total_travelViews
            FROM pbr_travel
            LEFT JOIN pbr_travel_views ON pbr_travel.tv_id = pbr_travel_views.tv_id
            GROUP BY pbr_travel.tv_id
            HAVING total_travelViews != 0";
    $stmt = $conn->query($sql);
    $stmt->execute();
    $resultTravel = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $sql = "SELECT pbr_travel_type.tvt_name,
                SUM(pbr_travel_views.tvv_view) AS total_travelTypeViews
            FROM pbr_travel_type
            LEFT JOIN pbr_travel_views ON pbr_travel_type.tvt_id = pbr_travel_views.tvt_id
            GROUP BY pbr_travel_type.tvt_id
            HAVING total_travelTypeViews != 0";
    $stmt = $conn->query($sql);
    $stmt->execute();
    $resultTravelType = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                                <div class="card-header text-center mt-2">
                                    <h4>ยอดเข้าชมแบ่งตาม ชื่อสถานที่ท่องเที่ยว (ครั้ง)</h4>
                                </div>
                                <div class="card-body">

                                    <?php if ($resultTravel) { ?>
                                        <div class="d-flex justify-content-center align-items-center" style="height: 500px;">
                                            <canvas id="chartTravelViews"></canvas>
                                        </div>
                                    <?php } else { ?>
                                        <div class="alert alert-light text-center" role="alert">
                                            <h1 class="pt-2">ไม่พบข้อมูล</h1>
                                            <i class="fa-solid fa-face-tired fa-10x text-warning py-2"></i>
                                            <hr class="border border-white">
                                            <p>!! กรุณาเพิ่มข้อมูล !!</p>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header text-center mt-2">
                                    <h4>ยอดเข้าชมแบ่งตาม ประเภทสถานที่ท่องเที่ยว (ครั้ง)</h4>
                                </div>

                                <?php if ($resultTravel) { ?>
                                    <div class="d-flex justify-content-center align-items-center" style="height: 500px;">
                                        <canvas id="chartTravelTypeViews"></canvas>
                                    </div>
                                <?php } else { ?>
                                    <div class="alert alert-light text-center" role="alert">
                                        <h1 class="pt-2">ไม่พบข้อมูล</h1>
                                        <i class="fa-solid fa-face-tired fa-10x text-warning py-2"></i>
                                        <hr class="border border-white">
                                        <p>!! กรุณาเพิ่มข้อมูล !!</p>
                                    </div>
                                <?php } ?>

                            </div>
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
        // เรียกใช้ตัวแปร ctx ที่กำหนดให้ตรงกับ canvas id 'myChart'
        const ctx = document.getElementById('chartTravelViews').getContext('2d');

        // สร้าง arrays เพื่อเก็บข้อมูลสำหรับกราฟ
        const travelLabels = []; // เก็บชื่อของการเดินทาง
        const travelViews = []; // เก็บจำนวนการเข้าชม

        // วน loop ผ่านข้อมูลที่ได้มาจาก PHP
        <?php foreach ($resultTravel as $index => $row) : ?>
            travelLabels.push("<?php echo $row['tv_name']; ?>"); // เพิ่มชื่อของการเดินทางลงใน array
            travelViews.push(<?php echo $row['total_travelViews']; ?>); // เพิ่มจำนวนการเข้าชมลงใน array
        <?php endforeach; ?>

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: travelLabels, // ชื่อของการเดินทาง
                datasets: [{
                    label: 'ยอดเข้าชม (ครั้ง)',
                    data: travelViews, // จำนวนการเข้าชม
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(201, 203, 207, 0.2)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(75, 192, 192)',
                        'rgb(255, 205, 86)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            }
        });
    </script>

    <script>
        // เรียกใช้ตัวแปร ctx ที่กำหนดให้ตรงกับ canvas id 'myChart'
        const ctx2 = document.getElementById('chartTravelTypeViews').getContext('2d');

        // สร้าง arrays เพื่อเก็บข้อมูลสำหรับกราฟ
        const travelTypeLabels = []; // เก็บประเภทสถานที่
        const travelTypeViews = []; // เก็บจำนวนการเข้าชม

        // วน loop ผ่านข้อมูลที่ได้มาจาก PHP
        <?php foreach ($resultTravelType as $index => $row) { ?>
            travelTypeLabels.push("<?php echo $row['tvt_name']; ?>"); // เพิ่มประเภทสถานที่ลงใน array
            travelTypeViews.push(<?php echo $row['total_travelTypeViews']; ?>); // เพิ่มจำนวนการเข้าชมลงใน array
        <?php } ?>

        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: travelTypeLabels, // ชื่อของการเดินทาง
                datasets: [{
                    label: 'ยอดเข้าชม (ครั้ง)',
                    data: travelViews, // จำนวนการเข้าชม
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(75, 192, 192)',
                        'rgb(255, 205, 86)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                    hoverOffset: 4
                }]
            },
        });
    </script>

</body>

</html>
<?php require_once("../include/sweetalert2.php") ?>