<?php
require_once("../db/connect.php");

$titlePage = "แผนภูมิแสดงยอดเข้าชมข่าว";

// แสดงข้อมูล News ทั้งหมด
try {
    $sql = "SELECT pbr_news.ns_title, 
                SUM(pbr_news_views.nsv_view) AS total_newsViews
            FROM pbr_news
            LEFT JOIN pbr_news_views ON pbr_news.ns_id = pbr_news_views.ns_id
            GROUP BY pbr_news.ns_id
            HAVING total_newsViews != 0";
    $stmt = $conn->query($sql);
    $stmt->execute();
    $resultNews = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $sql = "SELECT pbr_news_type.nst_name,
                SUM(pbr_news_views.nsv_view) AS total_newsTypeViews
            FROM pbr_news_type
            LEFT JOIN pbr_news_views ON pbr_news_type.nst_id = pbr_news_views.nst_id
            GROUP BY pbr_news_type.nst_id
            HAVING total_newsTypeViews != 0";
    $stmt = $conn->query($sql);
    $stmt->execute();
    $resultNewsType = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                                    <h4>ยอดเข้าชมแบ่งตาม หัวข้อข่าว (ครั้ง)</h4>
                                </div>
                                <div class="card-body">

                                    <?php if ($resultNews) { ?>
                                        <div class="d-flex justify-content-center align-items-center" style="height: 500px;">
                                            <canvas id="chartNewsViews"></canvas>
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
                                    <h4>ยอดเข้าชมแบ่งตาม ประเภทข่าว (ครั้ง)</h4>
                                </div>

                                <?php if ($resultNewsType) { ?>
                                    <div class="d-flex justify-content-center align-items-center" style="height: 500px;">
                                        <canvas id="chartNewsTypeViews"></canvas>
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
        // เรียกใช้ตัวแปร ctx ที่กำหนดให้ตรงกับ canvas
        const ctx = document.getElementById('chartNewsViews').getContext('2d');

        // สร้าง arrays เพื่อเก็บข้อมูลสำหรับกราฟ
        const newsLabels = []; // เก็บชื่อข่าว
        const newsViews = []; // เก็บจำนวนการเข้าชม

        // วน loop ผ่านข้อมูลที่ได้มาจาก PHP
        <?php foreach ($resultNews as $index => $row) : ?>
            newsLabels.push("<?php echo addslashes($row['ns_title']); ?>"); // เพิ่มชื่อข่าวลงใน array
            newsViews.push(<?php echo $row['total_newsViews']; ?>); // เพิ่มจำนวนการเข้าชมลงใน array
        <?php endforeach; ?>

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: newsLabels, // ชื่อข่าว
                datasets: [{
                    label: 'ยอดเข้าชม (ครั้ง)',
                    data: newsViews, // จำนวนการเข้าชม
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
                        beginAtZero: true
                    }
                }
            }
        });
    </script>



    <script>
        // เรียกใช้ตัวแปร ctx ที่กำหนดให้ตรงกับ canvas 
        const ctx2 = document.getElementById('chartNewsTypeViews').getContext('2d');

        // สร้าง arrays เพื่อเก็บข้อมูลสำหรับกราฟ
        const newsTypeLabels = []; // เก็บประเภทข่าว
        const newsTypeViews = []; // เก็บจำนวนการเข้าชม

        // วน loop ผ่านข้อมูลที่ได้มาจาก PHP
        <?php foreach ($resultNewsType as $index => $row) { ?>
            newsTypeLabels.push("<?php echo $row['nst_name']; ?>"); // เพิ่มประเภทข่าวลงใน array
            newsTypeViews.push(<?php echo $row['total_newsTypeViews']; ?>); // เพิ่มจำนวนการเข้าชมลงใน array
        <?php } ?>

        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: newsTypeLabels, // ประเภทข่าว
                datasets: [{
                    label: 'ยอดเข้าชม (ครั้ง)',
                    data: newsViews, // จำนวนการเข้าชม
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